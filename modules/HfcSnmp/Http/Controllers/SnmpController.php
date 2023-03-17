<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Modules\HfcSnmp\Http\Controllers;

use App\Http\Controllers\BaseViewController;
use App\User;
use Cache;
use Exception;
use File;
use Illuminate\Support\Facades\Log;
use Modules\HfcReq\Entities\NetElement;
use Modules\HfcSnmp\Entities\OID;
use Modules\HfcSnmp\Entities\Parameter;
use Modules\HfcSnmp\Events\NewSnmpValues;
use Session;

class SnmpController extends \BaseController
{
    private $timeout = 1000000;
    private $retry = 1;

    /**
     * @var object NetElement
     */
    private $netelement;
    private $netelementIp;

    /**
     * @var object Used for parent netgw of a cluster
     */
    private $parent_device;

    /**
     * @var array of OID-Strings that threw an exception during SNMP-Set
     */
    private $errors = [];

    /**
     * If set we only want to show the 3rd dimension parameters of this parameter and index in the controlling view
     *
     * @var int
     */
    private $index = 0;
    private $paramId = 0;

    /**
     * Key to get values from cache - set in init() function
     *
     * @var string
     */
    private $cacheKey;

    /**
     * Init SnmpController with a certain Device Model and
     * a MIB Array
     *
     * @param device the Device Model
     * @param mibs the MIB array
     *
     * @author Torsten Schmidt, Nino Ryschawy
     */
    public function init($netelement = null, $paramId = 0, $index = 0)
    {
        $this->netelement = $netelement;
        $this->index = $index ? [$index] : 0;
        $this->paramId = $paramId;
        $this->cacheKey = "snmpvalues.{$this->netelement->id}.$paramId.$index";

        $this->snmp_def_mode();

        $this->netelementIp = $netelement->ip;
        if (! $netelement->ip && $netelement->provDevice) {
            $this->netelementIp = gethostbyname($netelement->provDevice->hostname);
        }

        if ($netelement->base_type_id != 2) {
            return;
        }

        // Search parent NetGw for type cluster
        $netgw = $netelement->getParentNetelementOfType(3);
        $this->parent_device = $netgw ?: null;

        if ($this->netelement->ip) {
            return;
        }

        if (! $netgw) {
            throw new Exception(trans('messages.snmp.missing_netgw'));
        }

        $this->netelementIp = $netgw->ip;
    }

    /**
     * Returns the Controlling View for a NetElement (Device)
     *
     * Note: This function is used again for the 3rd Dimension of a Snmp Table (of which the Index link references to)
     *
     * @param   id          The NetElement id
     * @param   paramId     ID of the Parameter for 3rd Dimension View
     * @param   index       The Index we want to see 3rd Dim for
     *
     * @author  Torsten Schmidt, Nino Ryschawy
     */
    public function controlling_edit(NetElement $netelement, $paramId = 0, $index = 0)
    {
        $form_fields = [];
        $error = false;

        try {
            $this->init($netelement, $paramId, $index);

            $form_fields = $this->getSnmpValues(true);
        } catch (Exception $e) {
            Session::push('tmp_error_above_form', $e->getMessage());
            $error = true;

            if ($e->getMessage() == trans('messages.snmp.unreachable')) {
                $form_fields = $this->getLastValues();

                if ($form_fields) {
                    Session::forget('tmp_error_above_form');
                }
            }
        }

        // Show single OIDs that are not accessable on device
        if (! isset($e) && $this->errors) {
            Session::push('tmp_error_above_form', trans('messages.snmp.errors_walk', ['oids' => implode(', ', $this->errors)]));
        }

        // Init View
        $view_header = 'SNMP Settings: '.$netelement->name;
        $route_name = \NamespaceController::get_route_name();
        $headline = BaseViewController::compute_headline($route_name, $view_header, $netelement).'<li><a href="#">controlling</a></li>';
        $tabs = $netelement->tabs();
        $reload = $netelement->netelementtype->page_reload_time ?: 0;

        return \View::make('hfcsnmp::NetElement.controlling', $this->compact_prep_view(compact('error', 'netelement',
            'view_header', 'tabs', 'form_fields', 'route_name', 'headline', 'reload', 'paramId', 'index')));
    }

    /**
     * Controlling Update Function
     *
     * @param int id the NetElement id
     * @param int paramId, index    just for Redirect
     *
     * @author Torsten Schmidt, Nino Ryschawy
     */
    public function controlling_update($id, $paramId = 0, $index = 0)
    {
        $netelem = NetElement::where('id', '=', $id)->with('netelementtype')->first();
        $this->init($netelem, $paramId, $index);

        // TODO: validation
        $this->snmp_set_all(\Request::all());

        // Set Error Message in case some OIDs could not be set
        if ($this->errors) {
            $msg = trans('messages.snmp.errors_set', ['oids' => implode(', ', $this->errors)]);

            Session::push('tmp_error_above_form', $msg);
        }

        return \Redirect::route('NetElement.controlling_edit', [$id, $paramId, $index]);
    }

    /**
     * Get the necessary parameters (OIDs) of the netelementtype
     *
     * @return \Illuminate\Database\Eloquent\Collection of Parameter objects with related OID object
     */
    private function getParameters()
    {
        if ($this->paramId) {
            return Parameter::where('parent_id', '=', $this->paramId)->where('third_dimension', '=', 1)->with('oid')->orderBy('id')->get();
        }

        $netelement = $this->netelement;

        // use parent netgw for cluster
        if ($this->netelement->base_type_id == 2) {
            if (! $this->parent_device) {
                return [];
            }

            $netelement = $this->parent_device;
        }

        return $netelement->netelementtype->parameters()
            ->with('oid')
            ->orderBy('html_frame')->orderBy('html_id')->orderBy('oid_id')->orderBy('id')
            ->get();
    }

    /**
     * Start loop for broadcasting SNMP live values by first subscriber
     *
     * @return string status
     */
    public function triggerSnmpQueryLoop(NetElement $netelement, $paramId = 0, $index = 0)
    {
        Log::debug(__FUNCTION__.": Poll netelement $netelement->id via SNMP");

        $newSnmpValues = new NewSnmpValues([], $netelement, $paramId, $index);
        $channelName = $newSnmpValues->broadcastOn()->name;

        $websocketApi = new \App\extensions\websockets\WebsocketApi();

        // Don't run another query loop when someone else already triggered it
        if ($websocketApi->channelHasSubscribers($channelName, true)) {
            return 'already running';
        }

        $this->init($netelement, $paramId, $index);
        $params = $this->getParameters();

        // TODO: Write as Job ? - Then we would need to start and stop as many workers on demand as we
        // have loops running as the jobs/loops would need to run simultaneously
        do {
            $start = microtime(true);
            $data = $this->getSnmpValues(false, $params);
            // $data = json_encode(['.1.2.3.3.3' => rand(1, 100), '.1.23.4.5' => rand(1, 100)]);    // Testdata
            $queryTime = microtime(true) - $start;

            $newSnmpValues->setData($data);
            event($newSnmpValues);

            Log::debug("Send data to channel $channelName: ".substr($data, 0, 90).(strlen($data) > 90 ? ' ... }' : '').' - Query time: '.round($queryTime, 3));

            $this->sleepWell($queryTime, $netelement);
        } while ($websocketApi->channelHasSubscribers($channelName));

        return 'stopped';
    }

    /**
     * Let process sleep enough time to not stress device too much
     */
    private function sleepWell($queryTime, $netelement)
    {
        $reload = $netelement->netelementtype->page_reload_time ?: 2;

        usleep(($queryTime > $reload ? 2 : $reload - $queryTime) * 1000000);
    }

    /**
     * GET all SNMP values from device
     *
     * @param bool      ordered     true:  @return SNMP values as structured array to build initial view
     *                              false: @return raw json data to update values via Ajax
     * @param array     params      Optional array of Parameter objects to improve performance in loop
     * @return array TODO: explain output array
     *
     * @author Nino Ryschawy
     */
    public function getSnmpValues($ordered, $params = [])
    {
        $orderedValues = ['list' => [], 'frame' => ['linear' => [], 'tabular' => []], 'table' => []];
        $valuesToStore = $finalValues = [];
        $table_id = 0;

        if (! $this->netelementIp) {
            throw new Exception(trans('messages.snmp.missingIp'));
        }

        // Use cached values if device was already queried during the last seconds
        if ($ordered) {
            // TODO: if a query to a device takes a huge time - e.g. 3 secs and multiple users access the controlling page at the
            // same time we could mark the device as queried and let the user wait for the stored values triggered by the other user
            // Take care of marking it as not queried also when exception is thrown
            // if ($this->netelement->isQueried()) {
            //     while (! Cache::has($this->cacheKey)) {
            //         usleep(200000);
            //     }
            // }

            $values = Cache::get($this->cacheKey);

            if ($values) {
                Log::debug('Return cached SNMP values for netelement '.$this->netelement->id);

                return $values;
            }
        }

        $oldValues = $this->getStoredValues()['values'];
        if (! $params) {
            $params = $this->getParameters();
        }

        if ($params->isEmpty()) {
            throw new Exception(trans('messages.snmp.undefined'));
        }

        foreach ($params as $param) {
            $indices = $this->index ?: [];

            if (! $indices) {
                $indices_o = $param->indices()->where('netelement_id', '=', $this->netelement->id)->first();
                $indices = $indices_o && $indices_o->indices ? explode(',', $indices_o->indices) : [];

                if ($this->netelement->base_type_id == 2 && ! $indices_o) {
                    Log::error('HFC-Cluster is missing table indices for controlling view!', [$this->netelement->id]);
                    continue;
                }
            }

            // Table Param
            if ($param->oid->oid_table) {
                $table_id++;

                $results = $this->snmp_table($param, $indices);
                $valuesToStore = array_merge($valuesToStore, $results);

                $subparam = null;
                foreach ($results as $oid => $value) {
                    if (strpos($oid, $param->oid->oid.'.1.') !== false) {
                        $entry = substr($oid, strlen($param->oid->oid.'.1.'));
                        $suboid = $param->oid->oid.'.1.'.substr($entry, 0, strpos($entry, '.'));
                        $index = substr($oid, strlen($suboid));
                    } else {
                        // Support for self created tables with suboids not being a leaf of the table OID (could even be from another MIB)
                        $index = strrchr($oid, '.');                                // row in table
                        $suboid = substr($oid, 0, strlen($oid) - strlen($index));   // column in table
                    }

                    if (! $subparam || $subparam->oid != $suboid) {
                        if ($param->children->isEmpty()) {
                            $subparam = new Parameter;
                            $subparam->setRelation('oid', OID::where('oid', '=', $suboid)->first());
                        } else {
                            // If existent Subparams are already fetched from DB in snmp_table() with joined OID
                            $subparam = $param->children->where('oidoid', $suboid)->first();
                        }
                    }

                    if (! $subparam || ! $subparam->oid) {
                        Log::error("SNMP Query returned OID $suboid that is missing in database");

                        continue;
                    }

                    $value = self::_build_diff_and_divide($subparam, $index, $results, $value, $oldValues);
                    $finalValues[$oid] = $value;

                    // Order results and get HTML field description for initial view
                    // set table head only once
                    if (! isset($orderedValues['table'][$table_id]['head'][$suboid])) {
                        $orderedValues['table'][$table_id]['head'][$suboid] = $subparam->oid->name_gui ?: $subparam->oid->name;
                    }

                    $arr = self::_get_formfield_array($subparam->oid, $index, $value, true);
                    $field = BaseViewController::get_html_input($arr);

                    $orderedValues['table'][$table_id]['body'][$index][$suboid] = $field;
                }

                if ($param->children->where('third_dimension', '=', 1)->count()) {
                    $orderedValues['table'][$table_id]['3rd_dim'] = ['netelement_id' => $this->netelement->id, 'paramId' => $param->id];
                }
            }
            // Non Table Param - can not have subparams
            else {
                $results = $this->snmp_walk($param->oid, $indices);
                $valuesToStore = array_merge($valuesToStore, $results);

                // Calculate differential param
                foreach ($results as $oid => $value) {
                    $index = strrchr($oid, '.');                                // row in table
                    $suboid = substr($oid, 0, strlen($oid) - strlen($index));   // column in table
                    // join relevant information before calling diff function
                    $value = self::_build_diff_and_divide($param, $index, $results, $value, $oldValues);
                    $finalValues[$oid] = $value;

                    // Order results and get HTML field description for initial view
                    $arr = self::_get_formfield_array($param->oid, $index, $value);
                    $field = BaseViewController::add_html_string([$arr])[0]['html'];

                    if (! $param->html_frame) {
                        $orderedValues['list'][] = $field;
                    } elseif (strlen((string) $param->html_frame) == 1) {
                        $orderedValues['frame']['linear'][$param->html_frame][] = $field;
                    } else {
                        // e.g.: '12' -> row 1, column 2
                        $frame = (string) $param->html_frame;
                        $orderedValues['frame']['tabular'][$frame[0]][$frame[1]][] = $field;
                    }
                }
            }
        } // end foreach

        $this->storeSnmpValues($valuesToStore);
        $this->storeSnmpValues($orderedValues, 'ordered');
        Cache::put($this->cacheKey, $orderedValues, 5);

        return $ordered ? $orderedValues : json_encode($finalValues);
    }

    /**
     * Return last queried values from device that is not reachable anymore and show warning with queried time
     *
     * @return array
     */
    private function getLastValues()
    {
        $array = $this->getStoredValues('ordered');

        if ($array['values']) {
            Session::push('tmp_warning_above_form', trans('messages.snmp.lastValues', ['date' => date('Y-m-d', $array['time'])]));
        }

        return $array['values'];
    }

    /**
     * Store SNMP values
     *
     * @param array
     * @param string
     */
    private function storeSnmpValues($data, $ext = '')
    {
        $filePath = $this->netelement->getSnmpValuesStoragePath($ext);

        File::put($filePath, json_encode($data), true);
    }

    /**
     * Store values or get stored values
     *
     * @param string
     * @return array
     */
    private function getStoredValues($ext = '')
    {
        $filePath = $this->netelement->getSnmpValuesStoragePath($ext);

        if (! File::exists($filePath)) {
            return ['values' => []];
        }

        return [
            'time' => filemtime($filePath),
            'values' => json_decode(File::get($filePath), true),
        ];
    }

    /**
     * Determine resulting value dependent of unit divisor or other OID values (see source code descriptions)
     *
     * @param object    param   Parameter
     * @param string    index   last number of OID
     * @param array     results
     * @param string|int    value       current value from snmpwalk
     * @param string|int    old_value   value from last snmpwalk (to possibly calculate the difference)
     *
     * @author Nino Ryschawy
     */
    private static function _build_diff_and_divide($param, &$index, &$results, $value, $old_values)
    {
        $old_value = isset($old_values[$param->oidoid.$index]) ? $old_values[$param->oidoid.$index] : 0;

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
                    $old_value = isset($old_values[$divisor_oid.$index]) ? $old_values[$divisor_oid.$index] : 0;
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
     * @param object    OID
     * @param string    index   Last number of OID (with starting dot)
     * @param string|int value
     * @param bool      table
     */
    private static function _get_formfield_array($oid, $index, $value, $table = false)
    {
        $options = null;

        if ($table) {
            $options['style'] = 'simple';
            $options['style'] .= in_array($oid->type, ['i', 'u', 't']) ? ';width: 85px;' : '';
        }

        if ($oid->access == 'read-only') {
            $options['htmlReadonly'] = 'readonly';
        }

        // description of table is set only once for table head
        $ext = $index == '.0' ? '' : $index;
        $description = $table ? '' : ($oid->name_gui ? $oid->name_gui.$ext : $oid->name.$ext);

        $field = [
            'form_type'     => $oid->html_type,
            'name'          => $oid->oid.$index,
            'description'   => $description,
            'field_value'   => $value,
            'options'       => $options,
            // 'help'           => $oid->description,
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
     * @param   object
     * @param   array   of strings
     * @return array SNMP values in form: [OID => value]
     *
     * @author Torsten Schmidt, Nino Ryschawy
     */
    public function snmp_walk($oid, $indices = [])
    {
        $results = [];
        $community = $this->netelement->community();

        $oid_s = $oid->oid;

        // Log
        Log::debug('snmpwalk '.$this->netelementIp.' '.$oid_s);

        if ($indices) {
            try {
                // check if snmp version 2 is supported - use it - otherwise use version 1
                snmp2_get($this->netelementIp, $community, '1.3.6.1.2.1.1.1.0', $this->timeout, $this->retry);

                foreach ($indices as $index) {
                    try {
                        $results["$oid_s.$index"] = snmp2_get($this->netelementIp, $community, "$oid_s.$index", $this->timeout, $this->retry);
                    } catch (Exception $e) {
                        $name = $oid->name_gui ?: $oid->name;
                        $this->errors[] = "$name.$index";
                        Log::error("snmp2_get: $name.$index");
                    }
                }
            } catch (Exception $e) {
                try {
                    snmpget($this->netelementIp, $community, '1.3.6.1.2.1.1.1.0', $this->timeout, $this->retry);

                    foreach ($indices as $index) {
                        try {
                            $results["$oid_s.$index"] = snmp2_get($this->netelementIp, $community, "$oid_s.$index", $this->timeout, $this->retry);
                        } catch (Exception $e) {
                            $name = $oid->name_gui ?: $oid->name;
                            $this->errors[] = "$name.$index";
                            Log::error("snmpget: $name.$index");
                        }
                    }
                } catch (Exception $e) {
                    $results = [];
                }
            }
        } else {
            try {
                $results = snmp2_real_walk($this->netelementIp, $community, $oid_s, $this->timeout, $this->retry);
            } catch (Exception $e) {
                try {
                    // There are devices where querying v1 directly after v2 leads to exception (e.g. kathrein HMS-Transponder)
                    // usleep(400000);

                    $results = snmprealwalk($this->netelementIp, $community, $oid_s, $this->timeout, $this->retry);
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
     * @param   param   Table Object ID
     * @return array [values => [index => [oid => value]], [diff-OIDs]]
     *
     * @author  Nino Ryschawy
     */
    public function snmp_table($param, $indices)
    {
        $oid = $param->oid;
        $results = $res = $diff_param = $divisions = [];
        $relation = $param->children()
            ->with('oid')
            ->join('oid as o', 'o.id', '=', 'parameter.oid_id')
            ->select('parameter.*', 'o.oid as oidoid')
            ->orderBy('third_dimension')->orderBy('html_id')->orderBy('parameter.id')->get();

        $param->setRelation('children', $relation);
        $paramSingleDim = $param->children->where('third_dimension', '=', $this->index ? 1 : 0);

        // exact defined table via SubOIDs
        if (! $paramSingleDim->isEmpty()) {
            foreach ($paramSingleDim as $parameter) {
                // Note: snmpwalk -CE ends on this OID - makes it much faster
                // exec('snmpwalk -v2c -CE 1.3.6.1.2.1.10.127.1.1.1.1.3.6725 -c'.$this->netelement->community().' '.$this->netelementIp.' '.$oid->oid, $results);
                $results += $this->snmp_walk($parameter->oid, $indices);
            }
        }
        // standard table OID (all suboids(columns) and elements (rows))
        else {
            Log::debug('snmp2_real_walk (table) '.$this->netelementIp.' '.$oid->oid);
            try {
                $results = snmp2_real_walk($this->netelementIp, $this->netelement->community(), $oid->oid);
            } catch (Exception $e) {
                self::check_reachability($e);

                $results = [];
                $this->errors[] = $oid->name_gui ?: $oid->name;
                Log::error('snmp2_real_walk: '.$oid->name_gui ?: $oid->name);
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
     * @param array data    the HTML POST data array in form: [<oid> => <value>]
     *
     * @author Nino Ryschawy
     */
    public function snmp_set_all($data)
    {
        // Get stored Snmpvalues
        $oldValues = $this->getStoredValues()['values'];

        if (! $oldValues) {
            throw new Exception('Error: Stored SNMP Values were deleted!');
        }

        // TODO: get empty collection or already filled with OIDs to increase performance if probable
        // $oids = $this->_get_oid_collection();
        $oids = new \Illuminate\Database\Eloquent\Collection();
        $oid_o = null;

        // switch device and parent device if type is cluster so that all functions work properly - switch again to store values
        if ($this->netelement->base_type_id == 2) {
            $netelement = $this->netelement;
            $this->netelement = $this->parent_device;
        }

        $pre_conf = $this->netelement->netelementtype->pre_conf_value ? true : false;           // true - has to be done
        $user = \Auth::user();

        foreach ($data as $full_oid => $value) {
            // Discard everything that is not an snmp value field (method, token, ...)
            if ($full_oid[1] != '1') {
                continue;
            }

            // All dots of input variables are automatically replaced by PHP
            // See: https://stackoverflow.com/questions/68651/get-php-to-stop-replacing-characters-in-get-or-post-arrays
            // There is a workaround for $_POST (file_get_contents("php://input")) and $_GET ($_SERVER['QUERY_STRING']), but not very nice
            // So we have to replace all underscores by dots again
            $full_oid = str_replace('_', '.', $full_oid);

            // Null value can actually only happen, when someone deleted storage json file manually between last get and the save
            $old_val = $oldValues[$full_oid] ?? null;

            // ATTENTION: This check improves performance, but assumes that it's not possible to change value previously
            // divided by unit_divisor to a value multiplied exactly by unit_divisor as in following example:
            // e.g.: unit_divisor=10 and old_val=100 (in GUI 10) and (new) value=100 (in GUI 100) would result in not saving the value as 100=100
            // but value was actually changed to 10x the previous value
            if ($value == $old_val) {
                continue;
            }

            // GET OID to check if shown value was divided by unit_divisor (for the view)
            $index = strrchr($full_oid, '.');                                   // row in table
            $oid = substr($full_oid, 0, strlen($full_oid) - strlen($index));    // column in table

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
                    'username'  => $user ? $user->first_name.' '.$user->last_name : 'cronjob',
                    'method'    => 'updated',
                    'model'     => 'NetElement',
                    'model_id'  => $this->netelement->base_type_id == 2 ? $netelement->id : $this->netelement->id,
                    'text'      => ($oid_o->name_gui ?: $oid_o->name)." ($full_oid):  '".$old_val."' => '$value'",
                ]);

                $oldValues[$full_oid] = $value;
            } else {
                $this->errors[] = $oid_o->name_gui ?: $oid_o->name;
            }
        }

        // Do postconfig if preconfig was done
        if (isset($conf_val)) {
            $this->_configure($conf_val);
        }

        // Store values
        $this->netelement = $this->netelement->base_type_id == 2 ? $netelement : $this->netelement;
        $this->storeSnmpValues($oldValues);

        Cache::forget($this->cacheKey);
    }

    /**
     * Gets all necessary OIDs if it's probable that they will be necessary for update so that
     *  we have only one DB-Query and not multiple queries inside the for loop
     *
     * @return \Illuminate\Database\Eloquent\Collection - empty by default - filled with OIDs if it's possible to increase performance
     */
    private function _get_oid_collection()
    {
        // Performance improvement (needs only 33% of the time for 35 OIDs)
        // Get OID Collection (all read-write OIDs) for NetElements that dont have a table parameter only once before the foreach loop
        $query = $this->netelement->netelementtype->parameters()->join('oid', 'oid.id', '=', 'parameter.oid_id');
        $has_table = $query->where('oid.oid_table', '=', 1)->count();
        $cnt = $query->where('oid.access', '=', 'read-write')->whereNotIn('oid.unit_divisor', [null, 0])->count();

        if ($has_table || $cnt < 10) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        $oid_ids = $this->netelement->netelementtype->parameters()->get(['oid_id'])->pluck('oid_id')->all();

        return $oids = OID::whereIn('id', $oid_ids)->where('access', '=', 'read-write')->get();
    }

    /**
     * Set the corresponding Values to Configure the Device for a successful snmpset (e.g. needed by kathrein amplifiers)
     * NOTE: If Value is specified the post configuration is done
     *
     * @param   value   the value of the Parameter before the Configuration to reset
     * @return value of Parameter before the configuration, null when resetting the Parameter to this value (specified in argument)
     *
     * @author  Nino Ryschawy
     */
    private function _configure($value = null)
    {
        $type = $this->netelement->netelementtype;

        if ($type->pre_conf_oid_id xor $type->pre_conf_value) {
            Log::debug('Snmp Preconfiguration settings incomplete for this Device (NetElement)', [$this->netelement->name, $this->netelement->id]);

            return;
        }

        $oid = $type->oid;

        // PreConfiguration
        if (! $value) {
            $conf_val = snmpget($this->netelementIp, $this->netelement->community(), $oid->oid.'.0', $this->timeout, $this->retry);

            $ret = false;
            if ($conf_val != $type->pre_conf_value) {
                $ret = snmpset($this->netelementIp, $this->netelement->community('rw'), $oid->oid.'.0', $oid->type, $type->pre_conf_value, $this->timeout, $this->retry);
            }

            $ret ? Log::debug('Preconfigured Device for snmpset', [$this->netelement->name, $this->netelement->id]) : Log::debug('Failed to Preconfigure Device for snmpset', [$this->netelement->name, $this->netelement->id]);

            // wait time in usec
            $sleep_time = $type->pre_conf_time_offset * 1000000 ?: 0;
            usleep($sleep_time);

            return $conf_val;
        }

        // PostConfiguration
        snmpset($this->netelementIp, $this->netelement->community('rw'), $oid->oid.'.0', $oid->type, $value, $this->timeout, $this->retry);

        Log::debug('Postconfigured Device for snmpset', [$this->netelement->name, $this->netelement->id]);
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
        $community = $this->netelement->community('rw');

        // catch all OIDs that could not be set to print later in error message
        try {
            // NOTE: snmp2_set is also available
            $ret = snmpset($this->netelementIp, $community, $oid, $type, $value, $this->timeout, $this->retry);
        } catch (\ErrorException $e) {
            Log::error('snmpset failed with msg: '.$e->getMessage(), [$this->netelementIp, $community, $type, $value]);

            return false;
        }

        Log::debug('snmpset '.$this->netelementIp.' '.$community.' '.$oid.' '.$value.' '.$type, [$ret]);

        return $ret;
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
     *
     * @throws exception when device is not reachable
     */
    public static function check_reachability(Exception $e)
    {
        $msg = $e->getMessage();

        if (stripos($msg, 'Name or service not known') !== false || stripos($msg, 'No response from') !== false) {
            // This interrupts the code and doesn't lead to further SNMP queries
            throw new Exception(trans('messages.snmp.unreachable'));
        }
    }
}
