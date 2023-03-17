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

namespace Modules\ProvBase\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\ProvBase\Entities\Contract;
use Nwidart\Modules\Facades\Module;
use View;

class ProvBaseController extends BaseController
{
    public function index()
    {
        $title = 'Provisioning Dashboard';

        $contracts_data = self::getContractDashboardData();

        return View::make('provbase::index', $this->compact_prep_view(compact('title', 'contracts_data')));
    }

    public static function getContractDashboardData()
    {
        if (Module::collections()->has('BillingBase')) {
            return \Modules\BillingBase\Helpers\BillingAnalysis::getContractData();
        }

        return [
            'total' => Contract::where('contract_start', '<=', now())
                ->where(whereLaterOrEqual('contract_end', now()))
                ->count(),
        ];
    }

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        // label has to be the same like column in sql table
        return [
            ['form_type' => 'ip', 'name' => 'provisioning_server', 'description' => 'Provisioning Server IP'],
            ['form_type' => 'text', 'name' => 'ro_community', 'description' => 'SNMP Read Only Community'],
            ['form_type' => 'text', 'name' => 'rw_community', 'description' => 'SNMP Read Write Community'],

            ['form_type' => 'text', 'name' => 'domain_name', 'description' => 'Domain Name for Modems'],
            ['form_type' => 'text', 'name' => 'dns_password', 'description' => 'DNS update password', 'help' => 'MD5 HMAC; create using: ddns-confgen -a hmac-md5 -r /dev/urandom | grep secret', 'hidden' => \Module::collections()->has('ProvHA')],
            ['form_type' => 'text', 'name' => 'dhcp_def_lease_time', 'description' => 'DHCP Default Lease Time'],
            ['form_type' => 'text', 'name' => 'dhcp_max_lease_time', 'description' => 'DHCP Max Lease Time'],
            ['form_type' => 'text', 'name' => 'ppp_session_timeout', 'description' => 'PPP Session-Timeout', 'help' => trans('helper.ppp_session_timeout')],
            ['form_type' => 'text', 'name' => 'max_cpe', 'description' => 'Max CPEs per Modem', 'help' => 'Minimum & Default: 2'],
            ['form_type' => 'text', 'name' => 'ds_rate_coefficient', 'description' => 'Downstream rate coefficient', 'help' => trans('helper.rate_coefficient')],
            ['form_type' => 'text', 'name' => 'us_rate_coefficient', 'description' => 'Upstream rate coefficient', 'help' => trans('helper.rate_coefficient')],

            ['form_type' => 'text', 'name' => 'startid_contract', 'description' => 'Start ID Contracts'],
            ['form_type' => 'text', 'name' => 'startid_modem', 'description' => 'Start ID Modems'],
            ['form_type' => 'text', 'name' => 'startid_endpoint', 'description' => 'Start ID Endpoints'],

            ['form_type' => 'text', 'name' => 'acct_interim_interval', 'description' => 'Acct-Interim-Interval', 'help' => trans('helper.acct_interim_interval')],

            ['form_type' => 'checkbox', 'name' => 'modem_edit_page_new_tab', 'description' => 'Opening Modem Edit Page in New Tab', 'help' => trans('helper.openning_new_tab_for_modem')],
            ['form_type' => 'checkbox', 'name' => 'multiple_provisioning_systems', 'description' => 'Multiple provisioning systems', 'help' => 'Check if there are other DHCP servers in your network'],
            ['form_type' => 'checkbox', 'name' => 'additional_modem_reset', 'description' => 'Additional modem reset button', 'help' => trans('helper.additional_modem_reset')],
            ['form_type' => 'checkbox', 'name' => 'random_ip_allocation', 'description' => 'Allocate PPPoE IPs randomly'],
            ['form_type' => 'checkbox', 'name' => 'auto_factory_reset', 'description' => 'Automatic factory reset', 'help' => trans('helper.auto_factory_reset')],
        ];
    }

    /**
     * Base functionality for server-sent event.
     *
     * As stated in https://www.php.net/manual/en/function.flush.php,
     * you sometimes need to send whitespace if you want the browser to display the data.
     *
     * @param  string
     * @return \Illuminate\Http\Response
     *
     * @author Nino Ryschawy, Roy Schneider
     */
    public static function serverSentEvents($cmd)
    {
        return response()->stream(function () use ($cmd) {
            $handle = popen($cmd, 'r');

            if (! is_resource($handle)) {
                echo "data: finished\n\n";
                ob_flush();
                flush();

                return;
            }

            while (! feof($handle)) {
                $line = fgets($handle);
                $line = str_replace("\n", '', $line);

                echo "data: <br>$line";
                // browser won't display anything until it receives enough data
                echo str_repeat("\n", 4096);

                ob_flush();
                flush();
            }

            pclose($handle);
            echo "data: finished\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream; charset=utf-8',
        ]);
    }
}
