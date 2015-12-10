<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Cmts;
use Modules\ProvBase\Entities\IpPool;

class IpPoolController extends \BaseModuleController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		if (!$model)
			$model = new IpPool;

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'cmts_id', 'description' => 'CMTS Hostname', 'value' => $model->html_list($model->cmts_hostnames(), 'hostname')),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => array( 'CM' => 'Cable Modem', 'CPEPriv' => 'CPE Private', 'CPEPub' => 'CPE Public', 'MTA' => 'MTA')),
			array('form_type' => 'text', 'name' => 'net', 'description' => 'Net'),
			array('form_type' => 'text', 'name' => 'netmask', 'description' => 'Netmask'),
			array('form_type' => 'text', 'name' => 'ip_pool_start', 'description' => 'First IP'),
			array('form_type' => 'text', 'name' => 'ip_pool_end', 'description' => 'Last IP'),
			array('form_type' => 'text', 'name' => 'router_ip', 'description' => 'Router IP'),
			array('form_type' => 'text', 'name' => 'broadcast_ip', 'description' => 'Broadcast IP'),
			array('form_type' => 'text', 'name' => 'dns1_ip', 'description' => 'DNS1 IP'),
			array('form_type' => 'text', 'name' => 'dns2_ip', 'description' => 'DNS2 IP'),
			array('form_type' => 'text', 'name' => 'dns3_ip', 'description' => 'DNS3 IP'),
			array('form_type' => 'textarea', 'name' => 'optional', 'description' => 'Additional Options')
		);
	}

}
