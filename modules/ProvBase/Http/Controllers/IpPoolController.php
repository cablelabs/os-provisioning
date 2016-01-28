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
			array('form_type' => 'textarea', 'name' => 'optional', 'description' => 'Additional Options'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description')
		);
	}

    /**
     * Replaces the placeholders (named like the array key inside the data array/sql columns)
     * in the rules array with the needed data of the data array;
     *
     * used in own validation
     *
     * @author Nino Ryschawy
     */
	public function prep_rules($rules, $data)
	{
		foreach ($rules as $rkey => $description)
		{
			foreach ($data as $key => $value)
			{
				// search for key of data array in rule descriptions
				if (($pos = strpos($description, $key)) && substr($description, $pos-1, 1) != "|")
				{
					$rules[$rkey] = $description = preg_replace("/$key\b/", "$value", $description);
					// $rules[$rkey] = substr_replace($description,$value,$pos,strlen($key));	// replaces only once (not like str_replace)
					// $rules[$rkey] = str_replace($key, $value, $description);
				}
			}
		}
		// dd($rules, $data);
		return $rules;
	}

}
