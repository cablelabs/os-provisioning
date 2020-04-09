<?php

namespace Modules\HfcReq\Http\Controllers;

use View;
use Request;
use Modules\HfcReq\Entities\NetElement;
use Modules\HfcReq\Entities\NetElementType;
use App\Http\Controllers\BaseViewController;
use Modules\HfcBase\Http\Controllers\HfcBaseController;
use Modules\ProvMon\Http\Controllers\ProvMonController;

class NetElementController extends HfcBaseController
{
    public function index()
    {
        $model = static::get_model_obj();
        $headline = BaseViewController::translate_view($model->view_headline(), 'Header', 2);
        $view_header = BaseViewController::translate_view('Overview', 'Header');
        $create_allowed = static::get_controller_obj()->index_create_allowed;
        $delete_allowed = static::get_controller_obj()->index_delete_allowed;

        return View::make('Generic.index', $this->compact_prep_view(compact('headline', 'view_header', 'model', 'create_allowed', 'delete_allowed')));
    }

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($netelement = null)
    {
        $netelement = $netelement ?: new NetElement;

        $empty_field = $netelement->exists;

        $kml_files = $netelement->kml_files();

        // parse which netelementtype we want to edit/create
        // NOTE: this is for auto reload via HTML GET
        $type = 0;
        if (Request::has('netelementtype_id')) {
            $type = Request::get('netelementtype_id');
        } elseif ($netelement->netelementtype) {
            $type = $netelement->netelementtype->get_base_type();
        }

        $hidden4TapPort = $hidden4Tap = 0;
        $hidden4Tap = $type == 8 ? 1 : 0;
        $addressDesc1 = 'Address Line 1';
        $addressDesc2 = 'Address Line 2';

        if ($type == 8) {
            $addressDesc1 = 'RKS '.trans('messages.Address'); // Used as address to control the attenuation setting via Sat-Kabel-RKS-Server
            $addressDesc2 = trans('messages.Address');
        }

        if ($type == 9) {
            $parents = $netelement->html_list(NetElement::where('netelementtype_id', 8)->get(), 'name');
            $types = NetElementType::where('id', 9)->get(['id', 'name']);
            $hidden4TapPort = 1;

            $addressDesc1 = 'RKS Port'; // Used as address to control the attenuation setting via Sat-Kabel-RKS-Server

        } else {
            $parents = $netelement->getParentList();
            $types = NetElementType::get(['id', 'name']);
        }

        /*
         * provisioning device
         */
        $prov_device = [];
        $prov_device_hidden = 1;

        if ($type == 3) { // netgw
            $prov_device = $netelement->html_list(\Modules\ProvBase\Entities\NetGw::get(['id', 'hostname']), 'hostname', $empty_field);
        }

        if ($type == 4 || $type == 5) { // amp || node
            $prov_device = $netelement->html_list(\DB::table('modem')->where('deleted_at', '=', null)->get(['id', 'name']), ['id', 'name'], $empty_field, ': ');
        }

        if ($prov_device) {
            $prov_device_hidden = 0;
        }

        $cluster = false;
        // netelement is a cluster or will be created as type cluster
        if ($netelement->netelementtype_id == 2 || (! $netelement->exists && Request::get('netelementtype_id') == 2)) {
            $cluster = true;
        }

        // this is just a helper and won't be stored in the database
        $netelement->enable_agc = $netelement->exists && $netelement->agc_offset !== null;

        /*
         * cluster: rf card settings
         * Options array is hidden when not used
         */
        $options_array = ['form_type' => 'text', 'name' => 'options', 'description' => 'Options'];
        if ($netelement->netelementtype && $netelement->netelementtype->get_base_type() == 2) {
            $options_array = ['form_type' => 'select', 'name' => 'options', 'description' => 'RF Card Setting (DSxUS)', 'value' => $netelement->get_options_array()];
        }

        /*
         * return
         */
        $a = [
            ['form_type' => 'select', 'name' => 'netelementtype_id', 'description' => 'NetElement Type', 'value' => $netelement->html_list($types, 'name'), 'hidden' => 0],
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name'],
            // array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => ['NET' => 'NET', 'NETGW' => 'NETGW', 'DATA' => 'DATA', 'CLUSTER' => 'CLUSTER', 'NODE' => 'NODE', 'AMP' => 'AMP']),
            // net is automatically detected in Observer
            // array('form_type' => 'select', 'name' => 'net', 'description' => 'Net', 'value' => $nets),
            ['form_type' => 'ip', 'name' => 'ip', 'description' => 'IP address', 'hidden' => $hidden4TapPort || $hidden4Tap],
            ['form_type' => 'text', 'name' => 'link', 'description' => 'HTML Link', 'hidden' => $hidden4TapPort || $hidden4Tap],
            ['form_type' => 'select', 'name' => 'prov_device_id', 'description' => 'Provisioning Device', 'value' => $prov_device, 'hidden' => $prov_device_hidden],
            ['form_type' => 'text', 'name' => 'pos', 'description' => 'Geoposition', 'hidden' => $hidden4TapPort],
            ['form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Object', 'value' => $parents],
            array_merge($options_array, ['hidden' => $hidden4TapPort || $hidden4Tap]),
            // array('form_type' => 'select', 'name' => 'state', 'description' => 'State', 'value' => ['OK' => 'OK', 'YELLOW' => 'YELLOW', 'RED' => 'RED'], 'options' => ['readonly']),

            ['form_type' => 'select', 'name' => 'kml_file', 'description' => 'Choose KML file', 'value' => $kml_files],
            ['form_type' => 'file', 'name' => 'kml_file_upload', 'description' => 'or: Upload KML file', 'space' => 1],

            ['form_type' => 'text', 'name' => 'community_ro', 'description' => 'Community RO', 'hidden' => $hidden4TapPort || $hidden4Tap],
            ['form_type' => 'text', 'name' => 'community_rw', 'description' => 'Community RW', 'hidden' => $hidden4TapPort || $hidden4Tap],
            ['form_type' => 'text', 'name' => 'address1', 'description' => $addressDesc1],
            ['form_type' => 'text', 'name' => 'address2', 'description' => $addressDesc2, 'hidden' => $hidden4TapPort],
            ['form_type' => 'text', 'name' => 'controlling_link', 'description' => 'Controlling Link', 'hidden' => $hidden4TapPort || $hidden4Tap],
            ['form_type' => 'checkbox', 'name' => 'enable_agc', 'description' => 'Enable AGC', 'help' => trans('helper.enable_agc'), 'hidden' => ! $cluster],
            ['form_type' => 'text', 'name' => 'agc_offset', 'description' => 'AGC offset', 'help' => trans('helper.agc_offset'), 'checkbox' => 'show_on_enable_agc', 'hidden' => ! $cluster],
            ['form_type' => 'textarea', 'name' => 'descr', 'description' => 'Description'],
        ];

        $b = [];
        if (\Module::collections()->has('PropertyManagement') && $type == 9) {
            $b = [
                ['form_type' => 'select', 'name' => 'apartment_id', 'description' => 'Apartment', 'value' => $netelement->getApartmentsList()],
            ];
        }

        $c = [];
        if (\Module::collections()->has('HfcSnmp') && $type == 2) {
            $c = [
                ['form_type' => 'text', 'name' => 'rkm_line_number', 'description' => 'RKM line number'],
            ];
        }

        return array_merge($a, $b, $c);
    }

    public function prepare_input($data)
    {
        $data = parent::prepare_input($data);

        // set default offset if none was given
        if (empty($data['agc_offset'])) {
            $data['agc_offset'] = 0.0;
        }

        // set agc_offset to NULL if AGC is disabled
        if (empty($data['enable_agc'])) {
            $data['agc_offset'] = null;
        }

        // enable_agc is just a helper to decide if agc_offset should be NULL, thus we can unset it now
        unset($data['enable_agc']);

        return $data;
    }

    /**
     * Show tabs in Netelement edit page.
     *
     * @author Roy Schneider
     * @param Modules\HfcReq\Entities\NetElement
     * @return array
     */
    protected function editTabs($netelement)
    {
        $defaultTabs = parent::editTabs($netelement);

        $tabs = ProvMonController::checkNetelementtype($netelement);
        $tabs[] = $defaultTabs[1];

        return $tabs;
    }

    /**
     * Overwrites the base method to handle file uploads
     */
    public function store($redirect = true)
    {
        // check and handle uploaded KML files
        $this->handle_file_upload('kml_file', storage_path(static::get_model_obj()->kml_path));

        return parent::store();

        // $ret = parent::store();
        // NetElement::relation_index_build_all();
        // return $ret;
    }

    /**
     * Overwrites the base method to handle file uploads
     */
    public function update($id)
    {
        // check and handle uploaded KML files
        $this->handle_file_upload('kml_file', storage_path(static::get_model_obj()->kml_path));

        return parent::update($id);
    }
}
