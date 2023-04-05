<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Modules\ProvBase\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Modules\ProvBase\Entities\Address;
use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Modem;
use Symfony\Component\Console\Output\OutputInterface;

class GeocodeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:geocode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find geocodes (x,y) for all modems. Accepts number of modems to process as argument (default 250). Add “-vvv” for debug output. You can set chunk size by “--chunk_size”. Use “--rebuild” to rebuild Geocodes';

    /**
     * The signature (defining the optional argument)
     */
    protected $signature = 'nms:geocode {--modem_count=0} {--contract_count=0} {--chunk_size=100} {--rebuild}';

    // helpers filled from CLI args/opts
    protected $modemCount = 0;
    protected $contractCount = 0;
    protected $chunkSize = 100;
    protected bool $rebuild = false;
    protected $logs = [
        'error' => [],
        'info' => [],
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * This method takes care for usage policies of OSM/google (meaning that this has not to be handled in Modem::geocode()
     *
     * @return mixed
     *
     * @author Torsten Schmidt, Patrick Reichel, Farshid Ghiasimanesh
     */
    public function handle()
    {
        $this->getParams();

        if ($this->rebuild) {
            $this->handleModems();
            $this->handleContracts();

            return;
        }

        if ($this->modemCount) {
            $this->handleModems();
        }

        if ($this->contractCount) {
            $this->handleContracts();
        }
    }

    /**
     * Get the console command arguments and options.
     *
     * @author Patrick Reichel
     */
    protected function getParams()
    {
        // get number of modems and contracts to geocode
        $modemCount = intval($this->option('modem_count'));
        $contractCount = intval($this->option('contract_count'));
        $chunkSize = intval($this->option('chunk_size'));

        if ($this->option('rebuild')) {
            $this->rebuild = true;
        }

        if ($chunkSize < 1) {
            echo "Error: Pass integer >0 as argument for chunk or leave it for $this->chunkSize";

            exit(1);
        }

        if ($modemCount < 1 && $contractCount < 1 && ! $this->rebuild) {
            echo 'Error: Pass integer >0 as argument for modem/contract or Pass --rebuild option';

            exit(1);
        }

        $this->modemCount = $modemCount;
        $this->contractCount = $contractCount;
        $this->chunkSize = $chunkSize;
    }

    protected function handleModems()
    {
        $this->handleGeocodes(Modem::class, $this->modemCount);
    }

    protected function handleContracts()
    {
        $this->handleGeocodes(Contract::class, $this->contractCount);
    }

    private function geocodeQuery($model, int $count): Builder|EloquentBuilder
    {
        $query = $model::whereNull('geocode_source')
            ->orWhere('geocode_source', '')
            ->orWhere('geocode_source', 'like', 'n/a%')
            ->orderBy('geocode_source')
            ->orderBy('id');

        return $count && ! $this->rebuild ? $query->limit($count) : $query;
    }

    private function handleGeocodes($model, int $count): bool
    {
        $modelName = class_basename($model);
        $total = $this->geocodeQuery($model, $count)->select('id')->get()->count();
        $numChunks = ceil($total / $this->chunkSize);

        if (! $total) {
            $this->info("Nothing to geocode for $modelName", OutputInterface::VERBOSITY_DEBUG);

            return false;
        }

        // fetch addresses
        $addresses = Address::get()->keyBy(function ($address) {
            return $address->district.'_'.$address->zip.'_'.$address->city.'_'.$address->street.'_'.$address->house_number;
        });

        $this->info("Trying to geocode $total $modelName(s)", OutputInterface::VERBOSITY_DEBUG);

        // get all models with missing or uncertain geodata
        // ordering puts models with uncertain data first (correct wrong entries)
        // second are all models where geocode failed before
        $this->geocodeQuery($model, $count)->chunk($this->chunkSize, function ($models, $chunkIndex) use ($numChunks, &$count, $modelName, $addresses) {
            echo "\n";

            if ($count == 0 && ! $this->rebuild) {
                $this->printLogs();
                $this->info("Number of $modelName reached", OutputInterface::VERBOSITY_DEBUG);

                return false;
            }

            $this->info("$modelName: Processing chunk $chunkIndex/$numChunks", OutputInterface::VERBOSITY_DEBUG);

            $progressBarCount = $count && ! $this->rebuild ? min([count($models), $count]) : count($models);
            $progressBar = $this->output->createProgressBar($progressBarCount);
            $progressBar->start();

            foreach ($models as $model) {
                if ($count == 0 && ! $this->rebuild) {
                    $this->printLogs();
                    $this->info("Number of $modelName reached", OutputInterface::VERBOSITY_DEBUG);

                    return false;
                }

                ob_start();
                $ret = $model->geocode(true, $addresses[$model->district.'_'.$model->zip.'_'.$model->city.'_'.$model->street.'_'.$model->house_number] ?? null);
                ob_end_clean();

                if ($this->getOutput()->isDebug()) {
                    $info = 'id:'.$model->id.', '.$model->district.', '.$model->zip.', '.$model->city.', '.$model->street.' '.$model->house_number;

                    if ($ret) {
                        $this->logs['info'][] = 'success: '.implode(', ', $ret).', '.$info;
                    } else {
                        $this->logs['error'][] = $model->geocodeLastStatus().': error, could not translate, '.$info.' - ';
                    }
                }

                $progressBar->advance();

                $count--;

                if ($ret && $ret['source'] == 'OSM Nominatim') {
                    // sleep between 1.2 and 1.5 seconds (Nominatim allows one request per second)
                    $sleeptime = rand(1200000, 1500000);
                    usleep($sleeptime);
                }
            }

            $progressBar->finish();

            $this->printLogs();
        });

        echo "\n";

        return true;
    }

    private function printLogs(): void
    {
        echo "\n";

        foreach ($this->logs['error'] as $log) {
            echo "\n";

            $this->error($log, OutputInterface::VERBOSITY_DEBUG);
        }

        foreach ($this->logs['info'] as $log) {
            echo "\n";

            $this->info($log, OutputInterface::VERBOSITY_DEBUG);
        }

        // reset logs
        foreach ($this->logs as $k => $log) {
            $this->logs[$k] = [];
        }

        echo "\n";
    }
}
