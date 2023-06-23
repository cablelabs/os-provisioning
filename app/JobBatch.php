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

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobBatch extends Model
{

    protected $table = 'job_batches';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'total_jobs'  => 'integer',
        'pending_jobs'  => 'integer',
        'failed_jobs'  => 'integer',
        'created_at'   => 'datetime',
        'cancelled_at' => 'datetime',
        'finished_at'  => 'datetime',
    ];

    public function processedJobs(): int
    {
        return $this->total_jobs - $this->pending_jobs;
    }

    public function progress(): int
    {
        return $this->total_jobs > 0 ? round(($this->processedJobs() / $this->total_jobs) * 100) : 0;
    }

    public function hasPendingJobs(): bool
    {
        return $this->pending_jobs > 0;
    }

    public function finished(): bool
    {
        return !is_null($this->finished_at);
    }

    public function hasFailures(): bool
    {
        return $this->failed_jobs > 0;
    }

    public function failed(): bool
    {
        return $this->failed_jobs === $this->total_jobs;
    }

    public function cancelled(): bool
    {
        return !is_null($this->cancelled_at);
    }

}
