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

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DoNothingForXSecondsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels, Queueable;

    private $sleepTime = null;
    private $pushTime = null;

    public function __construct($pushTime = '1900-01-01', $sleepTime = 30)
    {
        $this->sleepTime = $sleepTime;
        $this->pushTime = $pushTime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo "\n";
        echo date('c').' –    PID '.getmypid().' (pushed '.$this->pushTime.'): Entering '.__METHOD__;
        echo "\n";
        echo date('c').' –        PID '.getmypid().' (pushed '.$this->pushTime.'): Going to sleep for '.$this->sleepTime.' sec';
        echo "\n";
        sleep($this->sleepTime);
        echo "\n";
        echo date('c').' –            PID '.getmypid().' (pushed '.$this->pushTime.'): Now awake and exiting';
        echo "\n";
        echo "\n";
    }
}
