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
use Modules\ProvBase\Entities\Qos;
use Modules\ProvBase\Entities\ProvBase;
use Modules\ProvBase\Observers\QosObserver;
use Modules\ProvBase\Entities\RadGroupReply;

class RepopulateRadGroupReplyCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:radgroupreply-repopulate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate radgroupreply table and refresh all entries';

    /**
     * Execute the console command.
     *
     * This is called during an nmsprime update,
     * since $radiusAttributes may have changed
     *
     * @return mixed
     */
    public function handle()
    {
        // check if writing to database is allowed
        if (\Module::collections()->has('ProvHA')) {
            if ('master' != config('provha.hostinfo.ownState')) {
                $msg = 'ProvHA slave not allowed to change database. Exiting…';
                \Log::warning(__METHOD__.': '.$msg);
                $this->info($msg);

                return;
            }
        }

        RadGroupReply::truncate();

        $provbase = ProvBase::first();

        $insert = [
            ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Port-Limit', 'op' => ':=', 'value' => '1'],
            ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Framed-MTU', 'op' => ':=', 'value' => '1492'],
            ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Framed-Protocol', 'op' => ':=', 'value' => 'PPP'],
            ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Service-Type', 'op' => ':=', 'value' => 'Framed-User'],
            ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Acct-Interim-Interval', 'op' => ':=', 'value' => $provbase->acct_interim_interval],
        ];

        if ($sessionTimeout = $provbase->ppp_session_timeout) {
            $insert[] = ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Session-Timeout', 'op' => ':=', 'value' => $sessionTimeout];
        }

        // this (Fall-Through) MUST be the last entry of $defaultGroup
        $insert[] = ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Fall-Through', 'op' => '=', 'value' => 'Yes'];
        RadGroupReply::insert($insert);

        $observer = new QosObserver;
        foreach (Qos::all() as $qos) {
            $observer->created($qos);
        }
    }
}
