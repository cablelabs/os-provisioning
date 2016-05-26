<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Cmts;
use Modules\ProvBase\Entities\IpPool;

class CmtsController extends \BaseModuleController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname'),
			array('form_type' => 'text', 'name' => 'type', 'description' => 'Type'),
			array('form_type' => 'text', 'name' => 'ip', 'description' => 'IP'),
			array('form_type' => 'text', 'name' => 'community_rw', 'description' => 'SNMP Private Community String'),
			array('form_type' => 'text', 'name' => 'community_ro', 'description' => 'SNMP Public Community String'),
			array('form_type' => 'text', 'name' => 'company', 'description' => 'Company'),
			array('form_type' => 'text', 'name' => 'state', 'description' => 'State'),
			array('form_type' => 'text', 'name' => 'monitoring', 'description' => 'Monitoring')
		);
	}

}
