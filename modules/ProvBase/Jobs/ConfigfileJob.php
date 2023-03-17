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

namespace Modules\ProvBase\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConfigfileJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Queueable;

    protected $filter;

    protected $cfId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filter = null, $cfId = null)
    {
        $this->filter = $filter;
        $this->cfId = $cfId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $configfile = new \Modules\ProvBase\Entities\Configfile;
        if (isset($this->cfId)) {
            $configfile->execute(null, $this->cfId);

            return;
        }

        $configfile->execute($this->filter);
    }
}
