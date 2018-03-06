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
		$params = $this->_get_parameter($param_id);

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
	 * @param Integer id the NetElement id
	 * @param Integer param_id, index 	just for Redirect
	 *
	 * @author Torsten Schmidt, Nino Ryschawy
	 */
	public function controlling_update($id, $param_id = 0, $index = 0)
	{
		// Init SnmpController
		$netelem = NetElement::findOrFail($id);
		$this->init ($netelem, $index);

		// TODO: validation
		// Transfer Settings via SNMP to Device
		$this->snmp_set_all(\Request::all());

		$msg = 'Updated!';
		$msg_color = 'blue';

		// Set Error Message in case some OIDs could not be set
		if ($this->set_errors) {
			$msg = 'The following Parameters could not be Set: '.implode(', ', $this->set_errors).'!';
			$msg_color = 'red';
		}

		return \Redirect::route('NetElement.controlling_edit', [$id, $param_id, $index])->with('message', $msg)->with('message_color', $msg_color);
	}


	/**
	 * Returns updated SnmpValues via client opened TCP connection (SSE)
	 */
	public function sse_get_snmpvalues($netelem_id, $param_id = 0, $index = 0, $reload = 1)
	{
		$this->init(NetElement::find($netelem_id), $index);
		$params = $this->_get_parameter($param_id);

		\Log::debug(__FUNCTION__.": $netelem_id, $param_id, $index, $reload, ". count($params));

		$response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use ($params, $reload) {

			// Get data and push to client
			$start = microtime(true);
			echo "data: ".$this->get_snmp_values($params)."\n\n";
			$end = microtime(true);

			// Dont stress device too much - sleep at least as long as the device needs to return the values
			$diff = $end - $start;
			$sleep_time = $diff > $reload / 2 ? $diff : $reload - $diff;
			sleep($sleep_time);

			\Log::debug('Updating NetElements SnmpValues for SSE took '. round($diff, 2) .' seconds');
		});

		$response->headers->set('Content-Type', 'text/event-stream');

		return $response;
	}


	/**
	 * Get the necessary parameters (OIDs) of the netelementtype
	 *
	 * @return \Illuminate\Database\Eloquent\Collection 	of Parameter objects with related OID object
	 */
	private function _get_parameter($param_id)
	{
		if ($param_id)
			return Parameter::where('parent_id', '=', $param_id)->where('third_dimension', '=', 1)->with('oid')->orderBy('id')->get();

		// TODO: check if netelement has a netelementtype -> exception for root elem
		return $this->device->netelementtype->parameters()
			->with('oid')
			->orderBy('html_frame')->orderBy('html_id')->orderBy('oid_id')->orderBy('id')
			->get();
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
		$old_vals = $this->_values();

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
		$this->_values($values_to_store);

		return $ordered ? $results_tot : json_encode($results_tot);
	}


	/**
	 * Store values or get stored values
	 *
	 * @param Array  if oid-to-value array is set these values are stored
	 */
	private function _values($array = null)
	{
		$dir_path_rel = "data/hfc/snmpvalues/".$this->device->id;

		if ($array)
			\Storage::put($dir_path_rel, json_encode($array));
		else
			return \Storage::exists($dir_path_rel) ? json_decode(\Storage::get($dir_path_rel)) : [];
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
	 * NOTE: snmp2 is minimum 20 times faster for several snmpwalks
	 *
	 * @param 	String 	SNMP Object Identifier
	 * @return 	Array 	SNMP values in form: [OID => value]
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
	 * Update all changed SNMP Values
	 *
	 * @param Array data 	the HTML POST data array in form: [<oid> => <value>]
	 *
	 * @author Nino Ryschawy
	 */
	public function snmp_set_all($data)
	{
		// Get stored Snmpvalues
		$old_vals = $this->_values();

		if (!$old_vals)
			throw new Exception("Error: Stored SNMP Values were deleted!");

		$oids = new \Illuminate\Database\Eloquent\Collection();
		$oid_o = null;
		$pre_conf = $this->device->netelementtype->pre_conf_value ? true : false; 			// true - has to be done

		foreach ($data as $full_oid => $value)
		{
			// Discard everything that is not an snmp value field (method, token, ...)
			if ($full_oid[1] != '1')
				continue;

			// All dots of input variables are automatically replaced by PHP - See: https://stackoverflow.com/questions/68651/get-php-to-stop-replacing-characters-in-get-or-post-arrays
			// There is a workaround for $_POST (file_get_contents("php://input")) and $_GET ($_SERVER['QUERY_STRING']), but not very nice
			// So we have to replace all underscores by dots again
			$full_oid = str_replace('_', '.', $full_oid);

			// Null value can actually only happen, when someone deleted storage json file manually between last get and the save
			$old_val = isset($old_vals->{$full_oid}) ? $old_vals->{$full_oid} : null;

			if ($value == $old_val)
				continue;

			// GET OID to check if shown value was divided by unit_divisor (for the view)
			$index = strrchr($full_oid, '.'); 									// row in table
			$oid   = substr($full_oid, 0, strlen($full_oid) - strlen($index)); 	// column in table

			if (!$oid_o || $oid_o->oid != $oid)
			{
				// GET OID from database only once
				if ($oids->contains('oid', $oid))
					$oid_o = $oids->where('oid', $oid)->first();
				else {
					$oid_o = OID::where('oid', '=', $oid)->first();
					$oids->add($oid_o);
				}
			}

			if ($oid_o->access == 'read-only')
				continue;

			if ($oid_o->unit_divisor)
				$value *= $oid_o->unit_divisor;

			// Do preconfiguration only once if necessary
			if ($pre_conf) {
				$conf_val = $this->_configure();
				$pre_conf = false;
			}

			// Set Value
			$ret = $this->snmp_set($full_oid, $oid_o->type, $value);

			// only update on success
			if ($ret)
				$old_vals->{$oid} = $value;
			else
				$this->set_errors[] = $oid_o->name_gui ? : $oid_o->name;
		}

		// Store values
		$this->_values($old_vals);

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
	 * Push a SNMP value to the device
	 *
	 * @param Object oid
	 * @param String|Integer value
	 * @return true on success, otherwise false
	 *
	 * @author Torsten Schmidt, Nino Ryschawy
	 */
	public function snmp_set ($oid, $type, $value)
	{
		$community  = $this->_get_community('rw');

		// catch all OIDs that could not be set to print later in error message
		try {
			// NOTE: snmp2_set is also available
			$ret = snmpset($this->device->ip, $community, $oid, $type, $value, $this->timeout, $this->retry);
		} catch (\ErrorException $e) {
			Log::error('snmpset failed with msg: '.$e->getMessage(), [$this->device->ip, $community, $type, $value]);
			return false;
		}

		Log::debug('snmpset '.$this->device->ip.' '.$community.' '.$oid.' '.$value.' '.$type, [$ret]);

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
