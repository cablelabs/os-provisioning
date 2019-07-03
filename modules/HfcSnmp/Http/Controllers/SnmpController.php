<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Log;
use Session;
use Exception;
use Modules\HfcSnmp\Entities\OID;
use Modules\HfcReq\Entities\NetElement;
use Modules\HfcSnmp\Entities\Parameter;
use Modules\HfcReq\Entities\NetElementType;
use App\Http\Controllers\BaseViewController;
use Modules\ProvMon\Http\Controllers\ProvMonController;

class SnmpController extends \BaseController
{
    private $timeout = 300000;
    private $retry = 1;

    /**
     * @var  object 	NetElement
     */
    private $device;

    /**
     * @var  object     Used for parent cmts of a cluster
     */
    private $parent_device;

    /**
     * @var  array  of OID-Strings that threw an exception during SNMP-Set
     */
    private $errors = [];

    /**
     * @var  bool 	If Set we only want to show the 3rd dimension parameters of this index for the controlling view
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
    public function init($device = null, $index = 0)
    {
        $this->device = $device;
        $this->index = $index ? [$index] : 0;

        // Search parent CMTS for type cluster
        if ($device->netelementtype_id == 2) {
            $cmts = $device->get_parent_cmts();
            $this->parent_device = $cmts ?: null;
            if (! $this->device->ip) {
                if ($cmts) {
                    $this->device->ip = $cmts->ip;
                } else {
                    Session::push('tmp_info_above_form', trans('messages.snmp.missing_cmts'));
                }
            }
        }

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
        $this->init($netelem, $index);

        // GET SNMP values of NetElement
        $params = $this->_get_parameter($param_id);

        try {
            $form_fields = $this->get_snmp_values($params, true);
        } catch (Exception $e) {
            $form_fields = null;
        }

        // Error messages
        if (isset($e)) {
            Session::push('tmp_error_above_form', $e->getMessage());
        } elseif (! $form_fields && ! Session::exists('tmp_info_above_form')) {
            $msg = trans('messages.snmp.undefined');
            Session::push('tmp_info_above_form', $msg);
        } elseif ($this->errors) {
            $msg = trans('messages.snmp.errors_walk', ['oids' => implode(', ', $this->errors)]);
            Session::push('tmp_error_above_form', $msg);
        }

        // Init View
        $view_header = 'SNMP Settings: '.$netelem->name;
        $view_var = $netelem;
        $route_name = \NamespaceController::get_route_name();
        $headline = BaseViewController::compute_headline(\NamespaceController::get_route_name(), $view_header, $view_var).' > controlling';
        $tabs = ProvMonController::checkNetelementtype($netelem);

        $view_path = 'hfcsnmp::NetElement.controlling';
        $form_path = 'Generic.form';
        $form_update = 'NetElement.controlling_update';

        $reload = $this->device->netelementtype->page_reload_time ?: 0;

        return \View::make($view_path, $this->compact_prep_view(compact('view_var', 'view_header', 'form_path', 'tabs', 'form_fields', 'form_update', 'route_name', 'headline', 'reload', 'param_id', 'index')));
    }

    /**
     * Controlling Update Function
     *
     * @param int id the NetElement id
     * @param int param_id, index 	just for Redirect
     *
     * @author Torsten Schmidt, Nino Ryschawy
     */
    public function controlling_update($id, $param_id = 0, $index = 0)
    {
        // Init SnmpController
        $netelem = NetElement::where('id', '=', $id)->with('netelementtype')->first();
        $this->init($netelem, $index);

        // TODO: validation
        // Transfer Settings via SNMP to Device
        $this->snmp_set_all(\Request::all());

        $msg = 'Updated!';
        $msg_color = 'blue';

        // Set Error Message in case some OIDs could not be set
        if ($this->errors) {
            $msg = trans('messages.snmp.errors_set', ['oids' => implode(', ', $this->errors)]);

            Session::push('tmp_error_above_form', $msg);
        }

        return \Redirect::route('NetElement.controlling_edit', [$id, $param_id, $index]);
    }

    /**
     * Get the necessary parameters (OIDs) of the netelementtype
     *
     * @return \Illuminate\Database\Eloquent\Collection 	of Parameter objects with related OID object
     */
    private function _get_parameter($param_id)
    {
        if ($param_id) {
            return Parameter::where('parent_id', '=', $param_id)->where('third_dimension', '=', 1)->with('oid')->orderBy('id')->get();
        }

        $device = $this->device;

        // use parent cmts for cluster
        if ($this->device->netelementtype_id == 2) {
            if (! $this->parent_device) {
                return [];
            }

            $device = $this->parent_device;
        }

        // TODO: check if netelement has a netelementtype -> exception for root elem
        return $device->netelementtype->parameters()
            ->with('oid')
            ->orderBy('html_frame')->orderBy('html_id')->orderBy('oid_id')->orderBy('id')
            ->get();
    }

    /**
     * Returns updated SnmpValues via client opened TCP connection (SSE)
     */
    public function sse_get_snmpvalues($netelem_id, $param_id = 0, $index = 0, $reload = 1)
    {
        $this->init(NetElement::find($netelem_id), $index);
        $params = $this->_get_parameter($param_id);

        \Log::debug(__FUNCTION__.": $netelem_id, $param_id, $index, $reload, ".count($params));

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($params, $reload) {

            // Get data and push to client
            $start = microtime(true);
            echo 'data: '.$this->get_snmp_values($params)."\n\n";
            $end = microtime(true);

            // Dont stress device too much - sleep at least as long as the device needs to return the values
            $diff = $end - $start;
            $sleep_time = $diff > $reload / 2 ? $diff : $reload - $diff;
            sleep($sleep_time);

            \Log::debug('Updating NetElements SnmpValues for SSE took '.round($diff, 2).' seconds');
        });

        $response->headers->set('Content-Type', 'text/event-stream');

        return $response;
    }

    /**
     * GET all SNMP values from device
     *
     * @param array 	params 		Array of Parameter Objects
     * @param bool 		ordered 	true:  @return SNMP values as structured array to build initial view
     * 								false: @return raw json data to update values via Ajax
     * @return array 				TODO: explain output array
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
        if (! $this->device->ip) {
            return [];
        }

        foreach ($params as $param) {
            $indices = $this->index ?: [];

            if (! $indices) {
                $indices_o = $param->indices()->where('netelement_id', '=', $this->device->id)->first();
                $indices = $indices_o && $indices_o->indices ? explode(',', $indices_o->indices) : [];

                if ($this->device->netelementtype_id == 2 && ! $indices_o) {
                    \Log::error('HFC-Cluster is missing table indices for controlling view!', [$this->device->id]);
                    continue;
                }
            }

            // Table Param
            if ($param->oid->oid_table) {
                $table_id++;

                $results = $this->snmp_table($param, $indices);
                $values_to_store = array_merge($values_to_store, $results);

                $subparam = null;
                foreach ($results as $oid => $value) {
                    $index = strrchr($oid, '.'); 								// row in table
                    $suboid = substr($oid, 0, strlen($oid) - strlen($index)); 	// column in table

                    if (! $subparam || $subparam->oid != $suboid) {
                        if ($param->children->isEmpty()) {
                            $subparam = new Parameter;
                            $subparam->setRelation('oid', OID::where('oid', '=', $suboid)->first());
                        } else {
                            // Note: If existent Subparams are already fetched from DB in snmp_table() with joined OID
                            $subparam = $param->children->where('oidoid', $suboid)->first();
                        }
                    }

                    if (! $subparam || ! $subparam->oid) {
                        \Log::error('SNMP Query returned OID that is missing in database!');
                        continue;
                    }

                    $value = self::_build_diff_and_divide($subparam, $index, $results, $value, $old_vals);

                    // order results for initial view
                    if ($ordered) {
                        // set table head only once
                        if (! isset($results_tot['table'][$table_id]['head'][$suboid])) {
                            $results_tot['table'][$table_id]['head'][$suboid] = $subparam->oid->name_gui ? $subparam->oid->name_gui : $subparam->oid->name;
                        }

                        $arr = self::_get_formfield_array($subparam->oid, $index, $value, true);
                        $field = BaseViewController::get_html_input($arr);

                        $results_tot['table'][$table_id]['body'][$index][$suboid] = $field;
                    } else {
                        $results_tot[$oid] = $value;
                    }
                }

                if ($ordered && $param->third_dimension_params()->count()) {
                    $results_tot['table'][$table_id]['3rd_dim'] = ['netelement_id' => $this->device->id, 'param_id' => $param->id];
                }
            }
            // Non Table Param - can not have subparams
            else {
                $results = $this->snmp_walk($param->oid, $indices);
                $values_to_store = array_merge($values_to_store, $results);

                // Calculate differential param
                foreach ($results as $oid => $value) {
                    $index = strrchr($oid, '.'); 								// row in table
                    $suboid = substr($oid, 0, strlen($oid) - strlen($index)); 	// column in table
                    // join relevant information before calling diff function
                    $value = self::_build_diff_and_divide($param, $index, $results, $value, $old_vals);

                    // order results for initial view
                    if ($ordered) {
                        $arr = self::_get_formfield_array($param->oid, $index, $value);
                        $field = BaseViewController::add_html_string([$arr])[0]['html'];

                        if (! $param->html_frame) {
                            $results_tot['list'][] = $field;
                        } elseif (strlen((string) $param->html_frame) == 1) {
                            $results_tot['frame']['linear'][$param->html_frame][] = $field;
                        } else {
                            // e.g.: '12' -> row 1, column 2
                            $frame = (string) $param->html_frame;
                            $results_tot['frame']['tabular'][$frame[0]][$frame[1]][] = $field;
                        }
                    } else {
                        $results_tot[$oid] = $value;
                    }
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
     * @param array  if array ([oid => value]) is set these values are stored
     */
    private function _values($array = null)
    {
        $dir_path_rel = 'data/hfc/snmpvalues/'.$this->device->id;

        if ($array) {
            \Storage::put($dir_path_rel, json_encode($array));
        } else {
            return \Storage::exists($dir_path_rel) ? json_decode(\Storage::get($dir_path_rel)) : [];
        }
    }

    /**
     * Determine resulting value dependent of unit divisor or other OID values (see source code descriptions)
     *
     * @param object 	param 	Parameter
     * @param string 	index 	last number of OID
     * @param array 	results
     * @param string|int 	value 		current value from snmpwalk
     * @param string|int 	old_value 	value from last snmpwalk (to possibly calculate the difference)
     *
     * @author Nino Ryschawy
     */
    private static function _build_diff_and_divide($param, &$index, &$results, $value, $old_values)
    {
        $old_value = isset($old_values->{$param->oidoid.$index}) ? $old_values->{$param->oidoid.$index} : 0;

        // Subtract old value from new value
        if ($param->diff_param) {
            $value -= $old_value;
        }

        // divide value by value of other oid or sum of values of multiple OIDs and make it percentual
        if ($param->divide_by) {
            if (! is_array($param->divide_by)) {
                $param->divide_by = \Acme\php\ArrayHelper::str_to_array($param->divide_by);
            }

            $divisor_total = 0;
            foreach ($param->divide_by as $divisor_oid) {
                $divisor = $results[$divisor_oid.$index];

                // For differential params build difference of divisor to old value as well
                if ($param->diff_param) {
                    $old_value = isset($old_values->{$divisor_oid.$index}) ? $old_values->{$divisor_oid.$index} : 0;
                    $divisor = $results[$divisor_oid.$index] - $old_value;
                }

                $divisor_total += $divisor;
            }

            $value = $divisor_total ? round($value / $divisor_total * 100, 2) : $value;
        }
        // divide value by fix number (e.g. to change the power(Potenz) of the value)
        elseif ($param->oid->unit_divisor && is_numeric($value)) {
            $value /= $param->oid->unit_divisor;
        }

        return $value;
    }

    /**
     * Generate Form Field array as preparation for creating the html form fields from it
     *
     * @param object 	OID
     * @param string 	index 	Last number of OID (with starting dot)
     * @param string|int value
     * @param bool 		table
     */
    private static function _get_formfield_array($oid, $index, $value, $table = false)
    {
        $options = null;

        if ($table) {
            $options['style'] = 'simple';
            $options['style'] .= in_array($oid->type, ['i', 'u', 't']) ? ';width: 85px;' : '';
        }

        if ($oid->access == 'read-only') {
            $options[] = 'readonly';
        }

        // description of table is set only once for table head
        $ext = $index == '.0' ? '' : $index;
        $description = $table ? '' : ($oid->name_gui ? $oid->name_gui.$ext : $oid->name.$ext);

        $field = [
            'form_type' 	=> $oid->html_type,
            'name' 			=> $oid->oid.$index,
            'description' 	=> $description,
            'field_value' 	=> $value,
            'options' 		=> $options,
            // 'help' 			=> $oid->description,
            ];

        if ($oid->html_type == 'select') {
            $field['value'] = $oid->get_select_values();
        }

        return $field;
    }

    /**
     * The SNMP Walk Function
     *
     * NOTE: snmp2 is minimum 20 times faster for several snmpwalks
     *
     * @param 	object
     * @param   array   of strings
     * @return 	array 	SNMP values in form: [OID => value]
     *
     * @author Torsten Schmidt, Nino Ryschawy
     */
    public function snmp_walk($oid, $indices = [])
    {
        $results = [];
        $community = $this->_get_community();

        $oid_s = $oid->oid;

        // Log
        Log::debug('snmpwalk '.$this->device->ip.' '.$oid_s);

        if ($indices) {
            try {
                // check if snmp version 2 is supported - use it - otherwise use version 1
                snmp2_get($this->device->ip, $community, '1.3.6.1.2.1.1.1.0', $this->timeout, $this->retry);

                foreach ($indices as $index) {
                    try {
                        $results["$oid_s.$index"] = snmp2_get($this->device->ip, $community, "$oid_s.$index", $this->timeout, $this->retry);
                    } catch (Exception $e) {
                        $name = $oid->name_gui ?: $oid->name;
                        $this->errors[] = "$name.$index";
                        \Log::error("snmp2_get: $name.$index");
                    }
                }
            } catch (Exception $e) {
                try {
                    snmpget($this->device->ip, $community, '1.3.6.1.2.1.1.1.0', $this->timeout, $this->retry);

                    foreach ($indices as $index) {
                        try {
                            $results["$oid_s.$index"] = snmp2_get($this->device->ip, $community, "$oid_s.$index", $this->timeout, $this->retry);
                        } catch (Exception $e) {
                            $name = $oid->name_gui ?: $oid->name;
                            $this->errors[] = "$name.$index";
                            \Log::error("snmpget: $name.$index");
                        }
                    }
                } catch (Exception $e) {
                    $results = [];
                }
            }
        } else {
            try {
                $results = snmp2_real_walk($this->device->ip, $community, $oid_s, $this->timeout, $this->retry);
            } catch (Exception $e) {
                try {
                    $results = snmprealwalk($this->device->ip, $community, $oid_s, $this->timeout, $this->retry);
                } catch (Exception $e) {
                    $results = [];

                    $this->errors[] = $oid->name_gui ?: $oid->name;
                }
            }
        }

        if (isset($e) && ! $results) {
            self::check_reachability($e);
        }

        return $results;
    }

    /**
     * SNMP Walk over a Table OID Parameter - Can also be a walk over all it's SubOIDs
     *
     * @param 	param 	Table Object ID
     * @return 	array	[values => [index => [oid => value]], [diff-OIDs]]
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
        if (! $param->children->isEmpty()) {
            foreach ($param->children as $param) {
                // Note: snmpwalk -CE ends on this OID - makes it much faster
                // exec('snmpwalk -v2c -CE 1.3.6.1.2.1.10.127.1.1.1.1.3.6725 -c'.$this->_get_community().' '.$this->device->ip.' '.$oid->oid, $results);
                $results += $this->snmp_walk($param->oid, $indices);
            }
        }
        // standard table OID (all suboids(columns) and elements (rows))
        else {
            Log::debug('snmp2_real_walk (table) '.$this->device->ip.' '.$oid->oid);
            try {
                $results = snmp2_real_walk($this->device->ip, $this->_get_community(), $oid->oid);
            } catch (Exception $e) {
                self::check_reachability($e);

                $results = [];
                $this->errors[] = $oid->name_gui ?: $oid->name;
                \Log::error('snmp2_real_walk: '.$oid->name_gui ?: $oid->name);
            }
        }

        if (! $results) {
            Log::error('No Results for SnmpWalk over OID: '.$oid->oid);
        } // Possible Reasons: wrong defined indices, device does not support oid

        return $results;
    }

    /**
     * Update all changed SNMP Values
     *
     * @param array data 	the HTML POST data array in form: [<oid> => <value>]
     *
     * @author Nino Ryschawy
     */
    public function snmp_set_all($data)
    {
        // Get stored Snmpvalues
        $old_vals = $this->_values();

        if (! $old_vals) {
            throw new Exception('Error: Stored SNMP Values were deleted!');
        }
        // TODO: get empty collection or already filled with OIDs to increase performance if probable
        // $oids = $this->_get_oid_collection();
        $oids = new \Illuminate\Database\Eloquent\Collection();
        $oid_o = null;

        // switch device and parent device if type is cluster so that all functions work properly - switch again to store values
        if ($this->device->netelementtype_id == 2) {
            $device = $this->device;
            $this->device = $this->parent_device;
        }

        $pre_conf = $this->device->netelementtype->pre_conf_value ? true : false; 			// true - has to be done
        $user = \Auth::user();

        foreach ($data as $full_oid => $value) {
            // Discard everything that is not an snmp value field (method, token, ...)
            if ($full_oid[1] != '1') {
                continue;
            }

            // All dots of input variables are automatically replaced by PHP - See: https://stackoverflow.com/questions/68651/get-php-to-stop-replacing-characters-in-get-or-post-arrays
            // There is a workaround for $_POST (file_get_contents("php://input")) and $_GET ($_SERVER['QUERY_STRING']), but not very nice
            // So we have to replace all underscores by dots again
            $full_oid = str_replace('_', '.', $full_oid);

            // Null value can actually only happen, when someone deleted storage json file manually between last get and the save
            $old_val = isset($old_vals->{$full_oid}) ? $old_vals->{$full_oid} : null;

            // ATTENTION: This check improves performance, but assumes that it's not possible to change value previously
            // divided by unit_divisor to a value multiplied exactly by unit_divisor as in following example:
            // e.g.: unit_divisor=10 and old_val=100 (in GUI 10) and (new) value=100 (in GUI 100) would result in not saving the value as 100=100
            // but value was actually changed to 10x the previous value
            if ($value == $old_val) {
                continue;
            }

            // GET OID to check if shown value was divided by unit_divisor (for the view)
            $index = strrchr($full_oid, '.'); 									// row in table
            $oid = substr($full_oid, 0, strlen($full_oid) - strlen($index)); 	// column in table

            if (! $oid_o || $oid_o->oid != $oid) {
                // GET OID from database only once
                if ($oids->contains('oid', $oid)) {
                    $oid_o = $oids->where('oid', $oid)->first();
                } else {
                    $oid_o = OID::where('oid', '=', $oid)->first();
                    $oids->add($oid_o);
                }
            }

            if ($oid_o->access == 'read-only') {
                continue;
            }

            if ($oid_o->unit_divisor) {
                $value *= $oid_o->unit_divisor;
            }

            if ($value == $old_val) {
                continue;
            }

            // Do preconfiguration only once if necessary
            if ($pre_conf) {
                $conf_val = $this->_configure();
                $pre_conf = false;
            }

            // Set Value
            $ret = $this->snmp_set($full_oid, $oid_o->type, $value);

            // only update on success
            if ($ret) {
                // Create GuiLog Entry
                \App\GuiLog::log_changes([
                    'user_id' => $user ? $user->id : 0,
                    'username' 	=> $user ? $user->first_name.' '.$user->last_name : 'cronjob',
                    'method' 	=> 'updated',
                    'model' 	=> 'NetElement',
                    'model_id'  => $this->device->netelementtype_id == 2 ? $device->id : $this->device->id,
                    'text' 		=> ($oid_o->name_gui ?: $oid_o->name)." ($full_oid):  '".$old_val."' => '$value'",
                    ]);

                $old_vals->{$full_oid} = $value;
            } else {
                $this->errors[] = $oid_o->name_gui ?: $oid_o->name;
            }
        }

        // Do postconfig if preconfig was done
        if (isset($conf_val)) {
            $this->_configure($conf_val);
        }

        // Store values
        $this->device = $this->device->netelementtype_id == 2 ? $device : $this->device;
        $this->_values($old_vals);
    }

    /**
     * Gets all necessary OIDs if it's probable that they will be necessary for update so that
     *	we have only one DB-Query and not multiple queries inside the for loop
     *
     * @return \Illuminate\Database\Eloquent\Collection  - empty by default - filled with OIDs if it's possible to increase performance
     */
    private function _get_oid_collection()
    {
        // Performance improvement (needs only 33% of the time for 35 OIDs)
        // Get OID Collection (all read-write OIDs) for NetElements that dont have a table parameter only once before the foreach loop
        $query = $this->device->netelementtype->parameters()->join('oid', 'oid.id', '=', 'parameter.oid_id');
        $has_table = $query->where('oid.oid_table', '=', 1)->count();
        $cnt = $query->where('oid.access', '=', 'read-write')->whereNotIn('oid.unit_divisor', [null, 0])->count();

        if ($has_table || $cnt < 10) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        $oid_ids = $this->device->netelementtype->parameters()->get(['oid_id'])->pluck('oid_id')->all();

        return $oids = OID::whereIn('id', $oid_ids)->where('access', '=', 'read-write')->get();
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

        if ($type->pre_conf_oid_id xor $type->pre_conf_value) {
            \Log::debug('Snmp Preconfiguration settings incomplete for this Device (NetElement)', [$this->device->name, $this->device->id]);

            return;
        }

        $oid = $type->oid;

        // PreConfiguration
        if (! $value) {
            $conf_val = snmpget($this->device->ip, $this->_get_community(), $oid->oid.'.0', $this->timeout, $this->retry);

            $ret = false;
            if ($conf_val != $type->pre_conf_value) {
                $ret = snmpset($this->device->ip, $this->_get_community('rw'), $oid->oid.'.0', $oid->type, $type->pre_conf_value, $this->timeout, $this->retry);
            }

            $ret ? \Log::debug('Preconfigured Device for snmpset', [$this->device->name, $this->device->id]) : \Log::debug('Failed to Preconfigure Device for snmpset', [$this->device->name, $this->device->id]);

            // wait time in usec
            $sleep_time = $type->pre_conf_time_offset * 1000000 ?: 0;
            usleep($sleep_time);

            return $conf_val;
        }

        // PostConfiguration
        snmpset($this->device->ip, $this->_get_community('rw'), $oid->oid.'.0', $oid->type, $value, $this->timeout, $this->retry);

        \Log::debug('Postconfigured Device for snmpset', [$this->device->name, $this->device->id]);
    }

    /**
     * Push a SNMP value to the device
     *
     * @param object oid
     * @param string|int value
     * @return true on success, otherwise false
     *
     * @author Torsten Schmidt, Nino Ryschawy
     */
    public function snmp_set($oid, $type, $value)
    {
        $community = $this->_get_community('rw');

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
        return $this->device->{'community_'.$access} ?: \Modules\Hfc\Entities\HfcBase::get([$access.'_community'])->first()->{$access.'_community'};
    }

    /**
     * Set PHP SNMP Default Values
     * Note: Must be only called once per Object Init
     *
     * @author Torsten Schmidt
     */
    private function snmp_def_mode()
    {
        snmp_set_quick_print(true);
        snmp_set_oid_numeric_print(true);
        snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
        snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
    }

    /**
     * Check if device was reachable via snmp
     *
     * @param exception
     * @throws exception    when device is not reachable
     */
    public static function check_reachability(Exception $e)
    {
        $msg = $e->getMessage();

        if (stripos($msg, 'Name or service not known') !== false || stripos($msg, 'No response from') !== false) {
            throw new Exception(trans('messages.snmp.unreachable'));
        }
    }
}
