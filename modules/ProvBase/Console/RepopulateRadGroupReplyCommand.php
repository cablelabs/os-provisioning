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
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Nas;
use Modules\ProvBase\Entities\RadCheck;
use Modules\ProvBase\Entities\RadGroupReply;
use Modules\ProvBase\Entities\RadIpPool;
use Modules\ProvBase\Entities\RadUserGroup;

class RepopulateRadGroupReplyCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:raddb-repopulate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate radius config tables and refresh all entries';

    /**
     * Execute the console command.
     *
     * This is called during an nmsprime update,
     * since $radiusAttributes may have changed
     *
     * The command truncates all radius tables that configure the RADIUS server and repopulates them.
     * It is the corresponding command to the DHCP command 'nms:dhcp'
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

        Nas::repopulateDb();                    // NetGw
        RadGroupReply::repopulateDb();          // QoS
        RadIpPool::repopulateDb();              // IpPool

        $this->repopulateRadModems();
    }

    /**
     * Repopulate modem related tables radcheck and radusergroup
     *
     * For performance reasons this is done in one function to not query all modems from DB twice
     */
    public function repopulateRadModems()
    {
        $chunksize = 1000;
        $modemQuery = Modem::join('configfile as cf', 'cf.id', 'modem.configfile_id')
            ->where('cf.device', 'tr069')
            ->where('internet_access', 1);
        $count = $modemQuery->count();

        echo "Build modem related radcheck and radusergroup table ...\n";

        RadCheck::truncate();                   // Modem
        RadUserGroup::truncate();              // Pivot of QoS to Modem

        echo "0/$count\r";

        $modemQuery->chunk($chunksize, function ($modems) use ($count, $chunksize) {
            static $i = 1;

            foreach ($modems as $modem) {
                // TODO: Write INSERT statements to file and hand it over to DB to improve performance
                $modem->updateRadius();
            }

            echo $i * $chunksize.'/'.$count."\r";
        });
    }
}
