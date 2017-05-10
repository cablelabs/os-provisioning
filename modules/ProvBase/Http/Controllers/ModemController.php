<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\Configfile;
use Modules\ProvBase\Entities\Qos;

use App\Exceptions\AuthExceptions;


class ModemController extends \BaseController {

	protected $index_create_allowed = false;
	protected $save_button = 'Save / Restart';

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		$pos = explode(',', \Input::get('pos'));
		if(count($pos) == 2)
			list($model['x'], $model['y']) = $pos;

		$installation_address_change_date_options = ['placeholder' => 'YYYY-MM-DD'];
		// check if installation_address_change_date is readonly (address change has been sent to Envia API)
		if (
			($model['installation_address_change_date'])
			&&
			(\PPModule::is_active('provvoipenvia'))
		) {
			$orders = \Modules\ProvVoipEnvia\Entities\EnviaOrder::
				where('modem_id', '=', $model->id)->
				where('method', '=', 'contract/relocate')->
				where('orderdate', '>=', $model['installation_address_change_date'])->
				where('contractreference', '<>', $model->contract_external_id)->
				get();

			if ($orders->count() > 0) {
				array_push($installation_address_change_date_options, 'readonly');
			}
		}

		// label has to be the same like column in sql table
		$a = array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'options' => ['readonly'], 'hidden' => 'C'),
			// TODO: show this dropdown only if necessary (e.g. not if creating a modem from contract context)
			array('form_type' => 'select', 'name' => 'contract_id', 'description' => 'Contract', 'hidden' => 'E', 'value' => $model->html_list($model->contracts(), 'lastname')),
			array('form_type' => 'text', 'name' => 'mac', 'description' => 'MAC Address', 'options' => ['placeholder' => 'AA:BB:CC:DD:EE:FF'], 'help' => trans('helper.mac_formats')),
			array('form_type' => 'select', 'name' => 'configfile_id', 'description' => 'Configfile', 'value' => $model->html_list($model->configfiles(), 'name')),
			array('form_type' => 'checkbox', 'name' => 'public', 'description' => 'Public CPE', 'value' => '1'),
			array('form_type' => 'checkbox', 'name' => 'network_access', 'description' => 'Network Access', 'value' => '1', 'help' => trans('helper.Modem_NetworkAccess'))
			);

		$b = \PPModule::is_active('billingbase') ? 
			array(array('form_type' => 'select', 'name' => 'qos_id', 'description' => 'QoS', 'value' => $model->html_list($model->qualities(), 'name'), 'hidden' => 1, 'space' => '1'))
			:
			array(array('form_type' => 'select', 'name' => 'qos_id', 'description' => 'QoS', 'value' => $model->html_list($model->qualities(), 'name'), 'space' => '1'));

		$geopos = link_to_route('CustomerModem.show', 'Geopos X/Y', ['true', $model->id]);
		$c = array(
			array('form_type' => 'text', 'name' => 'company', 'description' => 'Company'),
			array('form_type' => 'text', 'name' => 'department', 'description' => 'Department'),
			array('form_type' => 'select', 'name' => 'salutation', 'description' => 'Salutation', 'value' => $model->get_salutation_options()),
			array('form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname'),
			array('form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname'),
			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street'),
			array('form_type' => 'text', 'name' => 'house_number', 'description' => 'House Number'),
			array('form_type' => 'text', 'name' => 'zip', 'description' => 'Postcode'),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City'),
			array('form_type' => 'text', 'name' => 'installation_address_change_date', 'description' => 'Date of installation address change', 'hidden' => 'C', 'options' => $installation_address_change_date_options, 'help' => trans('helper.Modem_InstallationAddressChangeDate')), // Date of adress change for notification at telephone provider - important for localisation of emergency calls
			array('form_type' => 'text', 'name' => 'district', 'description' => 'District'),
			array('form_type' => 'text', 'name' => 'birthday', 'description' => 'Birthday', 'space' => '1', 'options' => ['placeholder' => 'YYYY-MM-DD']),

			array('form_type' => 'text', 'name' => 'serial_num', 'description' => 'Serial Number'),
			array('form_type' => 'text', 'name' => 'inventar_num', 'description' => 'Inventar Number'),

			array('form_type' => 'text', 'name' => 'x', 'description' => 'Geopos X', 'html' =>
				"<div class=col-md-12 style='background-color:#e0f2f1'>
				<div class=form-group><label for=x class='col-md-4 control-label' style='margin-top: 10px;'>$geopos</label>
				<div class=col-md-3><input class=form-control name=x type=text value='".$model['x']."' id=x style='background-color:#e0f2f1'></div>"),
			array('form_type' => 'text', 'name' => 'y', 'description' => 'Geopos Y', 'html' =>
				"<div class=col-md-3><input class=form-control name=y type=text value='".$model['y']."' id=y style='background-color:#e0f2f1'></div>
				</div></div>"),

			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description')
		);

		return array_merge($a, $b, $c);
	}


	protected function prepare_input_post_validation($data)
	{
		return unify_mac($data);
	}


	/**
	 * Get all management jobs for Envia
	 *
	 * @author Patrick Reichel
	 * @param $modem current modem object
	 * @return array containing linktexts and URLs to perform actions against REST API
	 */
	public static function _get_envia_management_jobs($modem) {

		$provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

		// check if user has the right to perform actions against Envia API
		// if not: don't show any actions
		try {
			\App\Http\Controllers\BaseAuthController::auth_check('view', 'Modules\ProvVoipEnvia\Entities\ProvVoipEnvia');
		}
		catch (PermissionDeniedError $ex) {
			return null;
		}

		return $provvoipenvia->get_jobs_for_view($modem, 'modem');
	}


	/*
	 * Modem Tabs Controller. -> Panel Header Right
	 * See: BaseController native function for more infos
	 *
	 * @param view_var: the model object to be displayed
	 * @return: array, e.g. [['name' => '..', 'route' => '', 'link' => [$view_var->id]], .. ]
	 * @author: Torsten Schmidt
	 */
	protected function get_form_tabs($view_var)
	{
		$a = [
			['name' => 'Edit', 'route' => 'Modem.edit', 'link' => [$view_var->id]],
		];

		if(!\PPModule::is_active('ProvMon'))
			return $a;

		array_push($a, ['name' => 'Analyses', 'route' => 'Provmon.index', 'link' => [$view_var->id]]);
		array_push($a, ['name' => 'CPE-Analysis', 'route' => 'Provmon.cpe', 'link' => [$view_var->id]]);

		// MTA: only show MTA analysis if Modem has MTAs
		if (isset($view_var->mtas) && isset($view_var->mtas[0]))
			array_push($a, ['name' => 'MTA-Analysis', 'route' => 'Provmon.mta', 'link' => [$view_var->id]]);

		// add tab for GuiLog
		array_push($a, parent::get_form_tabs($view_var)[0]);

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

		if(!\PPModule::is_active('HfcCustomer'))
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

		if(!\PPModule::is_active ('HfcCustomer'))
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
			$pre_t = ' Search in '.strtoupper($pre_f).' '.\Modules\HfcReq\Entities\NetElement::find($pre_v)->name;

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
	 * Set nullable fields.
	 *
	 * @author Patrick Reichel
	 */
	public function prepare_input($data)
	{
		$data = parent::prepare_input($data);

		// set this to null if no value is given
		$nullable_fields = array(
			'contract_ext_creation_date',
			'contract_ext_termination_date',
			'installation_address_change_date',
		);
		$data = $this->_nullify_fields($data, $nullable_fields);

		return $data;
	}

}
