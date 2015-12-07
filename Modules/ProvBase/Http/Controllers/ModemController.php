<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\Configfile;
use Modules\ProvBase\Entities\Qos;


class ModemController extends \BaseModuleController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'options' => ['readonly']),
			array('form_type' => 'select', 'name' => 'contract_id', 'description' => 'Contract', 'value' => $model->html_list($model->contracts(), 'id')),
			array('form_type' => 'text', 'name' => 'mac', 'description' => 'MAC adress'),
			array('form_type' => 'select', 'name' => 'configfile_id', 'description' => 'Configfile', 'value' => $model->html_list($model->configfiles(), 'name')),
			array('form_type' => 'checkbox', 'name' => 'public', 'description' => 'Public CPE', 'value' => '1'),
			array('form_type' => 'checkbox', 'name' => 'network_access', 'description' => 'Network Access', 'value' => '1'),
			array('form_type' => 'select', 'name' => 'qos_id', 'description' => 'Quality', 'value' => $model->html_list($model->qualities(), 'name'), 'space' => '1'),

			array('form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname'),
			array('form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname'),
			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street'),
			array('form_type' => 'text', 'name' => 'zip', 'description' => 'Postcode'),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City', 'space' => '1'),

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
	 * Display a listing of all Modem objects
	 *
	 * Changes to BaseController: Topography Mode when HfcCustomer Module is active
	 *
	 * @author Torsten Schmidt
	 */
	public function index()
	{
		if(!$this->get_model_obj()->module_is_active ('HfcCustomer'))
			return parent::index();

		$modems = Modem::where('id', '>', '0');

		if (\Input::get('topo') == '1')
		{
			// Generate KML file 
			$customer = new \Modules\HfcCustomer\Http\Controllers\CustomerTopoController;
			$file     = $customer->kml_generate ($modems);

			$view_header_right = 'Topography';
			$body_onload       = 'init_for_map';
		}

		// Prepare
		$panel_right = [['name' => 'List', 'route' => 'Modem.index', 'link' => ['topo' => '0']], 
						['name' => 'Topography', 'route' => 'Modem.index', 'link' => ['topo' => '1']]];

		$target      = ''; // TODO: use global define
		$view_var    = $modems->get();
		$route_name  = 'Modem';
		$view_header = "Modems";
		$create_allowed = $this->index_create_allowed;

		$preselect_field = \Input::get('preselect_field');
		$preselect_value = \Input::get('preselect_value');

		return \View::make('provbase::Modem.index', $this->compact_prep_view(compact('panel_right', 'view_header_right', 'view_var', 'create_allowed', 'file', 'target', 'route_name', 'view_header', 'body_onload', 'field', 'search', 'preselect_field', 'preselect_value')));
	}


	/**
	 * Perform a fulltext search.
	 *
	 * Changes to BaseController: 
	 *  - Topography Mode when HfcCustomer Module is active
	 *  - also search for Contracts while searching for Modems
	 *
	 * @author Torsten Schmidt
	 */
	public function fulltextSearch() 
	{
		$obj    = $this->get_model_obj();

		if(!$obj->module_is_active ('HfcCustomer'))
			return parent::fulltextSearch();

		// get the search scope
		$scope = \Input::get('scope');
		$mode  = \Input::get('mode');
		$query = \Input::get('query');
		$pre_f = \Input::get('preselect_field');
		$pre_v = \Input::get('preselect_value');
		$pre_t = '';

		// perform Modem search
		$modems = $obj->getFulltextSearchResults($scope, $mode, $query, $pre_f, $pre_v)[0];
		
		// perform contract search
		$obj = new \Modules\ProvBase\Entities\Contract;
		$contracts = $obj->getFulltextSearchResults('contract', $mode, $query, $pre_f, $pre_v)[0];

		// generate Topography
		if (\Input::get('topo') == '1')
		{
			// Generate KML file 
			$customer = new \Modules\HfcCustomer\Http\Controllers\CustomerTopoController;
			$file     = $customer->kml_generate ($modems);

			$view_header_right = 'Topography';
			$body_onload       = 'init_for_map';
		}

		if ($pre_f && $pre_v)
			$pre_t = ' Search in '.strtoupper($pre_f).' '.\Modules\HfcBase\Entities\Tree::find($pre_v)->name;

		$panel_right = [['name' => 'List', 'route' => 'Modem.fulltextSearch', 'link' => ['topo' => '0', 'scope' => $scope, 'mode' => $mode, 'query' => $query, 'preselect_field' => $pre_f, 'preselect_value' => $pre_v]], 
						['name' => 'Topography', 'route' => 'Modem.fulltextSearch', 'link' => ['topo' => '1', 'scope' => $scope, 'mode' => $mode, 'query' => $query, 'preselect_field' => $pre_f, 'preselect_value' => $pre_v]]];

		$view_var    = $modems->get();
		$view_var    = $view_var->merge($contracts->get());
		$route_name  = 'Modem';
		$view_header = 'Modems '.$pre_t;
		$create_allowed = $this->index_create_allowed;

		$preselect_field = \Input::get('preselect_field');
		$preselect_value = \Input::get('preselect_value');

		return \View::make('provbase::Modem.index', $this->compact_prep_view(compact('panel_right', 'view_header_right', 'view_var', 'create_allowed', 'file', 'target', 'route_name', 'view_header', 'body_onload', 'field', 'search', 'preselect_field', 'preselect_value')));
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

		return View::make('provbase::Modem.ping', compact('modem', 'ret'));

	}

	
	/**
	 * Monitoring
	 *
	 * @return Response
	 */
	public function monitoring($id)
	{
		$modem = Modem::find($id);

		return View::make('provbase::Modem.monitoring', compact('modem'));
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
		return View::make('provbase::Modem.lease', compact('modem'))->with('out', $ret);
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

		return View::make('provbase::Modem.log', compact('modem', 'out'));
	}

}
