<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcReq\Entities\NetElement;
use Modules\HfcReq\Entities\NetElementType;
use Modules\HfcSnmp\Entities\SnmpValue;
use Modules\HfcSnmp\Entities\OID;
use \App\Http\Controllers\BaseViewController;
use Modules\HfcReq\Http\Controllers\NetElementController;


use Log;

class SnmpController extends \BaseController{

	private $timeout = 300000;
	private $retry = 1;

	private $device;

	/**
	 * @var  Array  of OID-Objects that threw an exception during SNMP-Set
	 */
	private $set_errors = [];


	/**
	 * @var 	Is Set to "tabular" if Controlling View is built from html_frame strings with more than 2 letters in 1st form or "linear" in 2nd form
	 * 				"tabular" 					"linear" 		"table"
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

		// Get Html Form Fields for generic View - this includes the snmpwalk & updating snmpvalues
		$form_fields = $snmp->prep_form_fields();

// d($form_fields);
		// TODO: use multi update function here
		// $this->_multi_update_values();

		$form_tables = $panel_form_fields = [];
		// TODO: give the opportunity for a mixture
		if (!isset($form_fields[0]['name']))
		{
			$form_tables = static::_make_html_snmp_table($form_fields);
		}
		else
		{
			// order by panel (respective html_frame), evaluate count of columns
			foreach ($form_fields as $form_field)
			{
				$form_field = BaseViewController::add_html_string(array($form_field))[0];

				if (isset($panel_form_fields[$form_field['panel']]))
					$panel_form_fields[$form_field['panel']][] = $form_field;
				else
					$panel_form_fields[$form_field['panel']][0] = $form_field;
			}

			ksort($panel_form_fields);
		}

// d($panel_form_fields);

		// set view mode - global var is set in prep_form_fields & get_formfield_array
		$mode = $snmp->view_mode;

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


		// Init View
		$view_header = 'Edit: '.$netelem->name;
		$view_var 	 = $netelem;
		$route_name  = \NamespaceController::get_route_name();
		$headline 	 = BaseViewController::compute_headline(\NamespaceController::get_route_name(), $view_header, $view_var).' controlling';

		$panel_right = new NetElementController;
		$panel_right = $panel_right->prepare_tabs($view_var);

		$view_path   = 'hfcsnmp::NetElement.controlling';
		$form_path   = 'Generic.form';
		$form_update = 'NetElement.controlling_update';

		return \View::make($view_path, $this->compact_prep_view(compact('view_var', 'view_header', 'form_path', 'panel_right', 'panel_form_fields', 'form_tables', 'form_update', 'route_name', 'headline', 'mode', 'columns')));
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

		// TODO: validation
		// $validator = \Validator::make($data = $this->prepare_input(\Input::all()), $netelem::rules($id));
		// if ($validator->fails())
		// 	return Redirect::back()->withErrors($validator)->withInput();


		// Transfer Settings via SNMP to Device
		$data = \Request::all();
		$snmp->snmp_set_all($data);


		// Build Error Message in case OIDs could not be set
		if ($snmp->set_errors)
		{
			$msg = 'The following Parameters could not be Set: ';
			$msg_color = 'red';

			foreach ($snmp->set_errors as $k => $oid)
			{
				$msg .= $k ? ', ' : '';
				$msg .= $oid->name_gui ? : $oid->name;
			}

			$msg .= '!';
		}
		else
		{
			$msg = 'Updated!';
			$msg_color = 'blue';
		}

		return \Redirect::route('NetElement.controlling_edit', $id)->with('message', $msg)->with('message_color', $msg_color);
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
		$table_index = 0;

		// d($params);

		if (!$this->device->ip)
			return [];

$start = microtime(true);

		foreach ($params as $param)
		{
			$oid  	 = $param->oid;
			$results = $oid->oid_table ? $this->snmp_table($oid) : $this->snmp_walk($oid);

			// TODO: if device not reachable take already saved SnmpValues from Database but show a hint
			// if (!$results) ...


			if ($oid->oid_table)
			{
				$table_index++;

				foreach ($results as $res_oid => $values)
				{
					$suboid = OID::where('oid', '=', $res_oid)->get()->first();

					if (!$suboid)
					{
						// this should never occur
						Log::error('Missing OID Entry from '.$oid->name.' for: '.$res_oid, [$this->device->devicetype->name]);
						continue;
					}

					foreach ($values as $index => $value)
					{
						// Save SnmpValue
						// NOTE: This takes way too much time
						// TODO: only add valueset to global variable for later multiupdate - for that we have to change indexing 
						// of field names because we wont get them as return value anymore
						$ret = $this->_snmp_value_set($suboid, $value, $index);

						// reorder by index for easier graphical display
						$array[$index][$res_oid] = $this->_get_formfield_array($suboid, $ret, $value, 'table_'.$table_index);
// if ($suboid->oid == '.1.3.6.1.2.1.10.127.1.1.1.1.2')
// 	d($suboid, $res_oid, $results, $array, $ret);
					}
				}

				$this->view_mode = 'linear';

				continue;
			}


			foreach ($results as $oid->res_oid => $value)
			{
				// Save SnmpValue
				$ret = $this->_snmp_value_set($oid, $value);

				array_push($array, $this->_get_formfield_array($oid, $ret, $value, $param->html_frame ? : '91'));
			}
		}
// d(round(microtime(true) - $start, 2). 'sec', $results, $array);
// var_dump("\nTime prepare form fields: ".round(microtime(true) - $start, 3));

		return $array;
	}


	/**
	 * Generate Form Field array as preparation for creating the html form fields from it
	 *
	 * @param ret 	Array 	Return Value from _snmp_value_set [0 => oid_id, 1 => 'oid_index']
	 */
	private function _get_formfield_array($oid, $ret, $value, $frame)
	{
		// Compose Array with html options that is transformed to html string later
		$id 	 = $ret[0];
		$index   = $ret[1] == '.0' ? '' : $ret[1];
		$options = $oid->access == 'read-only' ? ['readonly'] : null;

		// TODO: improve with assignment before
		if (strpos($frame, 'table_') !== false)
		{
			$index = '';
			// TODO: Move to get_html_input ??
			$options['style'] = 'simple';
		}

// if ($oid->name_gui == 'Lower Pilot Modulation')
// d($value, $field);

		$field = array(
			'form_type' 	=> $oid->html_type,
			'name' 			=> 'field_'.$id,	 		// = SnmpValue->id - TODO: Check if string 'field_' is necessary in front
			'description' 	=> $oid->name_gui ? $oid->name_gui.$index : $oid->name.$index,
			'field_value' 	=> $oid->unit_divisor && is_numeric($value) ? $value / $oid->unit_divisor : $value,
			'options' 		=> $options,
			'panel' 		=> $frame, 		// default last row - only 1 column (second char!)
			// 'help' 			=> $oid->description,
			);

		if ($oid->html_type == 'select')
			$field['value'] = $oid->get_select_values();

		// set default linear mode, when even 1 field has a html frame with string length of 1
		if (strlen($field['panel'] < 2))
			$this->view_mode = 'linear';

		return $field;
	}




	/**
	 * Create HTML Tables out of the list of prepared form fields
	 *
	 * @param 	Array 	List of all form fields
	 * @return 	Array 	of HTML Tables
	 *
	 * TODO: standardise and move to BaseViewController!
	 */
	private static function _make_html_snmp_table($form_fields)
	{
		$s 		= '';
		$new 	= true;
		$last 	= 'table_1';

		foreach ($form_fields as $index => $fields)
		{
			// end table if new one starts
			if ($last != $fields[key($fields)]['panel'])
			{
				$s .= '</tbody></table>';
				$new = true;
				$tables[$last] = $s;
				$s = '';
			}

			// start new table
			if ($new)
			{
				$s .= '<table class="table table-condensed">';
				$s .= '<thead><tr>';
				$s .= '<th>Index</th>';
				
				foreach ($fields as $oid => $field)
					$s .= '<th>'.$field['description'].'</th>';

				$s .= '<tr></thead><tbody>';

				$new = false;
				reset($fields);
			}

			// add table body data
			$s .= '<tr><td>'.$index.'</td>';

			foreach ($fields as $oid => $field)
				$s .= '<td>'.BaseViewController::get_html_input($field).'</td>';

			$last = $field['panel'];
			$s .= '</tr>';
		}

		// end last table
		$tables[$last] = $s.'</tbody></table>';

		return $tables;
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
		$walk = snmprealwalk($this->device->ip, $community, $oid->oid, $this->timeout, $this->retry);

		// Log
		Log::info('snmpwalk '.$this->device->ip.' '.$oid->oid);

		return $walk;
	}


	/**
	 * SNMP Walk over a Table Object
	 *
	 * @param 	OID Object
	 * @return 	Array	[index => [oid => value]]
	 */
	public function snmp_table($oid)
	{
$start = microtime(true);

		Log::info('snmp2_real_walk (table) '.$this->device->ip.' '.$oid->oid);

		$results = snmp2_real_walk($this->device->ip, $this->_get_community(), $oid->mibfile->name.'::'.$oid->name);
		// $results = snmp2_real_walk($this->device->ip, $this->_get_community(), "DOCS-IF-MIB::docsIfUpstreamChannelTable");
		// exec('snmptable -v2c -Ci -c'.$this->_get_community().' '.$this->device->ip.' '.escapeshellarg($oid->mibfile->name.'::'.$oid->name), $results);


		// order by suboid for faster snmpvalue saving (still too slow)
		foreach ($results as $oid_index => $value)
		{
			$index  = strrchr($oid_index, '.');
			$oid_s 	= str_replace($index, '', $oid_index);
			$index  = substr($index, 1);

			$res[$oid_s][$index] = $value;
		}

// var_dump("Time single SnmpWalk ".$oid->oid.": ".round(microtime(true) - $start, 3));
// d(round(microtime(true) - $start, 3), $oid, $res, $results);

		return $res;
	}


	/**
	 * Create or Update SnmpValue Object which corresponds
	 * to $oid and $this->device with $value
	 *
	 * @param 	oid 	the OID Object
	 * @param 	value 	from snmpget command for this oid object
	 * @return 	the ID of the SnmpValue Model
	 *
	 * @author Torsten Schmidt, Nino Ryschawy
	 */
	private function _snmp_value_set ($oid, $value, $index = '')
	{
		// $obj = SnmpValue::updateOrCreate($data); 		// doesnt work as is

		$data = array(
			'netelement_id' => $this->device->id,
			'oid_id' 		=> $oid->id,
			'value' 		=> $value,
			'oid_index' 	=> $index ? : str_replace($oid->oid, '', $oid->res_oid),
			);


		// // compare resulting OID from snmpwalk with queried OID ... in case it's different we have a table with multiple elements & indexes
		$obj = SnmpValue::where('netelement_id', '=', $this->device->id)->where('oid_id', '=', $oid->id)->where('oid_index', '=', $data['oid_index'])->get()->first();
		// $obj = $snmpvalues->where('oid_id', $oid->id)->where('oid_index', (string) $data['oid_index'])->first();

		if ($obj)
		{
			// always update to get the latest timestamp ??
			// $data['updated_at'] = \Carbon\Carbon::now(\Config::get('app.timezone'));
			// Note: update method needs id to update correct element
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
		$eager_loading_model = new OID;
		$snmpvalues = SnmpValue::where('netelement_id', '=', $this->device->id)->with($eager_loading_model->table)->get();
		$pre_conf = true; 			// true - has to be done

		foreach ($data as $field => $value)
		{
			$arr = explode('_', $field);
			if ($arr[0] == 'field')
			{
				// explode data & write to Database
				$id  = $arr[1];
				$snmp_val = $snmpvalues->find($id);

				// In GUI the value was divided by divisor - multiplicate back now for value comparison
				if ($snmp_val->oid->unit_divisor)
					$value *= $snmp_val->oid->unit_divisor;

				// Set Value of Parameter in Database & Device only if it was changed in GUI
				if ($snmp_val->value != $value)
				{
					// Do preconfiguration if necessary
					if ($pre_conf)
					{
						$conf_val = $this->_configure();
						$pre_conf = false;
					}

					$snmp_val->value = $value;
					$snmp_val->save();

					// Set Value in Device via SNMP
					$this->snmp_set($snmp_val);
				}
			}
		}

		if (isset($conf_val))
			$this->_configure($conf_val);

		return true;
	}


	/**
	 * Set the corresponding Values to Configure the Device for a successful snmpset (e.g. needed by kathrein amplifiers)
	 * NOTE: If Value is specified the post configuration is done
	 *
	 * @param 	value   the value of the Parameter before the Configuration to reset
	 * @return 	value of Parameter before the configuration, null when resetting the Parameter to this value (specified in argument)
	 */
	private function _configure($value = null)
	{
		$type = $this->device->netelementtype;

		if (!$type->pre_conf_oid_id || !$type->pre_conf_value)
		{
			\Log::debug('No SNMP Preconfiguration defined for this Device (NetElement)', [$this->device->name]);
			return null;
		}

		$oid = $type->oid;

		// PreConfiguration
		if (!$value)
		{
			$conf_val = snmpget($this->device->ip, $this->_get_community(), $oid->oid.'.0', $this->timeout, $this->retry);

			$ret = false;
			if ($conf_val != $type->pre_conf_value)
				$ret = snmpset($this->device->ip, $this->_get_community('rw'), $oid->oid.'.0', $oid->type, $type->pre_conf_value, $this->timeout, $this->retry);

			$ret ? \Log::debug('Preconfigured Device for snmpset', [$this->device->name]) : \Log::debug('Failed to Preconfigure Device for snmpset', [$this->device->name]);

			return $conf_val;
		}

		// PostConfiguration
		snmpset($this->device->ip, $this->_get_community('rw'), $oid->oid.'.0', $oid->type, $value, $this->timeout, $this->retry);

		// wait time in msec
		$sleep_time = $type->pre_conf_time_offset ? : 0;
		usleep($sleep_time);

		return null;
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

		Log::info('snmp: set diff '.$this->device->ip.' '.$snmpvalue->value.' '.$oid->type.' '.$snmpvalue->value.' '.$ret);

		// catch all OIDs that could not be set to print later in error message
		try {
			$val = snmpset($this->device->ip, $this->_get_community('rw'), $oid->oid.$index, $oid->type, $snmpvalue->value, $this->timeout, $this->retry);
		} catch (\ErrorException $e) {
			$this->set_errors[] = $oid;
			return null;
		}

		return $val;
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


	// private function _multi_update_values()
	// {

	// 	/* NOTES: SQL Changes for multiple primary keys:
	// 		ALTER TABLE snmpvalue DROP PRIMARY KEY, ADD PRIMARY KEY(netelement_id, oid_id, oid_index);
	// 	 */

	// 	$data = array(
	// 		['netelement_id' => 28,
	// 		'oid_id' 		=> 760,
	// 		'value' 		=> 12,
	// 		'oid_index' 	=> '.0'],
	// 		['netelement_id' => 28,
	// 		'oid_id' 		=> 757,
	// 		'value' 		=> -100,
	// 		'oid_index' 	=> '.0'],
	// 		);
	
	// 	\DB::raw('insert into snmpvalue (netelement_id, oid_id, oid_index, value) VALUES (28, 760, 11, \'.0\'), (28, 757, -10, \'.0\') on duplicate key update value=VALUES(value)');
	// }

}