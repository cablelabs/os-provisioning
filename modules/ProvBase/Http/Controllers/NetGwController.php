<?php

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
            $ips = NetGw::where('id', '>', '0')->orderBy(\DB::raw('INET_ATON(ip)'))->get();

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

        // The NETGW company and series Array
        foreach (config('provbase.netgw') as $vendor => $__series) {
            $company_array[$vendor] = $vendor;
        }

        $series = config('provbase.netgw.'.$company);
        $types = array_map('strtoupper', (array_combine(NetGw::TYPES, NetGw::TYPES)));

        // TODO: series should be jquery based select depending on the company
        // TODO: (For BRAS) Make company and series field nullable and add empty field to company_array
        $ret_tmp = [
            ['form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => $types, 'select' => $types],
            ['form_type' => 'select', 'name' => 'company', 'description' => 'Company', 'value' => $company_array, 'select' => 'CMTS'],
            ['form_type' => 'select', 'name' => 'series', 'description' => 'Series', 'value' => $series, 'select' => 'CMTS'],
            ['form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname'],
            ['form_type' => 'ip', 'name' => 'ip', 'description' => 'IP', 'help' => 'Online'],
            ['form_type' => 'text', 'name' => 'community_rw', 'description' => 'SNMP Private Community String'],
            ['form_type' => 'text', 'name' => 'community_ro', 'description' => 'SNMP Public Community String'],
            // The following fields are currently not used
            // ['form_type' => 'text', 'name' => 'state', 'description' => 'State', 'hidden' => 1],
            // ['form_type' => 'text', 'name' => 'monitoring', 'description' => 'Monitoring', 'hidden' => 1],
        ];
        if (Sla::first()->valid()) {
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

    /**
     * @param Modules\ProvBase\Entities\NetGw
     * @return array
     */
    protected function editTabs($netgw)
    {
        if (! \Module::collections()->has('ProvMon')) {
            return [];
        }

        $tabs = parent::editTabs($netgw);

        if (\Bouncer::can('view_analysis_pages_of', NetGw::class)) {
            array_push($tabs, ['name' => 'Analyses', 'route' => 'ProvMon.netgw', 'link' => $netgw->id]);
        }

        return $tabs;
    }
}
