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
	 * @var 	Is Set to "tabular" if Controlling View is built from html_frame strings with more than 2 letters in 1st form or "linear" in 2nd form
	 * 				"tabular" 					"linear"
	 			|-------------------| 		|-----------|
	 			| 11 | 12 | 13 		| 		| 1 | 2 | 3 |
	 			| 21 | 22 			| 		| 4 | 5 | 6 |
	 			| 31 | 32 | 33 | 34 | 		| 7 | 8 	|
	 			|-------------------| 		|-----------|
	 */
	private $view_mode = 'tabular';



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
	 * Returns the Controlling View for a NetElement (Device)
	 *
	 * @param id the NetElement id
	 * @author Torsten Schmidt, Nino Ryschawy
	 */
	public function controlling_edit($id)
	{
		// Init NetElement Model & SnmpController
		$netelem = NetElement::findOrFail($id);
		$snmp 	 = new SnmpController;
		$snmp->init ($netelem);

		// Get Html Form Fields for generic View - this includes the snmpwalk
		$form_fields = $snmp->prep_form_fields();
		$form_fields = BaseViewController::add_html_string($form_fields, $netelem);

		// set view mode
		$mode = $snmp->view_mode;

		// order by panel (respective html_frame), evaluate count of columns
		$panel_form_fields = [];
		foreach ($form_fields as $form_field)
			isset($panel_form_fields[$form_field['panel']]) ? $panel_form_fields[$form_field['panel']][] = $form_field : $panel_form_fields[$form_field['panel']][0] = $form_field;

		// evaluate count of columns for each row if mode is tabular
		if ($mode == 'tabular')
		{
			$frames = array_keys($panel_form_fields);
			foreach ($frames as $key)
			{
				$key = (string) $key;
				$columns[$key[0]] = $key[1];
			}

		}

// d($panel_form_fields, $mode, $columns);

		// Init View
		$model_name  = \NamespaceController::get_model_name();
		$view_header = 'Edit: '.$netelem->name;
		$view_var 	 = $netelem;
		$route_name  = \NamespaceController::get_route_name();
		$view_header_links = BaseViewController::view_main_menus();
		$headline 	 = BaseViewController::compute_headline(\NamespaceController::get_route_name(), $view_header, $view_var).' controlling';

		// $panel_right = $this->prepare_tabs($view_var);
		$view_path = 'hfcsnmp::NetElement.controlling';
		$form_path = 'Generic.form';
		$form_update = 'NetElement.controlling_update';

		return \View::make($view_path, $this->compact_prep_view(compact('model_name', 'view_var', 'view_header', 'form_path', 'panel_form_fields', 'form_update', 'route_name', 'view_header_links', 'headline', 'mode', 'columns')));
	}


	/**
	 * Controlling Update Function
	 *
	 * @param id the NetElement id
	 * @author Torsten Schmidt
	 */
	public function controlling_update($id)
	{
		// Init SnmpController
		$netelem = NetElement::findOrFail($id);
		$snmp = new SnmpController;
		$snmp->init ($netelem);

		$data = \Request::all();

		// TODO: validation
		// $validator = \Validator::make($data = $this->prepare_input(\Input::all()), $netelem::rules($id));
		// if ($validator->fails())
		// 	return Redirect::back()->withErrors($validator)->withInput();

		// Transfer Settings via SNMP to Device
		$snmp->snmp_set_all($data);

		return \Redirect::route('NetElement.controlling_edit', $id)->with('message', 'Updated!');
	}


	/**
	 * Prepare Formular Fields for Controlling View of NetElement
	 * This includes getting all SNMP Values from Device
	 * 
	 * @return 	Array (Multidimensional)	Data for Generic Form View in Form [frame1 => [field1, field2, ...], frame2 => [...], ...]
	 *
	 * @author Torsten Schmidt, Nino Ryschawy
	 */
	public function prep_form_fields()
	{
		$array  = [];
		$params = $this->device->netelementtype->parameters;

		// d($params);

		if (!$this->device->ip)
			return $array;


		foreach ($params as $param)
		{
			$oid  	 = $param->oid;
			$results = $this->snmp_walk($oid);

			// TODO: if device not reachable take already saved SnmpValues from Database
			// if (!$results) ...

			foreach ($results as $oid->res_oid => $value)
			{
				// Set SnmpValue
				$ret 	= $this->_snmp_value_set($oid, $value);

				// Compose Array with html options that is transformed to html string later
				$id 	 = $ret[0];
				$index   = $ret[1];
				$options = $param->oid->access == 'read-only' ? ['readonly'] : null;

				if ($param->type_array)
				{
					$options = $value;
					$value   = $this->string_to_array($param->type_array);
				}
// if ($oid->name_gui == 'Forward Equalization')
// 	d($value);
				$field = array(
					'form_type' 	=> $oid->html_type,
					'name' 			=> 'field_'.$id,	 		// = SnmpValue->id - TODO: Check if string 'field_' is necessary in front
					'description' 	=> $oid->name_gui ? $oid->name_gui.$index : $oid->name.$index,
					'field_value' 	=> $oid->unit_divisor && is_numeric($value) ? $value / $oid->unit_divisor : $value,
					'options' 		=> $options,
					'panel' 		=> $param->html_frame ? : '91', 		// default last row - only 1 column (second char!)
					// 'help' 			=> $oid->description,
					);

				if ($oid->html_type == 'select')
					$field['value'] = $oid->get_select_values();

				// set default linear mode, when even 1 field has a html frame with string length of 1
				if (strlen($field['panel'] < 2))
					$this->view_mode = 'linear';

				array_push($array, $field);
			}
		}

		return $array;
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
		$community = $this->_get_community();

		// Walk
		$walk = snmpwalkoid($this->device->ip, $community, $oid->oid, $this->timeout, $this->retry);

		// Log
		Log::info('snmp: get '.$this->snmp_log().' '.$oid->oid.' '.implode(' ',$walk));

		return $walk;
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
			'oid_index' 	=> ''
			);
		
		// $obj = SnmpValue::updateOrCreate($data); 		// doesnt work as is

		// compare resulting OID from snmpwalk with queried OID ... in case it's different we have a table with multiple elements & indexes
		if ($oid->oid.'.0' != $oid->res_oid)
		{
			$data['oid_index'] = str_replace($oid->oid, '', $oid->res_oid);

			$obj = SnmpValue::where('netelement_id', '=', $this->device->id)->where('oid_id', '=', $oid->id)->where('oid_index', '=', $data['oid_index'])->get()->first();
		}
		else
			$obj = SnmpValue::where('netelement_id', '=', $this->device->id)->where('oid_id', '=', $oid->id)->get()->first();

		if ($obj)
		{
			// always update to get the latest timestamp ??
			// $data['updated_at'] = \Carbon\Carbon::now(\Config::get('app.timezone'));
			$obj->update($data);

			return [$obj->id, $data['oid_index']];
		}

		return [SnmpValue::create($data), $data['oid_index']];
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
	 * Return the Community String for Read-Only or Read-Write Access
	 *
	 * @param 	access 	String 	'ro' or 'rw'
	 * @author 	Nino Ryschawy
	 */
	private function _get_community($access = 'ro')
	{
		return $this->device->{'community_'.$access} ? : \Modules\ProvBase\Entities\ProvBase::get([$access.'_community'])->first()->{$access.'_community'};
	}


	/**
	 * Perform a SNMP set of all SNMP Values for this Controller
	 *
	 * @param 	data 	the HTML data array in form: ['field_<SnmpValue ID>' => <value>]
	 * @return 	form_fields array for generic edit view function
	 *
	 * @author Torsten Schmidt
	 */
	public function snmp_set_all($data)
	{
		foreach ($data as $field => $value)
		{
			if (explode ('_', $field)[0] == 'field')
			{
				// explode data & write to Database
				$id  = explode ('_', $field)[1];
				$snmp_val = SnmpValue::findOrFail($id);

				// Set Value of Parameter in Database & Device only if it was changed in GUI
				if ($snmp_val->value != $value)
				{
					$snmp_val->value = $value;
					$snmp_val->save();

					// Set Value in Device via SNMP
					$this->snmp_set($snmp_val);
				}
			}
		}

		return true;
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
		$community  = $this->_get_community();
		$oid 		= $snmpvalue->oid;
		$index 		= $snmpvalue->oid_index ? : '.0';

		$ret = snmpget($this->device->ip, $community, $snmpvalue->oid->oid.$index, $this->timeout, $this->retry);

		if ($ret === FALSE)
			return FALSE;

		if ($ret == $snmpvalue->value)
			return TRUE;

		Log::info('snmp: set diff '.$this->snmp_log().' '.$snmpvalue->value.' '.$oid->type.' '.$snmpvalue->value.' '.$ret);

		// TODO: encapsulate in try-catch block and return appropriate error messages

		return snmpset($this->device->ip, $this->_get_community('rw'), $oid->oid.$index, $oid->type, $snmpvalue->value, $this->timeout, $this->retry);
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