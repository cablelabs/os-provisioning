<?php

use Models\Modem;
use Models\Endpoint;
use Models\Configfile;
use Models\Quality;


class ModemController extends \BaseController {

	/**
	 * Make Checkbox Default Input
	 * see: see http://forumsarchive.laravel.io/viewtopic.php?id=11627
	 */
	private function default_input ($data)
	{
		if(!isset($data['public']))$data['public']=0;
		if(!isset($data['network_access']))$data['network_access']=0;

		return $data;
	}

	/**
	 * return all Configfile Objects for CMs
	 */
	private function configfiles ()
	{
		return Configfile::where('device', '=', 'CM')->where('public', '=', 'yes')->get();
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

		//return View::make('Modem.ping', compact('modem'))->with('out', $ret);	
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
	

	/**
	 * Display a listing of modems
	 *
	 * @return Response
	 */
	public function index()
	{
		$modems = Modem::all();

		return View::make('Modem.index', compact('modems'));
	}


	/**
	 * Show the form for creating a new modem
	 *
	 * @return Response
	 */
	public function create()
	{
		$configfiles = $this->html_list($this->configfiles(), 'name');
		$qualities = $this->html_list(Quality::all(), 'name');

		return View::make('Modem.create', compact('configfiles', 'qualities'));
	}


	/**
	 * Show the form for editing the specified modem.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$modem = Modem::find($id);
		$mac   = $modem->mac;
		$out = $this->search_lease('agent.remote-id '.$mac);
		$configfiles = $this->html_list($this->configfiles(), 'name');
		$qualities = $this->html_list(Quality::all(), 'name');

		return View::make('Modem.edit', compact('modem', 'out', 'configfiles', 'qualities'));
	}


	/**
	 * Store a newly created modem in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = $this->default_input(Input::all()), Modem::rules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = Modem::create($data)->id;

		return Redirect::route('Modem.edit', $id);
	}


	/**
	 * Update the specified modem in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$modem = Modem::findOrFail($id);

		$validator = Validator::make($data = $this->default_input(Input::all()), Modem::rules($id));
		
		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$modem->update($data);

		return Redirect::route('Modem.edit', $id);
	}

	/**
	 * Remove the specified modem from storage.
	 *
	 * @param  int  $id: bulk delete if == 0
	 * @return Response
	 */
	public function destroy($id)
	{
		if ($id == 0)
		{
			// bulk delete
			// TODO: put to base controller -> make it generic
			foreach (Input::all()['ids'] as $id => $val)
				Modem::destroy($id);
		}
		else
			Modem::destroy($id);

		return $this->index();
	}
	
}
