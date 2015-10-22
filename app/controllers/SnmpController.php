<?php

class SnmpController extends \BaseController {

	private $ip;
	private $comm_ro;
	private $comm_rw;

	private $timeout = 300000;
	private $retry = 1;

	private $model;

	private $mibs = array ();



	public function snmp_init ($model = null, $mibs = null, $ip = null, $ro = 'public', $rw = 'private')
	{
		$this->model = $model;
		$this->mibs  = $mibs;

		$this->ip = $ip;
		$this->comm_ro = $ro;
		$this->comm_rw = $rw;
	}

	public function __construct ($model = null, $mibs = null, $ip = null, $ro = 'public', $rw = 'private')
	{
		$this->snmp_init ($model, $mibs, $ip, $ro, $rw);
	}


	public function snmp_get ($field, $oid, $list = null)
	{
		$this->snmp_def_mode();

		$a = snmpget($this->ip, $this->comm_ro, $oid, $this->timeout, $this->retry);

		Log::info('snmp: get '.$this->snmp_log().' '.$oid.' '.$a);

		if (!$a)
			return false;

		if ($list)
			$this->model->{$field} = $list[$a];
		else
			$this->model->{$field} = $a;

		return $a;
	}


	public function snmp_set ($field, $oid, $type, $list = null)
	{
		$this->snmp_def_mode();
		$value = $this->model->{$field};

		if($list)
			$value = array_search($value, $list);

		$x = snmpget ($this->ip, $this->comm_ro, $oid, $this->timeout, $this->retry);

		if ($x === FALSE)
		{
			return FALSE;
		}

		if ($x == $value)
		{
			return false;
		}

		Log::info('snmp: set diff '.$this->snmp_log().' '.$oid.' '.$type.' '.$value.' '.$x);
		// return snmpset($this->ip, $this->comm_rw, $oid, $type, $value, $this->timeout, $this->retry);
	}


    public function snmp_get_all()
    {
    	foreach ($this->mibs as $mib) 
    		$this->snmp_get($mib[1], $mib[2], isset($mib[4]) ? $mib[4]:null);

    	$this->model->save();
    }


    public function snmp_set_all()
    {
    	foreach ($this->mibs as $mib) 
    		$this->snmp_set($mib[1], $mib[2], $mib[3], isset($mib[4]) ? $mib[4]:null);
    }


    public function dd()
    {
    	dd ($this->ip, $this->comm_ro, $this->comm_rw, $this->timeout, $this->retry, $this->model, $this->mibs);
    }


    private function snmp_log()
    {
    	return $this->ip;
    }


	private function snmp_def_mode()
	{
        snmp_set_quick_print(TRUE);
        snmp_set_oid_numeric_print(TRUE);
        snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
        snmp_set_oid_output_format (SNMP_OID_OUTPUT_NUMERIC);
	}

}
