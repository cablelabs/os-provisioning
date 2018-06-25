<?php

namespace Modules\ProvBase\Http\Controllers;

use App\Http\Controllers\BaseController;

class ProvBaseController extends BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'ip', 'name' => 'provisioning_server', 'description' => 'Provisioning Server IP'),
			array('form_type' => 'text', 'name' => 'ro_community', 'description' => 'SNMP Read Only Community'),
			array('form_type' => 'text', 'name' => 'rw_community', 'description' => 'SNMP Read Write Community'),

			array('form_type' => 'text', 'name' => 'domain_name', 'description' => 'Domain Name for Modems'),
			array('form_type' => 'text', 'name' => 'notif_mail', 'description' => 'Notification Email Address'),
			array('form_type' => 'text', 'name' => 'dhcp_def_lease_time', 'description' => 'DHCP Default Lease Time'),
			array('form_type' => 'text', 'name' => 'dhcp_max_lease_time', 'description' => 'DHCP Max Lease Time'),
			array('form_type' => 'text', 'name' => 'max_cpe', 'description' => 'Max CPEs per Modem', 'help' => 'Minimum & Default: 2'),
			array('form_type' => 'text', 'name' => 'ds_rate_coefficient', 'description' => 'Downstream rate coefficient', 'help' => trans('helper.rate_coefficient')),
			array('form_type' => 'text', 'name' => 'us_rate_coefficient', 'description' => 'Upstream rate coefficient', 'help' => trans('helper.rate_coefficient')),

			array('form_type' => 'text', 'name' => 'startid_contract', 'description' => 'Start ID Contracts'),
			array('form_type' => 'text', 'name' => 'startid_modem', 'description' => 'Start ID Modems'),
			array('form_type' => 'text', 'name' => 'startid_endpoint', 'description' => 'Start ID Endpoints'),
		);
	}

}
