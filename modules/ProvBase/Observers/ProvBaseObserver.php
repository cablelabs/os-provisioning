<?php

namespace Modules\ProvBase\Observers;

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
        $model->make_dhcp_glob_conf();

        $changes = $model->getDirty();

        // create new CPE ignore file
        if (array_key_exists('multiple_provisioning_systems', $changes)) {
            Modem::create_ignore_cpe_dhcp_file();
        }

        // recreate default network, if provisioning server ip address has been changed
        if (array_key_exists('provisioning_server', $changes)) {
            $model->make_dhcp_default_network_conf();
        }

        if (array_key_exists('dhcp_def_lease_time', $changes)) {
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
            \Artisan::call('nms:radgroupreply-repopulate');
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
            \Queue::push(new \Modules\ProvBase\Jobs\ConfigfileJob('cm'));
        }

        if (array_key_exists('ro_community', $changes)) {
            // update cacti database: replace the original snmp ro_community with the new one
            \DB::connection('mysql-cacti')
                ->table('host')
                ->where('hostname', 'like', "cm-%.{$model->domain_name}")
                ->where('snmp_community', $model->getOriginal('ro_community'))
                ->update(['snmp_community' => $model->ro_community]);
        }

        if (array_key_exists('domain_name', $changes)) {
            // update cacti database: replace the original domain_name with the new one
            \DB::connection('mysql-cacti')
                ->table('host')
                ->where('hostname', 'like', "cm-%.{$model->getOriginal('domain_name')}")
                ->update(['hostname' => \DB::raw("REPLACE(hostname, '{$model->getOriginal('domain_name')}', '$model->domain_name')")]);

            \DB::connection('mysql-cacti')
                ->table('data_input_data')
                ->where('value', 'like', "cm-%.{$model->getOriginal('domain_name')}")
                ->update(['value' => \DB::raw("REPLACE(value, '{$model->getOriginal('domain_name')}', '$model->domain_name')")]);

            \DB::connection('mysql-cacti')
                ->table('poller_item')
                ->where('hostname', 'like', "cm-%.{$model->getOriginal('domain_name')}")
                ->update(['hostname' => \DB::raw("REPLACE(hostname, '{$model->getOriginal('domain_name')}', '$model->domain_name')")]);

            \DB::connection('mysql-cacti')
                ->table('poller_item')
                ->where('arg1', 'like', "%cm-%.{$model->getOriginal('domain_name')}%")
                ->update(['arg1' => \DB::raw("REPLACE(arg1, '{$model->getOriginal('domain_name')}', '$model->domain_name')")]);

            \DB::connection('mysql-cacti')
                ->table('poller')
                ->where('hostname', 'like', "%.{$model->getOriginal('domain_name')}")
                ->update(['hostname' => \DB::raw("REPLACE(hostname, '{$model->getOriginal('domain_name')}', '$model->domain_name')")]);

            \DB::connection('mysql-cacti')
                ->table('poller')
                ->where('dbhost', 'like', "%.{$model->getOriginal('domain_name')}")
                ->update(['dbhost' => \DB::raw("REPLACE(dbhost, '{$model->getOriginal('domain_name')}', '$model->domain_name')")]);

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
