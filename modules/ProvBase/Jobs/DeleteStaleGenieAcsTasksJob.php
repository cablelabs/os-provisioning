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

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\ProvBase\Entities\Modem;

class DeleteStaleGenieAcsTasksJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job
     */
    public function handle()
    {
        $age = 4 * 60 * 60; // 4h in seconds
        $chunkSize = 100;
        $deleteTasks = [];

        // add all tasks older than $age to considered delete-tasks
        foreach ((array) json_decode(Modem::callGenieAcsApi('tasks?projection=device,timestamp', 'GET'), true) as $entry) {
            if (Carbon::parse($entry['timestamp'])->diffInSeconds() > $age) {
                $deleteTasks[last(explode('-', $entry['device']))][] = $entry['_id'];
            }
        }

        // remove all tasks of non-deleted modems from considered delete-tasks, leaving only stale tasks
        foreach (array_chunk(array_keys($deleteTasks), $chunkSize) as $serialNumChunk) {
            foreach (Modem::whereIn('serial_num', $serialNumChunk)->pluck('serial_num') as $serialNum) {
                unset($deleteTasks[$serialNum]);
            }
        }

        // delete stale tasks
        foreach (new \RecursiveIteratorIterator(new \RecursiveArrayIterator($deleteTasks)) as $task) {
            Modem::callGenieAcsApi("tasks/$task", 'DELETE');
        }
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        \Log::error($exception);

        clearFailedJobs('\\DeleteStaleGenieAcsTasksJob');
    }
}
