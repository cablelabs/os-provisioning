<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Modules\HfcReq\Http\Controllers;

use View;
use Illuminate\Http\Request;
use Modules\HfcReq\Entities\NetElement;
use App\Http\Controllers\BaseController;
use Modules\HfcReq\Entities\NetElementType;
use App\Http\Controllers\BaseViewController;

class NetElementController extends BaseController
{
    public function index()
    {
        $model = static::get_model_obj();
        $headline = BaseViewController::translate_view($model->view_headline(), 'Header', 2);
        $view_header = BaseViewController::translate_view('Overview', 'Header');
        $create_allowed = $this->index_create_allowed;
        $delete_allowed = $this->index_delete_allowed;
        $methodExists = method_exists($model, 'view_index_label');
        $indexTableInfo = $methodExists ? $model->view_index_label() : [];
        $hugeTable = $model->hasHugeIndexTable();

        return View::make('Generic.index', $this->compact_prep_view(compact('headline', 'hugeTable', 'view_header', 'model', 'create_allowed', 'delete_allowed', 'methodExists', 'indexTableInfo')));
    }

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($netelement = null)
    {
        $netelement = $netelement ?: new NetElement;
        $kml_files = $netelement->kml_files();

        // parse which netelementtype we want to edit/create
        // NOTE: this is for auto reload via HTML GET
        $type = NetElementType::find(request('netelementtype_id'))->base_type_id ??
            ($netelement->exists ? $netelement->netelementtype->base_type_id : null);

        $hidden4TapPort = $hidden4Tap = 0;
        $addressDesc1 = 'Address Line 1';
        $addressDesc2 = 'Address Line 2';
        $prov_device_hidden = in_array($type, [3, 4, 5]) ? 0 : 1;

        if ($type == 8) {
            $addressDesc1 = 'RKS '.trans('messages.Address'); // Used as address to control the attenuation setting via Sat-Kabel-RKS-Server
            $addressDesc2 = trans('messages.Address');
            $hidden4Tap = 1;
        }

        if ($type == 9) {
            $hidden4TapPort = 1;
            $addressDesc1 = 'RKS Port'; // Used as address to control the attenuation setting via Sat-Kabel-RKS-Server
        }

        // netelement is a cluster or will be created as type cluster
        $cluster = ($netelement->netelementtype_id == 2 || (! $netelement->exists && request('netelementtype_id') == 2));

        // this is just a helper and won't be stored in the database
        $netelement->enable_agc = $netelement->exists && $netelement->agc_offset !== null;

        /*
         * cluster: rf card settings
         * Options array is hidden when not used
         */
        $options_array = ['form_type' => 'text', 'name' => 'options', 'description' => 'Options'];
        if ($netelement->netelementtype && $type == 2) {
            $options_array = ['form_type' => 'select', 'name' => 'options', 'description' => 'RF Card Setting (DSxUS)', 'value' => $netelement->get_options_array($type)];
        }

        /*
         * return
         */
        $a = [
            ['form_type' => 'select', 'name' => 'netelementtype_id', 'description' => 'NetElement Type', 'value' => $this->setupSelect2Field($netelement, 'NetElementType'), 'hidden' => 0, 'options' => ['class' => 'select2-ajax', 'ajax-route' => route('NetElement.select2', ['relation' => 'netelementtypes'])]],
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name'],
            // array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => ['NET' => 'NET', 'NETGW' => 'NETGW', 'DATA' => 'DATA', 'CLUSTER' => 'CLUSTER', 'NODE' => 'NODE', 'AMP' => 'AMP']),
            // net is automatically detected in Observer
            // array('form_type' => 'select', 'name' => 'net', 'description' => 'Net', 'value' => $nets),
            ['form_type' => 'ip', 'name' => 'ip', 'description' => 'IP address', 'hidden' => $hidden4TapPort || $hidden4Tap],
            ['form_type' => 'text', 'name' => 'link', 'description' => 'ERD Link', 'hidden' => $hidden4TapPort || $hidden4Tap],
            ['form_type' => 'select', 'name' => 'prov_device_id', 'description' => 'Provisioning Device', 'value' => $this->setupSelect2Field($netelement, 'provDevice', 'prov_device_id', 'provDevice'), 'hidden' => $prov_device_hidden, 'options' => ['class' => 'select2-ajax', 'data-allow-clear' => 'true', 'ajax-route' => route('NetElement.select2', ['base_type_id' => $type, 'relation' => 'provDevice'])]],
            ['form_type' => 'text', 'name' => 'pos', 'description' => 'Geoposition', 'hidden' => $hidden4TapPort],
            ['form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Object', 'value' => $this->setupSelect2Field($netelement, 'Parent'), 'options' => ['class' => 'select2-ajax', 'data-allow-clear' => 'true', 'ajax-route' => route('NetElement.select2', ['model' => $netelement, 'relation' => 'parent'])]],
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
            $b[] = ['form_type' => 'select', 'name' => 'apartment_id', 'description' => 'Apartment', 'hidden' => 0,
                'value' => $this->setupSelect2Field($model, 'Apartment'), 'help' => trans('propertymanagement::help.apartmentList'),
                'options' => ['class' => 'select2-ajax', 'data-allow-clear' => 'true',
                    'ajax-route' => route('Apartment.select2', ['relation' => 'apartments']), ],
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
        $data['name'] = str_replace(['"', '\\'], '', $data['name']);
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

    public function prepare_rules($rules, $data)
    {
        if ($data['netelementtype_id'] == 9) {
            $id = $data['id'] ?? null;
            $rules['address1'] = 'required|unique:netelement,address1,'.$id.',id,deleted_at,NULL,netelementtype_id,9|regex:/^([0-9A-F]{2}){4}(~\d)?$/';
        }

        return parent::prepare_rules($rules, $data);
    }

    /**
     * Show tabs in Netelement edit page.
     *
     * @author Roy Schneider
     *
     * @param Modules\HfcReq\Entities\NetElement
     * @return array
     */
    protected function editTabs($netelement)
    {
        $defaultTabs = parent::editTabs($netelement);

        $tabs = $netelement->tabs();
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

    public function favorite($netelement)
    {
        cache()->forget(auth()->user()->login_name.'-Nets');

        return auth()->user()->favNetelements()->attach([$netelement]);
    }

    public function unfavorite($netelement)
    {
        cache()->forget(auth()->user()->login_name.'-Nets');

        return auth()->user()->favNetelements()->detach([$netelement]);
    }

    public function searchForNetsAndClusters(Request $request)
    {
        if (! $request->get('query')) {
            return collect();
        }

        $netId = array_search('Net', NetElementType::$undeletables);
        $clusterId = array_search('Cluster', NetElementType::$undeletables);

        return NetElement::without('netelementtype')
            ->whereIn('netelementtype_id', [$netId, $clusterId])
            ->where('name', 'like', '%'.$request->get('query').'%')
            ->limit(25)
            ->orderBy('netelementtype_id', 'ASC')
            ->get(['name', 'id', 'netelementtype_id'])
            ->toJson();
    }

    public function searchClusters($netId)
    {
        return NetElement::without('netelementtype')
            ->where('netelementtype_id', array_search('Cluster', NetElementType::$undeletables))
            ->where('net', $netId)
            ->limit(25)
            ->get(['name', 'id', 'netelementtype_id'])
            ->toJson();
    }
}
