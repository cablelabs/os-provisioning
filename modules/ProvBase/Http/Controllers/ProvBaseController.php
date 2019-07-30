<?php

namespace Modules\ProvBase\Http\Controllers;

use View;
use App\Http\Controllers\BaseController;
use Modules\Dashboard\Entities\BillingAnalysis;

class ProvBaseController extends BaseController
{
    public function index()
    {
        $data['income'] = BillingAnalysis::getIncomeData();
        $title = 'Provisioning Dashboard';
        $create_allowed = static::get_controller_obj()->index_create_allowed;
        $contracts_data = BillingAnalysis::getContractData();

        return View::make('provbase::index', $this->compact_prep_view(compact('title', 'create_allowed', 'contracts_data')));
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
            ['form_type' => 'text', 'name' => 'notif_mail', 'description' => 'Notification Email Address'],
            ['form_type' => 'text', 'name' => 'dhcp_def_lease_time', 'description' => 'DHCP Default Lease Time'],
            ['form_type' => 'text', 'name' => 'dhcp_max_lease_time', 'description' => 'DHCP Max Lease Time'],
            ['form_type' => 'text', 'name' => 'max_cpe', 'description' => 'Max CPEs per Modem', 'help' => 'Minimum & Default: 2'],
            ['form_type' => 'text', 'name' => 'ds_rate_coefficient', 'description' => 'Downstream rate coefficient', 'help' => trans('helper.rate_coefficient')],
            ['form_type' => 'text', 'name' => 'us_rate_coefficient', 'description' => 'Upstream rate coefficient', 'help' => trans('helper.rate_coefficient')],

            ['form_type' => 'text', 'name' => 'startid_contract', 'description' => 'Start ID Contracts'],
            ['form_type' => 'text', 'name' => 'startid_modem', 'description' => 'Start ID Modems'],
            ['form_type' => 'text', 'name' => 'startid_endpoint', 'description' => 'Start ID Endpoints'],

            ['form_type' => 'checkbox', 'name' => 'modem_edit_page_new_tab', 'description' => 'Opening Modem Edit Page in New Tab', 'help' => trans('helper.openning_new_tab_for_modem')],
            ['form_type' => 'checkbox', 'name' => 'multiple_provisioning_systems', 'description' => 'Multiple provisioning systems', 'help' => 'Check if there are other DHCP servers in your network'],
            ['form_type' => 'checkbox', 'name' => 'additional_modem_reset', 'description' => 'Additional modem reset button', 'help' => trans('helper.additional_modem_reset')],
        ];
    }
}
