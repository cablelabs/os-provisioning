<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcReq\Entities\NetElement;
use Modules\HfcReq\Entities\NetElementType;
use Modules\HfcSnmp\Entities\SnmpValue;
use Modules\HfcSnmp\Entities\OID;
use Modules\HfcSnmp\Entities\Parameter;
use \App\Http\Controllers\BaseViewController;
use Modules\HfcReq\Http\Controllers\NetElementController;


use Log;

class SnmpController extends \BaseController{

	private $timeout = 300000;
	private $retry = 1;

	/**
	 * @var  Object 	NetElement
	 */
	private $device;

	/**
	 * @var  Array  of OID-Objects that threw an exception during SNMP-Set
	 */
	private $set_errors = [];


	/**
	 * @var  Bool 	If Set we only want to show the 3rd dimension parameters of this index for the controlling view
	 */
	private $index = 0;


	/**
	 * Init SnmpController with a certain Device Model and
	 * a MIB Array
	 *
	 * @param device the Device Model
	 * @param mibs the MIB array
	 *
	 * @author Torsten Schmidt
	 */
	public function init ($device = null, $index = 0)
	{
		$this->device = $device;
		$this->index = $index ? [$index] : 0;

		$this->snmp_def_mode();
	}

	/**
	 * Returns the Controlling View for a NetElement (Device)
	 *
	 * Note: This function is used again for the 3rd Dimension of a Snmp Table (of which the Index link references to)
	 *
	 * @param 	id  		The NetElement id
	 * @param 	param_id 	ID of the Parameter for 3rd Dimension View
	 * @param 	index 		The Index we want to see 3rd Dim for
	 * @author 	Torsten Schmidt, Nino Ryschawy
	 */
	public function controlling_edit($id, $param_id = 0, $index = 0)
	{
		// Init NetElement Model & SnmpController
		$netelem = NetElement::findOrFail($id);
		$this->init ($netelem, $index);

		// GET SNMP values of NetElement
		// TODO: check if netelement has a netelementtype -> exception for root elem
		$params = $index ?
			Parameter::where('parent_id', '=', $param_id)->where('third_dimension', '=', 1)->with('oid')->orderBy('id')->get()
			:
			$this->device->netelementtype->parameters()->with('oid')->orderBy('html_frame')->orderBy('html_id')->orderBy('oid_id')->orderBy('id')->get();

		try {
			$form_fields = $this->get_snmp_values($params, true);
		} catch (\Exception $e) {
			return self::handle_exception($e);
		}

		// Init View
		$view_header = 'SNMP Settings: '.$netelem->name;
		$view_var 	 = $netelem;
		$route_name  = \NamespaceController::get_route_name();
		$headline 	 = BaseViewController::compute_headline(\NamespaceController::get_route_name(), $view_header, $view_var).' > controlling';

		$panel_right = new NetElementController;
		$panel_right = $panel_right->prepare_tabs($view_var);

		$view_path   = 'hfcsnmp::NetElement.controlling';
		$form_path   = 'Generic.form';
		$form_update = 'NetElement.controlling_update';

		$reload 	 = $this->device->netelementtype->page_reload_time ? : 0;

		return \View::make($view_path, $this->compact_prep_view(compact('view_var', 'view_header', 'form_path', 'panel_right', 'form_fields', 'form_update', 'route_name', 'headline', 'reload', 'param_id', 'index')));
	}


	/**
	 * Controlling Update Function
	 *
	 * @param id the NetElement id
	 * @author Torsten Schmidt
	 */
	public function controlling_update($id, $param_id = 0, $index = 0)
	{
		// Init SnmpController
		$netelem = NetElement::findOrFail($id);
		$snmp = new SnmpController;
		$snmp->init ($netelem);

		// TODO: validation
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

		return \Redirect::route('NetElement.controlling_edit', [$id, $param_id, $index])->with('message', $msg)->with('message_color', $msg_color);
	}


	/**
	 * GET all SNMP values from device
	 *
	 * @param Array 	params 		Array of Parameter Objects
	 * @param Bool 		ordered 	true:  @return SNMP values as structured array to build initial view
	 * 								false: @return raw json data to update values via Ajax
	 * @author Nino Ryschawy
	 */
	public function get_snmp_values($params, $ordered = false)
	{
		$results_tot = $ordered ? ['list' => [], 'frame' => ['linear' => [], 'tabular' => []], 'table' => []] : [];
		$values_to_store = [];
		$table_id = 0;

		// Get stored Snmpvalues
		$dir_path_rel = "data/hfc/snmpvalues/".$this->device->id;
		$old_vals = \Storage::exists($dir_path_rel) ? json_decode(\Storage::get($dir_path_rel)) : [];

		// TODO: if device not reachable take already saved SnmpValues from Database but show a hint - check via snmpget !?
		if (!$this->device->ip)
			return [];

		foreach ($params as $param)
		{
			$indices = $this->index ? : [];

			if (!$indices) {
				$indices_o = $param->indices()->where('netelement_id', '=', $this->device->id)->first();
				$indices = $indices_o ? explode(',', $indices_o->indices) : [];
			}

			// Table Param
			if ($param->oid->oid_table)
			{
				$table_id++;

				$results = $this->snmp_table($param, $indices);
				$values_to_store = array_merge($values_to_store, $results);

				$subparam = null;
				foreach ($results as $oid => $value)
				{
					$index  = strrchr($oid, '.'); 								// row in table
					$suboid = substr($oid, 0, strlen($oid) - strlen($index)); 	// column in table

					// Note: Subparams are already fetched from DB in snmp_table() with joined OID
					if (!$subparam || $subparam->oid != $suboid)
						$subparam = $param->children ? $param->children->where('oidoid', $suboid)->first() : OID::where('oid', '=', $suboid)->first();
					if (!$subparam) {
						\Log::error('SNMP Query returned OID that is missing in database!');
						continue;
					}

					$value = self::_build_diff_and_divide($subparam, $index, $results, $value, isset($old_vals->{$oid}) ? $old_vals->{$oid} : 0);

					// order results for initial view
					if ($ordered)
					{
						// set table head only once
						if (!isset($results_tot['table'][$table_id]['head'][$suboid]))
							$results_tot['table'][$table_id]['head'][$suboid] = $subparam->oid->name_gui ? $subparam->oid->name_gui : $subparam->oid->name;

						$arr = self::_get_formfield_array($subparam->oid, $index, $value, true);
						$field = BaseViewController::get_html_input($arr);

						$results_tot['table'][$table_id]['body'][$index][$suboid] = $field;
					}
					else
						$results_tot[$oid] = $value;
				}

				if ($ordered && $param->third_dimension_params()->count())
					$results_tot['table'][$table_id]['3rd_dim'] = ['netelement_id' => $this->device->id, 'param_id' => $param->id];
			}
			// Non Table Param - can not have subparams
			else
			{
				$results = $this->snmp_walk($param->oid->oid, $indices);
				$values_to_store = array_merge($values_to_store, $results);

				// Calculate differential param
				foreach ($results as $oid => $value)
				{
					$index  = strrchr($oid, '.'); 								// row in table
					$suboid = substr($oid, 0, strlen($oid) - strlen($index)); 	// column in table
					// join relevant information before calling diff function
					$value = self::_build_diff_and_divide($param, $index, $results, $value, isset($old_vals->{$oid}) ? $old_vals->{$oid} : 0);

					// order results for initial view
					if ($ordered)
					{
						$arr = self::_get_formfield_array($param->oid, $index, $value);
						$field = BaseViewController::add_html_string([$arr])[0]['html'];

						if (!$param->html_frame)
							$results_tot['list'][] = $field;
						else if (strlen((string) $param->html_frame) == 1)
							$results_tot['frame']['linear'][$param->html_frame][] = $field;
						else {
							// e.g.: '12' -> row 1, column 2
							$frame = (string) $param->html_frame;
							$results_tot['frame']['tabular'][$frame[0]][$frame[1]][] = $field;
						}
					}
					else
						$results_tot[$oid] = $value;
				}
			}
		} // end foreach

		// store snmp values
		\Storage::put($dir_path_rel, json_encode($values_to_store));

		return $ordered ? $results_tot : json_encode($results_tot);
	}


	/**
	 * Determine resulting value dependent of unit divisor or other OID values (see source code descriptions)
	 *
	 * @param Object 	param 	Parameter
	 * @param String 	index 	last number of OID
	 * @param Array 	results
	 * @param String|Integer 	value 		current value from snmpwalk
	 * @param String|Integer 	old_value 	value from last snmpwalk (to possibly calculate the difference)
	 *
	 * @author Nino Ryschawy
	 */
	private static function _build_diff_and_divide($param, &$index, &$results, $value, $old_value)
	{
		// Subtract old value from new value
		if ($param->diff_param)
			$value -= $old_value;

		// divide value by value of other oid or sum of values of multiple OIDs and make it percentual
		if ($param->divide_by)
		{
			if (!is_array($param->divide_by))
				$param->divide_by = \Acme\php\ArrayHelper::str_to_array($param->divide_by);

			$divisor = 0;
			foreach ($param->divide_by as $divisor_oid)
				$divisor += $results[$divisor_oid.$index];

			$value = $divisor ? round($value / $divisor * 100, 2) : $value;
		}
		// divide value by fix number (e.g. to change the power(Potenz) of the value)
		else if ($param->oid->unit_divisor && is_numeric($value))
			$value /= $param->oid->unit_divisor;

		return $value;
	}


	/**
	 * Generate Form Field array as preparation for creating the html form fields from it
	 *
	 * @param Object 	OID
	 * @param String 	index 	Last number of OID (with starting dot)
	 * @param String|Int value
	 * @param Bool 		table
	 */
	private static function _get_formfield_array($oid, $index, $value, $table = false)
	{
		$options = null;

		if ($table) {
			$options['style'] = 'simple';
			$options['style'] .= in_array($oid->type, ['i', 'u', 't']) ? ";width: 85px;" : '';
		}

		if ($oid->access == 'read-only')
			$options[] = 'readonly';

		// description of table is set only once for table head
		$ext = $index == '.0' ? '' : $index;
		$description = $table ? '' : ($oid->name_gui ? $oid->name_gui.$ext : $oid->name.$ext);

		$field = array(
			'form_type' 	=> $oid->html_type,
			'name' 			=> $oid->oid.$index,
			'description' 	=> $description,
			'field_value' 	=> $value,
			'options' 		=> $options,
			// 'help' 			=> $oid->description,
			);

		if ($oid->html_type == 'select')
			$field['value'] = $oid->get_select_values();

		return $field;
	}


	/**
	 * The SNMP Walk Function
	 *
	 * make a snmpwalk over the entire $oid->oid
	 * and create/update related SnmpValue Objects
	 *
	 * NOTE: snmp2 is minimum 20 times faster for several snmpwalks
	 *
	 * @param 	String 	SNMP Object Identifier
	 * @return 	Array 	of snmpwalk over oid in format [SnmpValue object id, snmp value]
	 *
	 * @author Torsten Schmidt, Nino Ryschawy
	 */
	public function snmp_walk ($oid, $indices = [])
	{
		$community = $this->_get_community();

		// Log
		Log::debug('snmpwalk '.$this->device->ip.' '.$oid);

		if ($indices)
		{
			try {
				// check if snmp version 2 is supported - use it - otherwise use version 1
				snmp2_get($this->device->ip, $community, '1.3.6.1.2.1.1.1', $this->timeout, $this->retry);

				foreach ($indices as $index)
					$results[$oid.'.'.$index] = snmp2_get($this->device->ip, $community, $oid.'.'.$index, $this->timeout, $this->retry);
			}
			catch (\Exception $e) {
				foreach ($indices as $index)
					$results[$oid.'.'.$index] = snmpget($this->device->ip, $community, $oid.'.'.$index, $this->timeout, $this->retry);
			}
		}
		else
		{
			try {
				$results = snmp2_real_walk($this->device->ip, $community, $oid, $this->timeout, $this->retry);
			} catch (\Exception $e) {
				$results = snmpwalk($this->device->ip, $community, $oid, $this->timeout, $this->retry);
			}
		}

		return $results;
	}


	/**
	 * SNMP Walk over a Table OID Parameter - Can also be a walk over all it's SubOIDs
	 *
	 * @param 	param 	Table Object ID
	 * @return 	Array	[values => [index => [oid => value]], [diff-OIDs]]
	 *
	 * @author 	Nino Ryschawy
	 */
	public function snmp_table($param, $indices)
	{
		$oid = $param->oid;
		$results = $res = $diff_param = $divisions = [];
		$relation = $param->children()
			->where('third_dimension', '=', $this->index ? 1 : 0)
			->with('oid')
			->join('oid as o', 'o.id', '=', 'parameter.oid_id')
			->select('parameter.*', 'o.oid as oidoid')
			->orderBy('third_dimension')->orderBy('html_id')->orderBy('parameter.id')->get();

		$param->setRelation('children', $relation);

		// exact defined table via SubOIDs
		if ($param->children)
		{
			foreach ($param->children as $param) {
				// Note: snmpwalk -CE ends on this OID - makes it much faster
				// exec('snmpwalk -v2c -CE 1.3.6.1.2.1.10.127.1.1.1.1.3.6725 -c'.$this->_get_community().' '.$this->device->ip.' '.$oid->oid, $results);
				$results += $this->snmp_walk($param->oid->oid, $indices);
			}
		}
		// standard table OID (all suboids(columns) and elements (rows))
		else {
			Log::debug('snmp2_real_walk (table) '.$this->device->ip.' '.$oid->oid);
			$results = snmp2_real_walk($this->device->ip, $this->_get_community(), $oid->oid);
		}

		if (!$results)
			Log::error('No Results for SnmpWalk over OID: '.$oid->oid); // Possible Reasons: wrong defined indices, device does not support oid

		return $results;
	}


	/**
	 * Create or Update SnmpValue Object which corresponds
	 * to $oid and $this->device with $value
	 *
	 * @param 	oid 	Object 		the OID Object
	 * @param 	value 	String 		from snmpget command for this oid object
	 * @param 	index 	String 		OID Index if already known (from table)
	 * @return 	Array 	[SnmpValue_id, 		// as reference in view - for snmp set
	 *					 OID-Index, 		// in case a walk over oid produces multiple results indexed by this
	 *					 Value-before] 		// Last Value for Difference Calculation
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

		$obj = SnmpValue::where('netelement_id', '=', $this->device->id)->where('oid_id', '=', $oid->id)->where('oid_index', '=', $data['oid_index'])->get()->first();
		// $obj = $snmpvalues->where('oid_id', $oid->id)->where('oid_index', (string) $data['oid_index'])->first();

		if ($obj)
		{
			$last_val = $obj->value;
			// always update to get the latest timestamp ??
			// $data['updated_at'] = \Carbon\Carbon::now(\Config::get('app.timezone'));
			// Note: update method needs id to update correct element
			$obj->update($data);

			return [$obj->id, $data['oid_index'], $last_val];
		}

		return [SnmpValue::create($data)->id, $data['oid_index'], null];
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
		$pre_conf = $this->device->netelementtype->pre_conf_value ? true : false; 			// true - has to be done
		foreach ($data as $field => $value)
		{
			$arr = explode('_', $field);

			if ($arr[0] != 'field')
				continue;

			// explode data & write to Database
			$id  = $arr[1];
			$snmp_val = $snmpvalues->find($id);

			// In GUI the value was divided by divisor - multiplicate back now for value comparison
			if ($snmp_val->oid->unit_divisor)
				$value *= $snmp_val->oid->unit_divisor;

			// Set Value of Parameter in Database & Device only if it was changed in GUI
			if ($snmp_val->value == $value)
				continue;

			// Do preconfiguration only once if necessary
			if ($pre_conf)
			{
				$conf_val = $this->_configure();
				$pre_conf = false;
			}

			$snmp_val->observer_enabled = true;			// enable observer for GuiLogs when a value is manually set
			$snmp_val->value = $value;
			$snmp_val->save();

			// Set Value in Device via SNMP
			$this->snmp_set($snmp_val);
		}

		// Do postconfig if preconfig was done
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
	 *
	 * @author 	Nino Ryschawy
	 */
	private function _configure($value = null)
	{
		$type = $this->device->netelementtype;

		if ($type->pre_conf_oid_id xor $type->pre_conf_value)
		{
			\Log::debug('Snmp Preconfiguration settings incomplete for this Device (NetElement)', [$this->device->name, $this->device->id]);
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

			$ret ? \Log::debug('Preconfigured Device for snmpset', [$this->device->name, $this->device->id]) : \Log::debug('Failed to Preconfigure Device for snmpset', [$this->device->name, $this->device->id]);

			// wait time in msec
			$sleep_time = $type->pre_conf_time_offset ? : 0;
			usleep($sleep_time);

			return $conf_val;
		}

		// PostConfiguration
		snmpset($this->device->ip, $this->_get_community('rw'), $oid->oid.'.0', $oid->type, $value, $this->timeout, $this->retry);

		\Log::debug('Postconfigured Device for snmpset', [$this->device->name, $this->device->id]);


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

		// $ret = snmpget($this->device->ip, $community, $oid->oid.$index, $this->timeout, $this->retry);


		// catch all OIDs that could not be set to print later in error message
		try {
			$val = snmpset($this->device->ip, $this->_get_community('rw'), $oid->oid.$index, $oid->type, $snmpvalue->value, $this->timeout, $this->retry);
		} catch (\ErrorException $e) {
// d($e, $this->device->ip, $this->_get_community('rw'), $oid->oid.$index, $oid->type, $snmpvalue->value);
			$this->set_errors[] = $oid;
			Log::error('snmpset failed with msg: '.$e->getMessage(), [$this->device->ip, $community, $oid->type, $snmpvalue->value]);
			return null;
		}

		Log::debug('snmp: set diff '.$this->device->ip.' '.$community.' '.$oid->oid.$index.' '.$snmpvalue->value.' '.$oid->type.' '.$val);

		if ($val === FALSE)
			return FALSE;

		if ($val == $snmpvalue->value)
			return TRUE;

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


	/**
	 * Returns the appropriate View dependent on the thrown Exception
	 */
	private static function handle_exception(\Exception $e)
	{
		$msg = $e->getMessage();

		// Wrong index specified
		if (strpos($msg, 'snmp2_get') !== false && strpos($msg, 'No Such Instance currently exists') !== false)
		{
			$oid = substr($msg, $start = (strpos($msg, '\'') + 1), strpos(substr($msg, $start + 1), '\'') + 1);

			$index = strrchr($oid, '.');
			$oid   = substr($oid, 0, strlen($oid) - strlen($index));
			$index = substr($index, 1);

			$error = 'snmp_get() failed';
			$message = "There's no Index '$index' for this OID '$oid' on this NetElement! Change this Index please!";

			return \View::make('errors.generic', compact('message', 'error'));
		}

		// Device not reachable/online
		if (strpos($msg, 'snmp') !== false && (($x = strpos($msg, 'No response from')) !== false))
		{
			$ip = substr($msg, $x + 16, 15);
			$method = explode(':', $msg)[0];

			$error = "$method failed";
			$message = "Device with IP $ip not reachable";

			return \View::make('errors.generic', compact('message', 'error'));
		}

		throw $e;
	}

}
