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
use Modules\ProvBase\Entities\Modem;
use Modules\HfcReq\Entities\NetElement;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\HfcCustomer\Http\Controllers\CustomerTopoController;

class AddModemsToPassiveElementCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:mtpe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a bunch of Modems to a passive Netelement';

    /**
     * The Passive Netelement the modem get added to
     *
     * @var Netelement
     */
    protected $netelement;

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
        $this->netelement = NetElement::findOrFail($this->argument('netelement'));

        if (! in_array($this->netelement->base_type_id, [11, 12, 13, 14])) {
            $this->warn('You have to select a Netelement of Type Passive Component');

            return 1;
        }

        if ($this->argument('url')) {
            $this->parseURL();

            return;
        }

        if ($this->option('modems')) {
            $this->parseString();

            return;
        }

        $this->error('No valid data provided! You need to enter either a valid URL or a NetElement AND at least one Modem!');
    }

    protected function parseUrl()
    {
        $coords = explode('/', $this->argument('url'));
        $modems = collect((new CustomerTopoController())->show_poly(array_pop($coords), true));

        $this->updateModems($modems);
    }

    protected function parseString()
    {
        $modems = explode('+', $this->option('modems'));

        if ($modems[0] == 0) {
            return $this->updateModems(collect(array_slice($modems, 1)));
        }

        return $this->updateModems(collect($modems));
    }

    protected function updateModems($modems)
    {
        Modem::findOrFail($modems)->each->update([
            'next_passive_id' => $this->argument('netelement'),
        ]);

        $this->info("success! {$modems->count()} Modems added.");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['netelement', InputArgument::REQUIRED, 'Id of the Netelement'],
            ['url', InputArgument::OPTIONAL, 'Paste in the URL of TOPO with coordinates'],
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
            ['modems', 'm', InputOption::VALUE_OPTIONAL, 'Ids of the modems connected with +', null],
        ];
    }
}
