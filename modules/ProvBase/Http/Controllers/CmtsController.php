<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Cmts;
use Modules\ProvBase\Entities\IpPool;

class CmtsController extends \BaseController {

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
			array('form_type' => 'select', 'name' => 'company', 'description' => 'Company', 'value' => ['Cisco' => 'Cisco', 'Casa' => 'Casa']),
			array('form_type' => 'text', 'name' => 'state', 'description' => 'State'),
			array('form_type' => 'text', 'name' => 'monitoring', 'description' => 'Monitoring')
		);
	}

	protected function get_form_tabs($view_var)
	{
		if(!\PPModule::is_active('ProvMon'))
			return [];

		return [
			['name' => 'Edit', 'route' => 'Cmts.edit', 'link' => [$view_var->id]],
			['name' => 'Analysis', 'route' => 'ProvMon.cmts', 'link' => [$view_var->id]],
			parent::get_form_tabs($view_var)[0]
		];
	}

}
