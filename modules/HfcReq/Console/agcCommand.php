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

namespace Modules\HfcReq\Console;

use Illuminate\Console\Command;
use Modules\HfcReq\Entities\NetElement;

class agcCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:agc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute Automatic Gain Control based on measured SNR';

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
     * Execute the console command. Automatically adjust power level of all CMTS
     *
     * @return true
     *
     * @author: Ole Ernst
     */
    public function handle()
    {
        // get all clusters having a non-null AGC offset
        foreach (NetElement::where('netelementtype_id', 2)->whereNotNull('agc_offset')->get() as $netelement) {
            $netelement->apply_agc();
        }

        return true;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // array('example', InputArgument::REQUIRED, 'An example argument.'),
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
