<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcReq\Entities\NetElement;
use Modules\HfcSnmp\Entities\SnmpValue;
use Modules\HfcSnmp\Entities\OID;
use \App\Http\Controllers\BaseViewController;


use Log;

class SnmpController extends \BaseController{

	private $timeout = 300000;
	private $retry = 1;

	private $device;


	/**
	 * Init SnmpController with a certain Device Model and
	 * a MIB Array
	 *
	 * @param device the Device Model
	 * @param mibs the MIB array
	 *
	 * @author Torsten Schmidt
	 */
	public function init ($device = null)
	{
		$this->device = $device;

		$this->snmp_def_mode();
	}

	/**
	 * Controlling Read Function
	 *
	 * TODO: split SNMP Stuff from netelem specific stuff
	 *       and do not return a View -> instead call BaseController@edit
	 *
	 * @param id the NetElement id
	 * @author Torsten Schmidt
	 */
	public function controlling_edit($id)
	{
		// Init NetElement Model
		$netelem = NetElement::findOrFail($id);

		// Init SnmpController
		$snmp = new SnmpController;
		$snmp->init ($netelem);

		// Get Html Form Fields for generic View
		$fields = $snmp->prep_form_fields();
		// $form_fields = $snmp->make_html($fields);
		$form_fields = BaseViewController::add_html_string($fields, $netelem);


		// Init View
		// $obj = static::get_model_obj();
		$model_name  = \NamespaceController::get_model_name();
		$view_header = 'Edit: '.$netelem->name;
		// $view_var 	 = $obj->findOrFail($id);
		$view_var 	 = $netelem;
		$route_name  = \NamespaceController::get_route_name();
		$view_header_links = BaseViewController::view_main_menus();

		$view_path = 'hfcsnmp::NetElement.controlling';
		$form_path = 'Generic.form';
		$form_update = 'NetElement.controlling_update';

		//dd(compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields', 'form_update'));


		return \View::make($view_path, $this->compact_prep_view(compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields', 'form_update', 'route_name', 'view_header_links')));
	}


	/**
	 * Controlling Update Function
	 *
	 * @param id the NetElement id
	 * @author Torsten Schmidt
	 */
	public function controlling_update($id)
	{
		$netelem = NetElement::findOrFail($id);

		// TODO: validation
		$validator = \Validator::make($data = $this->prepare_input(\Input::all()), $netelem::rules($id));

/*
		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
*/

		// Init SnmpController
		$snmp = new SnmpController;
		$snmp->init ($netelem);

		// Set Html Form Fields for generic View

		$snmp->snmp_set_all($data);


		return \Redirect::route('NetElement.controlling_update', $id)->with('message', 'Updated!');
	}



	/**
	 * Create or Update SnmpValue Object which corresponds
	 * to $oid and $this->device with $value
	 *
	 * @param 	oid the OID Object
	 * @param 	value from snmpget command for this oid object
	 * @return 	the ID of the SnmpValue Model
	 *
	 * @author Torsten Schmidt, Nino Ryschawy
	 */
	private function _snmp_value_set ($oid, $value)
	{
		$data = array(
			'netelement_id' => $this->device->id,
			'oid_id' 		=> $oid->id,
			'value' 		=> $value,
			);

		$obj = SnmpValue::updateOrCreate($data);

		return $obj->id;
	}


	/**
	 * Create or Update SnmpValue Object which corresponds
	 * to $oid and $this->device with $value
	 *
	 * @param string encoded array like "3:qam64;4:qam256"
	 * @return encoded array of string like [3]=>"qam64", [4]=>"qam256"
	 * @author Torsten Schmidt
	 */
	private function string_to_array ($s)
	{
		$ret = array();
		foreach (explode (';', $s) as $line)
			$ret[explode(':', $line)[0]] = explode(':', $line)[1];
		return $ret;
	}


	/**
	 * The SNMP Walk Function
	 *
	 * make a snmpwalk over the entire $oid->oid
	 * and create/update related SnmpValue Objects
	 *
	 * @param oid the OID Object
	 * @return array of snmpwalk over oid in format [SnmpValue object id, snmp value]
	 *
	 * @author Torsten Schmidt
	 */
	public function snmp_walk ($oid)
	{
		$community = $this->device->community_ro ? : \Modules\ProvBase\Entities\ProvBase::get(['ro_community'])->first()->ro_community;

		// Walk
		$walk = snmpwalkoid($this->device->ip, $community, $oid->oid, $this->timeout, $this->retry);

		// Log
		Log::info('snmp: get '.$this->snmp_log().' '.$oid->oid.' '.implode(' ',$walk));

		// Fetch Walk and write result to SnmpValue Objects (DB)
		$ret = array();

		foreach ($walk as $oid->oid => $v)
		{
			// if (!$v)
			// 	return false;

			$b = $this->_snmp_value_set($oid, $v);

			if (!$b)
				return false;

			array_push($ret, [$b, $v]);
		}

		return $ret;
	}


	/**
	 * The SNMP Set Function
	 *
	 * snmpset the $snmpvalue object with $snmpvalue->value
	 *
	 * Note: performs snmpsetdiff
	 *
	 * @param snmpvalue the SnmpValue Object
	 * @return true if success, otherwise false
	 *
	 * @author Torsten Schmidt
	 */
	public function snmp_set ($snmpvalue)
	{
		$x = snmpget ($this->device->ip, $this->device->community_ro, $snmpvalue->oid_index, $this->timeout, $this->retry);

		var_dump(' ', $x);

		if ($x === FALSE)
			return FALSE;

		if ($x == $snmpvalue->value)
			return TRUE;

		$snmpmib = OID::findOrFail($snmpvalue->snmpmib_id);

		Log::info('snmp: set diff '.$this->snmp_log().' '.$snmpvalue->oid_index.' '.$snmpmib->type.' '.$snmpvalue->value.' '.$x);
		// return snmpset($this->device->ip, $this->device->community_rw, $oid, $type, $value, $this->timeout, $this->retry);
	}



	/**
	 * Prepare Formular Fields for Controlling View of NetElement
	 * This includes getting all SNMP Values from Device
	 * 
	 * @return 	Array 	Data for Generic Form View
	 *
 	 * @author Torsten Schmidt, Nino Ryschawy
	 */
	public function prep_form_fields()
	{
		$ret  = [];
		$oids = $this->device->netelementtype->oids;

		if (!$this->device->ip)
			return $ret;

    	foreach ($oids as $oid)
    	{
    		foreach ($this->snmp_walk($oid) as $a)
    		{
    			// d($a, $oid);
    			$options = null;
    			$value = $a[1];

    			if($oid->type_array)
    			{
    				$options = $a[1];
    				$value = $this->string_to_array($oid->type_array);
    			}

    			$field = array(
    				'form_type' 	=> $oid->html_type,
    				'name' 			=> 'field_'.$a[0],
    				'description' 	=> $oid->name,
    				// 'description' 	=> '<a href="'.route('OID.edit', ['id' => $oid->id]).'">'.$oid->name.'</a>',
    				'field_value' 	=> $value,
    				'options' 		=> $options
    				);

    			array_push($ret, $field);
    		}
    	}

    	return $ret;
	}



	/**
	 * Perform a SNMP set of all SNMP Values for this Controller
	 *
	 * @param data the HTML data array in form array of ['field_<SnmpValue ID>' => <value>]
	 * @return form_fields array for generic edit view function
	 *
	 * @author Torsten Schmidt
	 */
    public function snmp_set_all($data)
    {
    	foreach ($data as $field => $value)
    	{
    		var_dump($value);
    		if (explode ('_', $field)[0] == 'field')
    		{
    			// explode data
    			$id = explode ('_', $field)[1];
	    		$snmpmib = SnmpValue::findOrFail($id);
	    		$snmpmib->value = $value;
	    		$snmpmib->save();

	    		// The SET command
	    		$this->snmp_set($snmpmib);
    		}
    	}

    	return true;
    }



    private function snmp_log()
    {
    	return $this->device->ip;
    }


	/**
	 * Set PHP SNMP Default Values
	 * Note: Must be only called once per Object Init
	 *
	 * @author Torsten Schmidt
	 */
	private function snmp_def_mode()
	{
        snmp_set_quick_print(TRUE);
        snmp_set_oid_numeric_print(TRUE);
        snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
        snmp_set_oid_output_format (SNMP_OID_OUTPUT_NUMERIC);
	}


}