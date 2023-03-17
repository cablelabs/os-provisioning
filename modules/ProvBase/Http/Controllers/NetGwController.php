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

use App\Sla;
use Modules\ProvBase\Entities\NetGw;

class NetGwController extends \BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        $init_values = [];

        if (! $model) {
            $model = new NetGw;
        }

        // create context: calc next free ip pool
        if (! $model->exists) {
            $init_values = [];

            // fetch all NETGW ip's and order them
            $ips = NetGw::orderBy('ip')->get();

            // still NETGW added?
            if ($ips->count() > 0) {
                $next_ip = long2ip(ip2long($ips[0]->ip) - 1);
            } // calc: next_ip = last_ip-1
            else {
                $next_ip = env('NETGW_SETUP_FIRST_IP', '172.20.3.253');
            } // default first ip

            $init_values += [
                'ip' => $next_ip,
            ];
        }

        // NETGW series selection based on NETGW company
        if (\Request::filled('company')) { // for auto reload
            $company = \Request::get('company');
        } elseif ($model->exists) { // else if using edit.blade
            $company = $model->company;
            $init_values += [
                'series' => $model->series,
            ];
        } else { // a fresh create
            $company = 'Cisco';
        }

        $types = array_map('strtoupper', array_combine(NetGw::TYPES, NetGw::TYPES));

        // TODO: series should be jquery based select depending on the company
        // TODO: (For BRAS) Make company and series field nullable and add empty field to company_array
        $ret_tmp = [
            ['form_type' => 'select', 'name' => 'company', 'description' => 'Company', 'value' => $this->getSelectFromConfig()],
            ['form_type' => 'select', 'name' => 'series', 'description' => 'Series', 'value' => $this->getSelectFromConfig($company)],
            ['form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => $types, 'select' => $types],
            ['form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname'],
            ['form_type' => 'ip', 'name' => 'ip', 'description' => 'IP', 'help' => 'Online'],
            ['form_type' => 'ip', 'name' => 'ipv6', 'description' => 'IPv6', 'help' => 'Online'],
            ['form_type' => 'text', 'name' => 'community_rw', 'description' => 'SNMP Private Community String'],
            ['form_type' => 'text', 'name' => 'community_ro', 'description' => 'SNMP Public Community String'],
            ['form_type' => 'text', 'name' => 'nas_secret', 'description' => 'RADIUS Client secret', 'select' => 'BRAS'],
            ['form_type' => 'text', 'name' => 'coa_port', 'description' => 'RADIUS Change of Authorization port', 'select' => 'BRAS'],
            ['form_type' => 'checkbox', 'name' => 'ssh_auto_prov', 'description' => 'Auto-Provisioning via SSH', 'value' => '1', 'select' => 'OLT', 'help' => trans('helper.ssh_auto_prov')],
            ['form_type' => 'text', 'name' => 'username', 'description' => 'SSH username', 'checkbox' => 'show_on_ssh_auto_prov'],
            ['form_type' => 'text', 'name' => 'password', 'description' => 'SSH password', 'checkbox' => 'show_on_ssh_auto_prov'],
            ['form_type' => 'text', 'name' => 'ssh_port', 'description' => 'SSH port', 'checkbox' => 'show_on_ssh_auto_prov'],
            // The following fields are currently not used
            // ['form_type' => 'text', 'name' => 'state', 'description' => 'State', 'hidden' => 1],
            // ['form_type' => 'text', 'name' => 'monitoring', 'description' => 'Monitoring', 'hidden' => 1],
        ];
        if (false && Sla::first()->valid()) {
            $ret_tmp[] = ['form_type'=> 'text',
                'name' => 'formatted_support_state',
                'description' => 'Support State',
                'field_value'=> ucfirst(str_replace('-', ' ', $model->support_state)),
                'help'=>trans('helper.netGwSupportState.'.$model->support_state),
                'help_icon'=> $model->getFaSmileClass()['fa-class'],
                'options' =>['readonly'], 'color'=> $model->getFaSmileClass()['bs-class'], ];
        }

        // add init values if set
        $ret = [];
        foreach ($ret_tmp as $elem) {
            if (array_key_exists($elem['name'], $init_values)) {
                $elem['init_value'] = $init_values[$elem['name']];
            }
            array_push($ret, $elem);
        }

        return $ret;
    }

    private function getSelectFromConfig($key = null)
    {
        $config = config('provbase.netgw'.($key ? ".$key" : ''));
        $config['Other'] = 'Other';

        if (! $key) {
            $config = array_keys($config);
        }

        return array_combine($config, $config);
    }

    protected function prepare_input($data)
    {
        $data = parent::prepare_input($data);

        // delete possibly existing ssh credentials
        if ($data['ssh_auto_prov'] == 0) {
            $data['username'] = null;
            $data['password'] = null;
            $data['ssh_port'] = null;
        }

        return $data;
    }

    public function prepare_rules($rules, $data)
    {
        if ($data['type'] == 'bras') {
            $rules['nas_secret'] = 'required';
        }

        return parent::prepare_rules($rules, $data);
    }

    /**
     * @param Modules\ProvBase\Entities\NetGw
     * @return array
     */
    protected function editTabs($netGw)
    {
        $defaultTabs = parent::editTabs($netGw);

        if ($netGw->netelement) {
            $tabs = $netGw->netelement->tabs();
            unset($tabs[1]['route']);

            $tabs[] = $defaultTabs[1];

            return $tabs;
        }

        $defaultTabs[] = ['name' => 'Analyses', 'icon' => 'area-chart', 'route' => 'ProvMon.netgw', 'link' => $netGw->id];

        if (! \Module::collections()->has('ProvMon')) {
            $defaultTabs[array_key_last($defaultTabs)]['route'] = 'missingModule';
            $defaultTabs[array_key_last($defaultTabs)]['link'] = 'Prime Monitoring';
        }

        return $defaultTabs;
    }
}
