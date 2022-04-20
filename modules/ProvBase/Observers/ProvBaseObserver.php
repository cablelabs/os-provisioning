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

namespace Modules\ProvBase\Observers;

use Queue;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\ProvBase;
use Modules\ProvBase\Entities\RadGroupReply;

/**
 * ProvBase Observer Class
 * Handles changes on ProvBase Gateways
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class ProvBaseObserver
{
    public function updated($model)
    {
        $changes = $model->getDirty();

        // create new CPE ignore file
        if (array_key_exists('multiple_provisioning_systems', $changes)) {
            Modem::create_ignore_cpe_dhcp_file();
        }

        // recreate default network, if provisioning server ip address has been changed
        if (array_key_exists('provisioning_server', $changes)) {
            Queue::pushOn('high', new \Modules\ProvBase\Jobs\DhcpJob());
        }

        if (multi_array_key_exists(['dhcp_def_lease_time', 'dhcp_max_lease_time'], $changes)) {
            // recreate global DHCP config file
            $model->make_dhcp_glob_conf();

            // adjust radiusd config and restart it
            $sed = storage_path('app/tmp/update-sqlippool.sed');
            file_put_contents($sed, "s/^\s*lease_duration\s*=.*/\tlease_duration = $model->dhcp_def_lease_time/");
            exec("sudo sed -i -f $sed /etc/raddb/mods-available/sqlippool");
            exec('sudo systemctl restart radiusd.service');
            unlink($sed);
        }

        if (array_key_exists('ppp_session_timeout', $changes)) {
            $this->updatePPPSessionTimeout($model->ppp_session_timeout);
        }

        if (array_key_exists('acct_interim_interval', $changes)) {
            RadGroupReply::repopulateDB();
        }

        if (array_key_exists('random_ip_allocation', $changes)) {
            $queryPath = storage_path('app/config/provbase/radius/queries.conf');
            if ($model->random_ip_allocation) {
                $query = 'allocate_find = "SELECT framedipaddress FROM ${ippool_table} WHERE pool_name = \'%{control:Pool-Name}\' AND expiry_time IS NULL ORDER BY RAND() LIMIT 1 FOR UPDATE"';
            } else {
                $query = 'allocate_find = "SELECT framedipaddress FROM ${ippool_table} WHERE pool_name = \'%{control:Pool-Name}\' AND (expiry_time < NOW() OR expiry_time IS NULL) ORDER BY (username <> \'%{User-Name}\'), (callingstationid <> \'%{Calling-Station-Id}\'), expiry_time LIMIT 1 FOR UPDATE"';
            }
            file_put_contents($queryPath, $query."\n");
            exec('sudo systemctl restart radiusd.service');
        }

        // re-evaluate all qos rate_max_help fields if one or both coefficients were changed
        if (multi_array_key_exists(['ds_rate_coefficient', 'us_rate_coefficient'], $changes)) {
            $pb = ProvBase::first();
            foreach (Qos::all() as $qos) {
                $qos->ds_rate_max_help = $qos->ds_rate_max * 1000 * 1000 * $pb->ds_rate_coefficient;
                $qos->us_rate_max_help = $qos->us_rate_max * 1000 * 1000 * $pb->us_rate_coefficient;
                $qos->save();
            }
        }

        // build all Modem Configfiles via Job as this will take a long time
        if (multi_array_key_exists(['ds_rate_coefficient', 'us_rate_coefficient', 'max_cpe'], $changes)) {
            Queue::pushOn('medium', new \Modules\ProvBase\Jobs\ConfigfileJob('cm'));
        }

        if (array_key_exists('domain_name', $changes)) {
            // adjust named config and restart it
            $sed = storage_path('app/tmp/update-domain.sed');
            file_put_contents($sed, "s/zone \"{$model->getOriginal('domain_name')}\" IN/zone \"$model->domain_name\" IN/g");
            exec("sudo sed -i -f $sed /etc/named-nmsprime.conf");

            file_put_contents($sed, "s/{$model->getOriginal('domain_name')}/$model->domain_name/g");
            exec("sudo sed -i -f $sed /etc/named-ddns.sh");

            exec('sudo rndc sync -clean');
            exec("sudo sed -i -f $sed /var/named/dynamic/in-addr.arpa.zone");
            exec("sudo sed -i -f $sed /var/named/dynamic/nmsprime.test.zone");
            exec('sudo systemctl restart named.service');
            unlink($sed);
        }

        if (array_key_exists('dns_password', $changes)) {
            $model->makeDdnsConf();
        }
    }

    /**
     * Update Session-Timeout attribute in radgroupreply table
     *
     * Handles both setting and resetting Session-Timeout and
     * multiple Session-Timeout entries in radgroupreply table,
     * which shouldn't happen
     *
     * @author Ole Ernst
     */
    private function updatePPPSessionTimeout($timeout)
    {
        $defaultGroup = RadGroupReply::$defaultGroup;
        $query = RadGroupReply::where('groupname', $defaultGroup)
            ->where('attribute', 'Session-Timeout');

        if ($timeout && $query->count() == 1) {
            $query->update(['value' => $timeout]);

            return;
        }

        $query->delete();

        if (! $timeout) {
            return;
        }

        RadGroupReply::where('groupname', $defaultGroup)
            ->where('attribute', 'Fall-Through')
            ->delete();

        RadGroupReply::insert([
            ['groupname' => $defaultGroup, 'attribute' => 'Session-Timeout', 'op' => ':=', 'value' => $timeout],
            // this (Fall-Through) MUST be the last entry of $defaultGroup
            ['groupname' => $defaultGroup, 'attribute' => 'Fall-Through', 'op' => '=', 'value' => 'Yes'],
        ]);
    }
}
