<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcSnmp\Entities\SnmpValue;
use Modules\HfcSnmp\Entities\SnmpMib;

use Log;

class SnmpController extends \BaseModuleController{

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
	 * Create or Update SnmpValue Object which corresponds
	 * to $snmpmib and $this->device with $value
	 *
	 * @param snmpmib the SnmpMib Object
	 * @param value from snmpget command for this snmpmib object
	 * @return the snmpmib->id
	 *
	 * @author Torsten Schmidt
	 */
	private function snmp_value_set ($snmpmib, $value)
	{
		$obj = SnmpValue::where('device_id', '=', $this->device->id)->
						  where('snmpmib_id','=', $snmpmib->id)->
						  where('oid_index', '=', $snmpmib->oid)->get();

		if (isset($obj[0]))
			$obj = $obj[0];
		else
		{
			$obj = new SnmpValue;
			$obj->device_id  = $this->device->id;
			$obj->snmpmib_id = $snmpmib->id;
			$obj->oid_index  = $snmpmib->oid;
		}

		$obj->value = $value;
		
		if (!$obj->save())
			return false;

		return $obj->id;
	}


	/**
	 * Create or Update SnmpValue Object which corresponds
	 * to $snmpmib and $this->device with $value
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
	 * make a snmpwalk over the entire $snmpmib->oid 
	 * and create/update related SnmpValue Objects
	 *
	 * @param snmpmib the SnmpMib Object
	 * @return array of snmpwalk over oid in format [SnmpValue object id, snmp value] 
	 *
	 * @author Torsten Schmidt
	 */
	public function snmp_walk ($snmpmib)
	{
		// Walk
		$walk = snmpwalkoid($this->device->ip, $this->device->community_ro, $snmpmib->oid, $this->timeout, $this->retry);

		// Log
		Log::info('snmp: get '.$this->snmp_log().' '.$snmpmib->oid.' '.implode(' ',$walk));

		// Fetch Walk and write result to SnmpValue Objects (DB)
		$ret = array();
		foreach ($walk as $snmpmib->oid => $v)
		{
			if (!$v)
				return false;

			$b = $this->snmp_value_set($snmpmib, $v);

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

		$snmpmib = SnmpMib::findOrFail($snmpvalue->snmpmib_id);

		Log::info('snmp: set diff '.$this->snmp_log().' '.$snmpvalue->oid_index.' '.$snmpmib->type.' '.$snmpvalue->value.' '.$x);
		// return snmpset($this->device->ip, $this->device->community_rw, $oid, $type, $value, $this->timeout, $this->retry);
	}


	/**
	 * Get all SNMP Values of this Controller
	 * and save results in SnmpValue Object
	 *
	 * @return form_fields array for generic edit view function
	 *
	 * @author Torsten Schmidt
	 */
    public function snmp_get_all()
    {
    	$ret = array();

    	foreach ($this->device->devicetype->snmpmibs as $mib)  
    	{
    		foreach ($this->snmp_walk($mib) as $a)
    		{
    			$options = null;
    			$value = $a[1];

    			if($mib->type_array)
    			{
    				$options = $a[1];
    				$value = $this->string_to_array($mib->type_array);
    			}

    			array_push($ret, ['form_type' => $mib->html_type, 'name' => 'field_'.$a[0], 'description' => $mib->field, 'value' => $value, 'options' => $options]);
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
