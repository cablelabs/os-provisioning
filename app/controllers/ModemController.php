<?php

use Models\Modem;
use Models\Endpoint;
use Models\Configfile;
use Models\Qos;


class ModemController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		if (!$model)
			$model = new Modem;

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'mac', 'description' => 'MAC adress'),
			array('form_type' => 'select', 'name' => 'configfile_id', 'description' => 'Configfile', 'value' => $model->html_list($model->configfiles(), 'name')),
			array('form_type' => 'checkbox', 'name' => 'public', 'description' => 'Public CPE', 'value' => '1'),
			array('form_type' => 'checkbox', 'name' => 'network_access', 'description' => 'Network Access', 'value' => '1'),
			array('form_type' => 'select', 'name' => 'qos_id', 'description' => 'Quality', 'value' => $model->html_list($model->qualities(), 'name')),
			array('form_type' => 'text', 'name' => 'serial_num', 'description' => 'Serial Number'),
			array('form_type' => 'text', 'name' => 'inventar_num', 'description' => 'Inventar Number'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description')
		);
	}

	/**
	 * TODO: make generic
	 * Make Checkbox Default Input
	 * see: see http://forumsarchive.laravel.io/viewtopic.php?id=11627
	 */
	protected function default_input ($data)
	{
		if(!isset($data['public']))$data['public']=0;
		if(!isset($data['network_access']))$data['network_access']=0;

		return $data;
	}



	/**
	 * Ping
	 *
	 * @return Response
	 */
	public function ping($id)
	{
		$modem = Modem::find($id);
		$hostname = $modem->hostname;
		
		if (!exec ('ping -c5 -i0.2 '.$hostname, $ret))
			$ret = array ('Modem is Offline');

		return View::make('Modem.ping', compact('modem', 'ret'));

	}

	
	/**
	 * Monitoring
	 *
	 * @return Response
	 */
	public function monitoring($id)
	{
		$modem = Modem::find($id);

		return View::make('Modem.monitoring', compact('modem'));
	}


	/**
	 * Search String in dhcpd.lease file and
	 * return the matching host
	 *
	 * TODO: make a seperate class for dhcpd
	 * lease stuff (search, replace, ..)
	 *
	 * @return Response
	 */
	public function search_lease ($search)
	{
		// parse dhcpd.lease file
		$file   = file_get_contents('/var/lib/dhcpd/dhcpd.leases');
		$string = preg_replace( "/\r|\n/", "", $file );
		preg_match_all('/lease(.*?)}/', $string, $section);

		$ret = array();
		$i   = 0;

		// fetch all lines matching hw mac
		foreach (array_reverse(array_unique($section[0])) as $s)
		{
		    if(strpos($s, $search)) 
		    {
		    	/*
		    	if ($i == 0)
		    		array_push($ret, "<b>Last Lease:</b>");

		    	if ($i == 1)
		    		array_push($ret, "<br><br><b>Old Leases:</b>");
				*/

		    	// push matching results 
		        array_push($ret, str_replace('{', '{<br>', str_replace(';', ';<br>', $s)));
		        $i++;

if (0)
{
		        // TODO: convert string to array and convert return
		        $a = explode(';', str_replace ('{', ';', $s));

		     	if (!isset($ret[$a[0]]))
		     		$ret[$a[0]] = array();   

		        array_push($ret[$a[0]], $a);
}

		    }
		}

		return $ret;
	}


	/**
	 * Leases
	 *
	 * @return Response
	 */
	public function lease($id)
	{
		$modem = Modem::find($id);
		$mac  = $modem->mac;
		$ret  = $this->search_lease('hardware ethernet '.$mac);

		// view
		return View::make('Modem.lease', compact('modem'))->with('out', $ret);
	}


	/**
	 * Log
	 *
	 * @return Response
	 */
	public function log($id)
	{
		$modem = Modem::find($id);
		$hostname = $modem->hostname;
		$mac      = $modem->mac;
		
		if (!exec ('cat /var/log/messages | egrep "('.$mac.'|'.$hostname.')" | tail -n 100  | sort -r', $ret))
			$out = array ('no logging');

		return View::make('Modem.log', compact('modem', 'out'));
	}

}
