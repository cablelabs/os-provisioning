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

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfigfileCommand extends Command implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'nms:configfile {filter?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make all configfiles';

    /**
     * Filter (from argument) to only build cable modem or mta configfiles
     *
     * @var string cm|mta|tr069
     */
    protected $filter = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($filter = '')
    {
        $this->filter = $filter;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->input && $this->argument('filter')) {
            $this->filter = $this->argument('filter');
        }

        $configfile = new \Modules\ProvBase\Entities\Configfile;
        $configfile->execute($this->filter);
    }
}
