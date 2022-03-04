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
use Modules\ProvBase\Entities\Contract;
use Symfony\Component\Console\Input\InputArgument;

class ContractCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Contract Scheduling Command (call with daily, daily_all or monthly)';

    /**
     * Global counter
     *
     * @var int
     */
    protected $i;

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
     * @return mixed
     */
    public function handle()
    {
        // we don't check contracts that ended before defined here (in days)
        $days_around_start_and_enddates = 7;

        $min_date = date('Y-m-d', strtotime("-$days_around_start_and_enddates days"));
        $max_date = date('Y-m-d', strtotime("+$days_around_start_and_enddates days"));
        $today = date('Y-m-d');

        if (! in_array($this->argument('date'), ['daily', 'daily_all', 'monthly'])) {
            $this->error('Wrong/missing argument');

            return false;
        }

        \Log::debug('Run '.$this->argument('date').' conversion');

        // Fallback mode – runs conversions on every contract
        $contractsQuery = Contract::query();

        if (! \Str::endswith($this->argument('date'), '_all')) {
            // shrink the list of contracts to run daily conversion on – this is an expensive operation
            if (\Module::collections()->has('BillingBase')) {
                // attention: do not check if valid_to or valid_from is fixed – we need them both
                //      (change item dates, trigger online state of modems)
                $contractsQuery = Contract::where(whereLaterOrEqual('contract_end', $min_date))
                    ->where('contract_start', '<=', $today)
                    ->join('item', 'contract.id', '=', 'item.contract_id')
                    ->join('product', 'product.id', '=', 'item.product_id')
                    ->whereIn('product.type', ['Internet', 'Voip'])
                    ->where(function ($query) use ($min_date, $max_date) {
                        // using advanced where clause to set brackets properly
                        $query
                            ->whereBetween('item.valid_from', [$min_date, $max_date])
                            ->orWhereBetween('item.valid_to', [$min_date, $max_date])
                            ->orWhereBetween('contract.contract_start', [$min_date, $max_date])
                            ->orWhereBetween('contract.contract_end', [$min_date, $max_date]);
                    })
                    ->groupBy('contract.id')
                    ->select('contract.*');
            } else {
                if ($this->argument('date') == 'daily') {
                    /*  (1) Contract begins today or began in last days and internet_access = 0
                        (2) Contract ended in last days and internet_access = 1
                    */
                    $contractsQuery = Contract::where(function ($query) use ($min_date) {
                        $query
                        ->where('internet_access', 0)
                        ->where('contract_start', '<=', date('Y-m-d'))
                        ->where('contract_start', '>', $min_date);
                    })
                    ->orWhere(function ($query) use ($min_date) {
                        $query
                        ->where('internet_access', 1)
                        ->whereNotNull('contract_end')
                        ->where('contract_end', '<', date('Y-m-d'))
                        ->where('contract_end', '>=', $min_date);
                    });
                } else {
                    /* Contract must be valid and
                        (3) Qos-id needs to be changed
                        (4) Voip-id needs to be changed
                    */
                    $contractsQuery = Contract::where('contract_start', '<=', date('Y-m-d'))
                        ->where(whereLaterOrEqual('contract_end', date('Y-m-d', strtotime('+1 day'))))
                        ->where(function ($query) {
                            $query
                            ->whereNotNull('next_qos_id')
                            ->orWhereNotNull('next_voip_id');
                        })
                        ->where(function ($query) {
                            $query
                            ->where('qos_id', '!=', 'next_qos_id')
                            ->orWhere('voip_id', '!=', 'next_voip_id');
                        });
                }
            }
        }

        $this->i = 1;
        // Count on groupBy delivers unexpected results - See https://github.com/laravel/framework/issues/28931
        // TODO: Laravel 7
        $num = (clone $contractsQuery)->pluck('id')->count();

        $contractsQuery->chunk(1000, function ($contracts) use ($num) {
            foreach ($contracts as $c) {
                echo "contract: $this->i/$num \r";
                $this->i++;

                if (in_array($this->argument('date'), ['daily', 'daily_all'])) {
                    $c->daily_conversion();
                }

                if ($this->argument('date') == 'monthly') {
                    $c->monthly_conversion();
                }
            }
        });

        if ($this->i > 1) {
            echo "\n";
        }

        system('/bin/chown -R apache '.storage_path('logs'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['date', InputArgument::REQUIRED, 'daily/daily_all/monthly'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        ];
    }
}
