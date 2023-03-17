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
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CacheIndexTableCountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels, Queueable;

    /**
     * Modelnames that dont have index tables
     *
     * @var array
     */
    protected $excludes = [
        'AccountingRecord',
        'CccUser',
        'RadGroupReply',
        'RadIpPool',
    ];

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $models = \App\BaseModel::get_models();

        foreach ($models as $modelname => $namespace) {
            if (in_array($modelname, $this->excludes)) {
                continue;
            }

            $table = (new $namespace)->table;

            if (! $table || ! \Schema::hasTable($table)) {
                continue;
            }

            $count = $namespace::count();

            if ($count < 2) {
                continue;
            }

            cache(['indexTables.'.$table => $count]);
        }

        system('chown -R apache:apache '.storage_path('framework/cache'));
    }
}
