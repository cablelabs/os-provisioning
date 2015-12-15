<?php 

namespace Modules\ProvBase\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

use App\Http\Controllers\BaseModuleController;

class ProvBaseController extends BaseModuleController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'prov_server_ip', 'description' => 'Provisioning Server IP'),
			array('form_type' => 'text', 'name' => 'ro_community', 'description' => 'SNMP Read Only Community'),
			array('form_type' => 'text', 'name' => 'rw_community', 'description' => 'SNMP Read Write Community'),

			array('form_type' => 'text', 'name' => 'domain_name', 'description' => 'Domain Name for Modems'),
			array('form_type' => 'text', 'name' => 'notif_mail', 'description' => 'Notification Email Address'),
			
			array('form_type' => 'text', 'name' => 'startid_contract', 'description' => 'Start ID Contracts'),
			array('form_type' => 'text', 'name' => 'startid_modem', 'description' => 'Start ID Modems'),
			array('form_type' => 'text', 'name' => 'startid_endpoint', 'description' => 'Start ID Endpoints'),
			);
	}
	
}