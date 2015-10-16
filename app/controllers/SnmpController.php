<?php

class SnmpController {

	private $ip;
	private $comm_ro;
	private $comm_rw;

	private $timeout = 300000;
	private $retry = 1;

	private $model;

	protected $mibs = array ();


	public function snmp_set_ip ($ip, $ro = 'public', $rw = 'private')
	{
		$this->ip = $ip;
		$this->comm_ro = $ro;
		$this->comm_rw = $rw;
	}	

	public function snmp_set_mibs($mibs)
	{
		$this->mibs=$mibs;
	}

	public function snmp_set_model ($model)
	{
		$this->model = $model;
	}


	public function __construct ($model = null, $mibs = null, $ip = null, $ro = 'public', $rw = 'private')
	{
		$this->model = $model;
		$this->mibs  = $mibs;

		$this->ip = $ip;
		$this->comm_ro = $ro;
		$this->comm_rw = $rw;
	}


	public function snmp_get ($field, $oid, $list = null)
	{
		Log::info('snmp: get '.$this->snmp_log().' '.$oid);
		$this->snmp_def_mode();

		$a = snmpget($this->ip, $this->comm_ro, $oid, $this->timeout, $this->retry);

		if (!$a)
			return false;

		if ($list)
			$this->model->{$field} = $list[$a];
		else
			$this->model->{$field} = $a;

		return $a;
	}


	public function snmp_set ($field, $oid, $type, $value)
	{
		Log::info('snmp: set diff '.$this->snmp_log().' '.$oid.' '.$type);
		$this->snmp_def_mode();

		$x = snmpget ($this->comm_ro, $oid, $this->timeout, $this->retry);

		if ($x === FALSE)
		{
			return FALSE;
		}

		if ($x == $value)
		{
			# debug ("snmpsetdiff no change must be transmit");
			return false;
		}

		return snmpset($this->ip, $this->comm_rw, $oid, $type, $value, $this->timeout, $this->retry);
	}


	public function snmp_def_mode()
	{
        snmp_set_quick_print(TRUE);
        snmp_set_oid_numeric_print(TRUE);
        snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
        snmp_set_oid_output_format (SNMP_OID_OUTPUT_NUMERIC);
	}


    public function snmp_get_all()
    {
    	foreach ($this->mibs as $mib) {
    		$this->snmp_get($mib[0], $mib[1], isset($mib[2]) ? $mib[2]:null);
    	}
    	
    }


    public function dd()
    {
    	dd ($this->ip, $this->comm_ro, $this->comm_rw, $this->timeout, $this->retry, $this->model, $this->mibs);
    }


    private function snmp_log()
    {
    	return $this->ip;
    }

}
