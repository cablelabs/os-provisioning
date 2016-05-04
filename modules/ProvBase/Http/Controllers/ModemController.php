<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\Configfile;
use Modules\ProvBase\Entities\Qos;

use App\Exceptions\AuthExceptions;


class ModemController extends \BaseModuleController {

	protected $index_create_allowed = false;
	protected $save_button = 'Save / Restart';

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'options' => ['readonly']),
			array('form_type' => 'select', 'name' => 'contract_id', 'description' => 'Contract'),
			array('form_type' => 'text', 'name' => 'mac', 'description' => 'MAC adress'),
			array('form_type' => 'select', 'name' => 'configfile_id', 'description' => 'Configfile', 'value' => $model->html_list($model->configfiles(), 'name')),
			array('form_type' => 'checkbox', 'name' => 'public', 'description' => 'Public CPE', 'value' => '1'),
			array('form_type' => 'checkbox', 'name' => 'network_access', 'description' => 'Network Access', 'value' => '1'),
			// TODO: change to hidden field when billing module is active?
			array('form_type' => 'select', 'name' => 'qos_id', 'description' => 'QoS', 'value' => $model->html_list($model->qualities(), 'name'), 'space' => '1'),

			array('form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname'),
			array('form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname'),
			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street'),
			array('form_type' => 'text', 'name' => 'zip', 'description' => 'Postcode'),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City', 'space' => '1'),

			array('form_type' => 'text', 'name' => 'serial_num', 'description' => 'Serial Number'),
			array('form_type' => 'text', 'name' => 'inventar_num', 'description' => 'Inventar Number'),

			array('form_type' => 'text', 'name' => 'x', 'description' => 'Geopos X', 'html' =>
				"<div class=col-md-12 style='background-color:#e0f2f1'>
				<div class=form-group><label for=x class='col-md-3 control-label'>Geopos X/Y</label>
				<div class=col-md-4><input class=form-control name=x type=text value='".$model['x']."' id=x style='background-color:#e0f2f1'></div>"),
			array('form_type' => 'text', 'name' => 'y', 'description' => 'Geopos Y', 'html' =>
				"<div class=col-md-4><input class=form-control name=y type=text value='".$model['y']."' id=y style='background-color:#e0f2f1'></div>
				</div></div>"),

			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description')
		);
	}


	/*
	 * Modem Controller Breadcrumb. -> Panel Header Right
	 * See: BaseController native function for more infos
	 *
	 * @param view_var: the model object to be displayed
	 * @return: array, e.g. [['name' => '..', 'route' => '', 'link' => [$view_var->id]], .. ]
	 * @author: Torsten Schmidt
	 */
	protected function get_form_breadcrumb($view_var)
	{
		$a = [['name' => 'Edit', 'route' => 'Modem.edit', 'link' => [$view_var->id]],
				['name' => 'Analyses', 'route' => 'Provmon.index', 'link' => [$view_var->id]],
				['name' => 'CPE-Analysis', 'route' => 'Provmon.cpe', 'link' => [$view_var->id]]];

		// MTA: only show MTA analysis if Modem has MTAs
		if (isset($view_var->mtas[0]))
			array_push($a, ['name' => 'MTA-Analysis', 'route' => 'Provmon.mta', 'link' => [$view_var->id]]);

		return $a;
	}


	/**
	 * Display a listing of all Modem objects
	 *
	 * Changes to BaseController: Topography Mode when HfcCustomer Module is active
	 *
	 * TODO: - topo mode does not work anymore ?
	 *       - split / use / exit with BaseController function
	 *
	 * @author Torsten Schmidt
	 */
	public function _index_todo()
	{
		\App\Http\Controllers\BaseAuthController::auth_check('view', $this->get_model_name());

		if(!static::get_model_obj()->module_is_active ('HfcCustomer'))
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
		$obj    = static::get_model_obj();

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


}
