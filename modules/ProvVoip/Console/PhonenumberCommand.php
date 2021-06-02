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

namespace Modules\ProvVoip\Console;

use Illuminate\Console\Command;
use Modules\ProvVoip\Entities\Phonenumber;

class PhonenumberCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'provvoip:phonenumber';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Phonenumber Scheduling Command';

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
        $phonenumbers = Phonenumber::all();

        foreach ($phonenumbers as $phonenumber) {
            $phonenumber->daily_conversion();
        }
    }
}
