<?php

namespace Modules\ProvBase\Http\Controllers;

use Bouncer;
use App\GlobalConfig;
use Modules\ProvBase\Entities\Modem;

class ModemController extends \BaseController
{
    protected $index_create_allowed = false;
    protected $save_button_name = 'Save / Restart';
    protected $save_button_title_key = 'modem_save_button_title';

    // save button title ? for a help message
    protected $edit_view_second_button = true;
    protected $second_button_name = 'Restart via CMTS';
    protected $second_button_title_key = 'modem_force_restart_button_title';

    public function __construct()
    {
        if (\Modules\ProvBase\Entities\ProvBase::first()->additional_modem_reset) {
            $this->edit_view_third_button = true;
            $this->third_button_name = 'Reset Modem';
            $this->third_button_title_key = 'modem_reset_button_title';
        }

        parent::__construct();
    }

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new Modem;
        }

        if (! $model->exists) {
            $config = GlobalConfig::find(1);
            $model->country_code = $config->default_country_code;
        }

        $pos = explode(',', \Input::get('pos'));
        if (count($pos) == 2) {
            [$model['x'], $model['y']] = $pos;
        }

        $installation_address_change_date_options = ['placeholder' => 'YYYY-MM-DD'];
        // check if installation_address_change_date is readonly (address change has been sent to envia TEL API)
        if (
            ($model['installation_address_change_date'])
            &&
            (\Module::collections()->has('ProvVoipEnvia'))
        ) {
            $orders = \Modules\ProvVoipEnvia\Entities\EnviaOrder::
                where('modem_id', '=', $model->id)->
                where('method', '=', 'contract/relocate')->
                where('orderdate', '>=', $model['installation_address_change_date'])->
                get();

            foreach ($orders as $order) {
                if (! \Modules\ProvVoipEnvia\Entities\EnviaOrder::orderstate_is_final($order)) {
                    array_push($installation_address_change_date_options, 'readonly');
                }
            }
        }

        // label has to be the same like column in sql table
        $a = [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name'],
            ['form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'options' => ['readonly'], 'hidden' => 'C'],
            // TODO: show this dropdown only if necessary (e.g. not if creating a modem from contract context)
            ['form_type' => 'select', 'name' => 'contract_id', 'description' => 'Contract', 'hidden' => 'E', 'value' => $model->html_list($model->contracts(), 'lastname')],
            ['form_type' => 'text', 'name' => 'mac', 'description' => 'MAC Address', 'options' => ['placeholder' => 'AA:BB:CC:DD:EE:FF'], 'help' => trans('helper.mac_formats')],
            ['form_type' => 'select', 'name' => 'configfile_id', 'description' => 'Configfile', 'value' => $model->html_list_with_count($model->configfiles(), 'name', false, '', 'configfile_id', 'modem'), 'help' => trans('helper.configfile_count')],
            ['form_type' => 'checkbox', 'name' => 'public', 'description' => 'Public CPE', 'value' => '1'],
            ['form_type' => 'checkbox', 'name' => 'internet_access', 'description' => 'Internet Access', 'value' => '1', 'help' => trans('helper.Modem_InternetAccess')],
            ];

        $b = \Module::collections()->has('BillingBase') ?
            [['form_type' => 'text', 'name' => 'qos_id', 'description' => 'QoS', 'hidden' => 1, 'space' => '1']]
            :
            [['form_type' => 'select', 'name' => 'qos_id', 'description' => 'QoS', 'value' => $model->html_list($model->qualities(), 'name'), 'space' => '1']];

        if (\Module::collections()->has('HfcCustomer')) {
            $rect = [round($model->x, 4) - 0.0001, round($model->x, 4) + 0.0001, round($model->y, 4) - 0.0001, round($model->y, 4) + 0.0001];
            $geopos = link_to_route('CustomerModem.show', 'Geopos X/Y', ['true', $model->id]).'    ('.link_to_route('CustomerRect.show', trans('messages.proximity'), $rect).')';
        } else {
            $geopos = 'Geopos X/Y';
        }

        $c = [
            ['form_type' => 'text', 'name' => 'company', 'description' => 'Company'],
            ['form_type' => 'text', 'name' => 'department', 'description' => 'Department'],
            ['form_type' => 'select', 'name' => 'salutation', 'description' => 'Salutation', 'value' => $model->get_salutation_options()],
            ['form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname'],
            ['form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname'],
            ['form_type' => 'text', 'name' => 'street', 'description' => 'Street'],
            ['form_type' => 'text', 'name' => 'house_number', 'description' => 'House Number'],
            ['form_type' => 'text', 'name' => 'zip', 'description' => 'Postcode'],
            ['form_type' => 'text', 'name' => 'city', 'description' => 'City'],
            ['form_type' => 'text', 'name' => 'district', 'description' => 'District'],
            ['form_type' => 'text', 'name' => 'country_code', 'description' => 'Country code', 'help' => 'ISO 3166 ALPHA-2 (two characters)'],
            ['form_type' => 'text', 'name' => 'installation_address_change_date', 'description' => 'Date of installation address change', 'hidden' => 'C', 'options' => $installation_address_change_date_options, 'help' => trans('helper.Modem_InstallationAddressChangeDate')], // Date of adress change for notification at telephone provider - important for localisation of emergency calls
            ['form_type' => 'text', 'name' => 'birthday', 'description' => 'Birthday', 'space' => '1', 'options' => ['placeholder' => 'YYYY-MM-DD']],

            ['form_type' => 'text', 'name' => 'serial_num', 'description' => 'Serial Number'],
            ['form_type' => 'text', 'name' => 'inventar_num', 'description' => 'Inventar Number'],

            ['form_type' => 'text', 'name' => 'x', 'description' => 'Geopos X', 'html' => "<div class=col-md-12 style='background-color:whitesmoke'>
				<div class='form-group row'><label for=x class='col-md-4 control-label' style='margin-top: 10px;'>$geopos</label>
				<div class=col-md-3><input class=form-control name=x type=text value='".$model['x']."' id=x style='background-color:whitesmoke'></div>"],
            ['form_type' => 'text', 'name' => 'y', 'description' => 'Geopos Y', 'html' => "<div class=col-md-3><input class=form-control name=y type=text value='".$model['y']."' id=y style='background-color:whitesmoke'></div>
				</div></div>"],

            ['form_type' => 'text', 'name' => 'geocode_source', 'description' => 'Geocode origin', 'help' => trans('helper.Modem_GeocodeOrigin')],
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
        ];

        return array_merge($a, $b, $c);
    }

    /**
     * Get all management jobs for envia TEL
     *
     * @author Patrick Reichel
     * @param $modem current modem object
     * @return array containing linktexts and URLs to perform actions against REST API
     */
    public static function _get_envia_management_jobs($modem)
    {
        $provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

        if (Bouncer::can('view', 'Modules\ProvVoipEnvia\Entities\ProvVoipEnvia')) {
            return $provvoipenvia->get_jobs_for_view($modem, 'modem');
        }
    }

    /**
     * Modem Tabs Controller. -> Panel Header Right
     * See: BaseController native function for more information
     *
     * @param Modules\ProvBase\Entities\Modem
     * @return: array, e.g. [['name' => '..', 'route' => '', 'link' => [$view_var->id]], .. ]
     * @author: Torsten Schmidt
     */
    protected function editTabs($model)
    {
        // defines which edit page you came from
        \Session::put('Edit', 'Modem');

        $tabs = parent::editTabs($model);

        if (! \Module::collections()->has('ProvMon')) {
            return $tabs;
        }

        if (\Bouncer::can('view_analysis_pages_of', Modem::class)) {
            array_push($tabs, ['name' => 'Analyses', 'route' => 'ProvMon.index', 'link' => $model->id],
                ['name' => 'CPE-Analysis', 'route' => 'ProvMon.cpe', 'link' => $model->id]);

            // MTA: only show MTA analysis if Modem has MTA's
            if (isset($model->mtas) && isset($model->mtas[0])) {
                array_push($tabs, ['name' => 'MTA-Analysis', 'route' => 'ProvMon.mta', 'link' => $model->id]);
            }
        }

        return $tabs;
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
        $obj = static::get_model_obj();

        if (! \Module::collections()->has('HfcCustomer')) {
            return parent::fulltextSearch();
        }

        // get the search scope
        $scope = \Input::get('scope');
        $mode = \Input::get('mode');
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
        if (\Input::get('topo') == '1') {
            // Generate KML file
            $customer = new \Modules\HfcCustomer\Http\Controllers\CustomerTopoController;
            $file = $customer->kml_generate($modems);

            $view_header_right = 'Topography';
            $body_onload = 'init_for_map';
        }

        if ($pre_f && $pre_v) {
            $pre_t = ' Search in '.strtoupper($pre_f).' '.\Modules\HfcReq\Entities\NetElement::find($pre_v)->name;
        }

        $tabs = [['name' => 'List', 'route' => 'Modem.fulltextSearch', 'link' => ['topo' => '0', 'scope' => $scope, 'mode' => $mode, 'query' => $query, 'preselect_field' => $pre_f, 'preselect_value' => $pre_v]],
                        ['name' => 'Topography', 'route' => 'Modem.fulltextSearch', 'link' => ['topo' => '1', 'scope' => $scope, 'mode' => $mode, 'query' => $query, 'preselect_field' => $pre_f, 'preselect_value' => $pre_v]], ];

        $view_var = $modems->get();
        $view_var = $view_var->merge($contracts->get());
        $route_name = 'Modem';
        $view_header = 'Modems '.$pre_t;
        $create_allowed = $this->index_create_allowed;

        $preselect_field = \Input::get('preselect_field');
        $preselect_value = \Input::get('preselect_value');

        return \View::make('provbase::Modem.index', $this->compact_prep_view(compact('tabs', 'view_header_right', 'view_var', 'create_allowed', 'file', 'target', 'route_name', 'view_header', 'body_onload', 'field', 'search', 'preselect_field', 'preselect_value')));
    }

    /**
     * Return tree view of all used firmwares
     *
     * @return View
     *
     * @author Ole Ernst
     */
    public function firmware_view()
    {
        $view_var = Modem::get_firmware_tree();

        $headline = $view_header = 'Firmware';
        $create_allowed = false;

        return \View::make('Generic.tree', $this->compact_prep_view(compact('headline', 'view_header', 'view_var', 'create_allowed')));
    }

    /**
     * Return status of modem via API
     *
     * @return JsonResponse
     *
     * @author Ole Ernst
     */
    public function api_status($ver, $id)
    {
        if ($ver !== '0') {
            return response()->json(['ret' => "Version $ver not supported"]);
        }

        if (! $modem = static::get_model_obj()->find($id)) {
            return response()->json(['ret' => 'Object not found']);
        }

        $domain_name = \Modules\ProvBase\Entities\ProvBase::first()->domain_name;
        exec("sudo ping -c1 -i0 -w1 {$modem->hostname}.$domain_name", $ping, $offline);

        return response()->json(['ret' => 'success', 'online' => ! $offline]);
    }

    /**
     * Restart modem via API
     *
     * @return JsonResponse
     *
     * @author Ole Ernst
     */
    public function api_restart($ver, $id)
    {
        if ($ver !== '0') {
            return response()->json(['ret' => "Version $ver not supported"]);
        }

        if (! $modem = static::get_model_obj()->find($id)) {
            return response()->json(['ret' => 'Object not found']);
        }

        $modem->restart_modem();

        $err = collect([
            \Session::get('tmp_info_above_form'),
            \Session::get('tmp_warning_above_form'),
            \Session::get('tmp_error_above_form'),
        ])->collapse()
        ->implode(', ');

        return response()->json(['ret' => $err ?: 'success']);
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
        $nullable_fields = [
            'contract_ext_creation_date',
            'contract_ext_termination_date',
            'installation_address_change_date',
        ];
        $data = $this->_nullify_fields($data, $nullable_fields);

        // ISO 3166 country codes are uppercase
        $data['country_code'] = \Str::upper($data['country_code']);

        return unify_mac($data);
    }

    public function prepare_rules($rules, $data)
    {
        if (! \Module::collections()->has('BillingBase')) {
            $rules['qos_id'] = 'required|exists:qos,id,deleted_at,NULL';
        }

        return parent::prepare_rules($rules, $data);
    }

    /**
     * Inheritet update function to handle force restart button as
     * we dont want to update the modem when this button is clicked
     */
    public function update($id)
    {
        if (! \Input::has('_2nd_action') && ! \Input::has('_3rd_action')) {
            return parent::update($id);
        }

        $modem = Modem::find($id);
        $modem->restart_modem(false, \Input::has('_3rd_action'));

        return \Redirect::back();
    }
}
