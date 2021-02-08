<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Modem;

class EndpointController extends \BaseController
{
    protected $index_create_allowed = false;

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        if (! $model->exists) {
            $model->hostname = $model->getNewHostname();
        }

        // label has to be the same like column in sql table
        return [
            ['form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'help' => '.cpe.'.\Modules\ProvBase\Entities\ProvBase::first()->domain_name],
            ['form_type' => 'text', 'name' => 'modem_id', 'description' => 'Modem', 'hidden' => 1],
            ['form_type' => 'text', 'name' => 'mac', 'description' => 'MAC Address', 'options' => ['placeholder' => 'AA:BB:CC:DD:EE:FF'], 'help' => trans('helper.endpointMac').' '.trans('helper.mac_formats')],
            ['form_type' => 'checkbox', 'name' => 'fixed_ip', 'description' => 'Fixed IP', 'value' => '1', 'help' => trans('helper.fixed_ip_warning')],
            ['form_type' => 'text', 'name' => 'ip', 'description' => 'Fixed IP', 'checkbox' => 'show_on_fixed_ip'],
            ['form_type' => 'text', 'name' => 'prefix', 'description' => trans('messages.prefix').' (IPv6)', 'checkbox' => 'show_on_fixed_ip', 'options' => ['placeholder' => 'fd00:1::/64']],
            ['form_type' => 'text', 'name' => 'add_reverse', 'description' => 'Additional rDNS record', 'checkbox' => 'show_on_fixed_ip', 'help' => trans('helper.addReverse')],
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],

        ];
    }

    protected function prepare_input($data)
    {
        $data = parent::prepare_input($data);

        if ($data['fixed_ip'] == 0) {
            // delete possibly existing ip to avoid later collisions in validation rules
            $data['ip'] = $data['prefix'] = $data['version'] = null;
        } else {
            $data['version'] = IpPoolController::getVersion($data['ip']);
        }

        return unifyMac($data);
    }

    // TODO: tr069 must always be fixed IPv4
    protected function prepare_rules($rules, $data)
    {
        if ($data['version'] == '6') {
            $rules['prefix'][] = 'required';
        }

        $modem = Modem::with('configfile')->find($data['modem_id']);

        // CNAME: hostname -> mangled name based on CPE MAC address (DOCSIS only)
        // the only case not requiring an IP address
        if ($modem && $modem->configfile->device == 'cm' && ! $data['version']) {
            $rules['mac'][] = 'required';

            return parent::prepare_rules($rules, $data);
        }

        $rules['ip'][] = 'required';

        // Assign fixed IP to unknown CPE behind CM only implemented for v4
        if ($modem && $modem->configfile->device == 'cm' && $data['version'] == 4) {
            return parent::prepare_rules($rules, $data);
        }

        $rules['mac'][] = 'required';

        return parent::prepare_rules($rules, $data);
    }
}
