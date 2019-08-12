<?php

namespace Modules\ProvBase\Http\Controllers;

use App\Sla;
use Modules\ProvBase\Entities\Cmts;

class CmtsController extends \BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        $init_values = [];

        if (! $model) {
            $model = new Cmts;
        }

        // create context: calc next free ip pool
        if (! $model->exists) {
            $init_values = [];

            // fetch all CMTS ip's and order them
            $ips = Cmts::where('id', '>', '0')->orderBy(\DB::raw('INET_ATON(ip)'))->get();

            // still CMTS added?
            if ($ips->count() > 0) {
                $next_ip = long2ip(ip2long($ips[0]->ip) - 1);
            } // calc: next_ip = last_ip-1
            else {
                $next_ip = env('CMTS_SETUP_FIRST_IP', '172.20.3.253');
            } // default first ip

            $init_values += [
                'ip' => $next_ip,
            ];
        }

        // CMTS series selection based on CMTS company
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

        // The CMTS company and series Array
        foreach (config('provbase.cmts') as $vendor => $__series) {
            $company_array[$vendor] = $vendor;
        }

        $series = config('provbase.cmts.'.$company);

        /**
         * label has to be the same like column in sql table
         */
        // TODO: series should be jquery based select depending on the company
        // TODO: State and Monitoring without functionality -> hidden
        $ret_tmp = [
            ['form_type' => 'select', 'name' => 'company', 'description' => 'Company', 'value' => $company_array],
            ['form_type' => 'select', 'name' => 'series', 'description' => 'Series', 'value' => $series],
            ['form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname'],
            ['form_type' => 'ip', 'name' => 'ip', 'description' => 'IP', 'help' => 'Online'],
            ['form_type' => 'text', 'name' => 'community_rw', 'description' => 'SNMP Private Community String'],
            ['form_type' => 'text', 'name' => 'community_ro', 'description' => 'SNMP Public Community String'],
            ['form_type' => 'text', 'name' => 'state', 'description' => 'State', 'hidden' => 1],
            ['form_type' => 'text', 'name' => 'monitoring', 'description' => 'Monitoring', 'hidden' => 1],
        ];
        if (Sla::first()->valid()) {
            $ret_tmp[] = ['form_type'=> 'text',
                'name' => 'formatted_support_state',
                'description' => 'Support State',
                'field_value'=> ucfirst(str_replace('-', ' ', $model->support_state)),
                'help'=>trans('helper.cmtsSupportState.'.$model->support_state),
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
     * @param Modules\ProvBase\Entities\Cmts
     * @return array
     */
    protected function editTabs($cmts)
    {
        if (! \Module::collections()->has('ProvMon')) {
            return [];
        }

        $tabs = parent::editTabs($cmts);

        if (\Bouncer::can('view_analysis_pages_of', Cmts::class)) {
            array_push($tabs, ['name' => 'Analyses', 'route' => 'ProvMon.cmts', 'link' => $cmts->id]);
        }

        return $tabs;
    }
}
