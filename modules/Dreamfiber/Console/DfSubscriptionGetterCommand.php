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

namespace Modules\Dreamfiber\Console;

use Illuminate\Console\Command;
use Modules\Dreamfiber\Entities\Dreamfiber;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DfSubscriptionGetterCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dreamfiber:get_dfsubscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Dreamfiber suscriptions from API.';

    /**
     * The supported filter arguments
     */
    protected $filtersAvailable = [
        'all',
        'pending',
        'single',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     *
     * @author Patrick Reichel
     */
    public function __construct()
    {
        parent::__construct();

        $this->dreamfiber = new Dreamfiber();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @author Patrick Reichel
     */
    public function handle()
    {
        $option = null;

        $filter = $this->argument('filter');
        if (! in_array($filter, $this->filtersAvailable)) {
            $this->error('Given argument “'.$filter.'” not in ['.implode('|', $this->filtersAvailable).']');
            $this->line('');
            exit(1);
        }

        if ('single' == $filter) {
            $option = $this->option('subscriptionid');
        }

        $this->dreamfiber->getDfSubscriptionInformation($filter, $option);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     *
     * @author Patrick Reichel
     */
    protected function getArguments()
    {
        return [
            [
                'filter',
                InputArgument::REQUIRED,
                'What subscriptions shall we get? ['.implode('|', $this->filtersAvailable).']',
            ],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     *
     * @author Patrick Reichel
     */
    protected function getOptions()
    {
        return [
            ['subscriptionid', null, InputOption::VALUE_OPTIONAL, 'Subscription ID to get information for', null],
        ];
    }
}
