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

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\RadCheck;
use Modules\ProvBase\Entities\RadReply;
use Modules\ProvBase\Entities\RadIpPool;

class UseSqlippool extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add sqlippool table
        \DB::connection('mysql-radius')->unprepared(file_get_contents('/etc/raddb/mods-config/sql/ippool/mysql/schema.sql'));

        // adjust radiusd sqlippool IP lease duration
        $leaseTime = Modules\ProvBase\Entities\ProvBase::first()->dhcp_def_lease_time;
        $queryPath = storage_path('app/config/provbase/radius/queries.conf');
        exec("sed -i -e 's/^\s*lease_duration\s*=.*/\tlease_duration = $leaseTime/' -e '/^\s*\$INCLUDE/i\\\\t\$INCLUDE $queryPath' /etc/raddb/mods-available/sqlippool");

        // enable sqlippool
        $link = '/etc/raddb/mods-enabled/sqlippool';
        exec("ln -srf /etc/raddb/mods-available/sqlippool $link");
        exec("chgrp -h radiusd $link");
        exec("sed -i -e '/^accounting {/a\\\\tsqlippool' -e '/^post-auth {/a\\\\tsqlippool' /etc/raddb/sites-enabled/default");
        exec('systemctl restart radiusd.service');

        // populate radippool table
        $insert = [];
        foreach (Modules\ProvBase\Entities\NetGw::where('type', 'bras')->get() as $bras) {
            foreach ($bras->ippools as $pool) {
                foreach (array_map('long2ip', range(ip2long($pool->ip_pool_start), ip2long($pool->ip_pool_end))) as $ip) {
                    $insert[] = [
                        'pool_name' => $pool->type,
                        'framedipaddress' => $ip,
                    ];
                }
            }
        }
        RadIpPool::truncate();
        RadIpPool::insert($insert);
        // don't lease fixed ip addresses of endpoints, i.e. set expiry_time to 'infinity'
        $fixedIp = Modules\ProvBase\Entities\Endpoint::pluck('ip');
        RadIpPool::whereIn('framedipaddress', $fixedIp)->update(['expiry_time' => '9999-12-31 23:59:59']);

        // repopulate radreply table
        $insert = [];
        foreach (Modem::whereNotNull('ppp_username')->get() as $modem) {
            foreach ($modem->endpoints as $endpoint) {
                $insert[] = [
                    'username' => $modem->ppp_username,
                    'attribute' => 'Framed-IP-Address',
                    'op' => ':=',
                    'value' => $endpoint->ip,
                ];
            }
        }
        RadReply::truncate();
        RadReply::insert($insert);

        // repopulate radcheck table
        $insert = [];
        foreach (Modem::whereNotNull('ppp_username')->get() as $modem) {
            $insert[] = [
                'username' => $modem->ppp_username,
                'attribute' => 'Cleartext-Password',
                'op' => ':=',
                'value' => $modem->ppp_password,
            ];
            $insert[] = [
                'username' => $modem->ppp_username,
                'attribute' => 'Pool-Name',
                'op' => ':=',
                'value' => $modem->public ? 'CPEPub' : 'CPEPriv',
            ];
        }
        RadCheck::truncate();
        RadCheck::insert($insert);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
