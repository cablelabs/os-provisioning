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

use Illuminate\Http\Request;
use Nwidart\Modules\Facades\Module;
use Modules\HfcReq\Entities\NetElement;
use App\Http\Controllers\BaseController;
use Modules\HfcReq\Entities\NetElementType;

class NetElementController extends BaseController
{
    /**
     * Accessor for File Upload Paths
     *
     * @return array
     */
    protected function getFileUploadPaths(): array
    {
        return [
            'infrastructure_file' => NetElement::GPS_FILE_PATH,
        ];
    }

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($netelement = null)
    {
        $netelement = $netelement ?: new NetElement;

        // parse which netelementtype we want to edit/create
        // NOTE: this is for auto reload via HTML GET
        $type = NetElementType::find(request('netelementtype_id'))->base_type_id ??
            ($netelement->exists ? $netelement->base_type_id : null);

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
        $cluster = ($netelement->base_type_id == 2 || (! $netelement->exists && request('netelementtype_id') == 2));

        // this is just a helper and won't be stored in the database
        $netelement->enable_agc = $netelement->exists && $netelement->agc_offset !== null;

        /*
         * cluster: rf card settings
         * Options array is hidden when not used
         */
        $options_array = ['form_type' => 'text', 'name' => 'options', 'description' => 'Options'];
        if ($netelement->netelementtype_id && $type == 2) {
            $options_array = ['form_type' => 'select', 'name' => 'options', 'description' => 'RF Card Setting (DSxUS)', 'value' => $netelement->get_options_array($type)];
        }

        $a = [
            ['form_type' => 'select', 'name' => 'netelementtype_id', 'description' => 'NetElement Type', 'select' => NetElementType::$undeletables, 'value' => $this->setupSelect2Field($netelement, 'NetElementType'), 'hidden' => 0, 'options' => ['class' => 'select2-ajax', 'ajax-route' => route('NetElement.select2', ['relation' => 'netelementtypes'])]],
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'space' => 1],
            // array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => ['NET' => 'NET', 'NETGW' => 'NETGW', 'DATA' => 'DATA', 'CLUSTER' => 'CLUSTER', 'NODE' => 'NODE', 'AMP' => 'AMP']),
            // net is automatically detected in Observer
            // array('form_type' => 'select', 'name' => 'net', 'description' => 'Net', 'value' => $nets),
            ['form_type' => 'ip', 'name' => 'ip', 'description' => 'IP address', 'hidden' => $hidden4TapPort || $hidden4Tap],
            ['form_type' => 'select', 'name' => 'prov_device_id', 'description' => 'Provisioning Device', 'value' => $this->setupSelect2Field($netelement, 'provDevice', 'prov_device_id', 'provDevice'), 'hidden' => $prov_device_hidden, 'options' => ['class' => 'select2-ajax', 'data-allow-clear' => 'true', 'ajax-route' => route('NetElement.select2', ['base_type_id' => $type, 'relation' => 'provDevice'])]],
            ['form_type' => 'text', 'name' => 'community_ro', 'description' => 'Community RO', 'hidden' => $hidden4TapPort || $hidden4Tap],
            ['form_type' => 'text', 'name' => 'community_rw', 'description' => 'Community RW', 'hidden' => $hidden4TapPort || $hidden4Tap],
            ['form_type' => 'text', 'name' => 'username', 'description' => 'Username', 'select' => 'RKM-Server', 'help' => trans('helper.netelement.credentials')],
            ['form_type' => 'text', 'name' => 'password', 'description' => 'Password', 'select' => 'RKM-Server', 'help' => trans('helper.netelement.credentials')],
            array_merge($options_array, ['hidden' => $hidden4TapPort || $hidden4Tap, 'space' => 1]),

            ['form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Object', 'value' => $this->setupSelect2Field($netelement, 'Parent'), 'options' => ['class' => 'select2-ajax', 'data-allow-clear' => 'true', 'ajax-route' => route('NetElement.select2', ['model' => $netelement, 'relation' => 'parent'])]],
            ['form_type' => 'text', 'name' => 'link', 'description' => 'ERD Link', 'hidden' => $hidden4TapPort || $hidden4Tap],
            ['form_type' => 'text', 'name' => 'controlling_link', 'description' => 'Controlling Link', 'hidden' => $hidden4TapPort || $hidden4Tap],
            // array('form_type' => 'select', 'name' => 'state', 'description' => 'State', 'value' => ['OK' => 'OK', 'YELLOW' => 'YELLOW', 'RED' => 'RED'], 'options' => ['readonly']),
        ];

        if (Module::collections()->has('HfcBase')) {
            $a[] = ['form_type' => 'select', 'name' => 'infrastructure_file', 'description' => 'Choose Infrastructure file', 'value' => $netelement->infrastructureGpsFiles()];
            $a[] = ['form_type' => 'file', 'name' => 'infrastructure_file_upload', 'description' => 'or: Upload Infrastructure file', 'help' => trans('helper.gpsUpload'), 'space' => 1];
        }

        $b = [
            ['form_type' => 'text', 'name' => 'lng', 'description' => 'Longitude', 'hidden' => $hidden4TapPort],
            ['form_type' => 'text', 'name' => 'lat', 'description' => 'Latitude', 'hidden' => $hidden4TapPort],
            ['form_type' => 'text', 'name' => 'address1', 'description' => $addressDesc1],
            ['form_type' => 'text', 'name' => 'address2', 'description' => $addressDesc2, 'hidden' => $hidden4TapPort],
            ['form_type' => 'checkbox', 'name' => 'enable_agc', 'description' => 'Enable AGC', 'help' => trans('helper.enable_agc'), 'hidden' => ! $cluster],
            ['form_type' => 'text', 'name' => 'agc_offset', 'description' => 'AGC offset', 'help' => trans('helper.agc_offset'), 'checkbox' => 'show_on_enable_agc', 'hidden' => ! $cluster, 'space' => 1],
            ['form_type' => 'textarea', 'name' => 'descr', 'description' => 'Description'],
        ];

        if (Module::collections()->has('PropertyManagement') && $type == 9) {
            $b[] = ['form_type' => 'select', 'name' => 'apartment_id', 'description' => 'Apartment', 'hidden' => 0,
                'value' => $this->setupSelect2Field($netelement, 'Apartment'), 'help' => trans('propertymanagement::help.apartmentList'),
                'options' => ['class' => 'select2-ajax', 'data-allow-clear' => 'true',
                    'ajax-route' => route('Apartment.select2', ['relation' => 'apartments']), ],
            ];
        }

        if (Module::collections()->has('HfcSnmp') && $type == 2) {
            $b[] = ['form_type' => 'text', 'name' => 'rkm_line_number', 'description' => 'RKM line number'];
        }

        return array_merge($a, $b);
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
        $tabs = $netelement->tabs();

        if (isset($tabs[0]['route']) && $tabs[0]['route'] == 'NetElement.edit') {
            unset($tabs[0]['route']);
        }

        if (! Module::collections()->has('CoreMon')) {
            $tabs[] = parent::editTabs($netelement)[1];
        }

        return $tabs;
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
            ->whereIn('base_type_id', [$netId, $clusterId])
            ->where('name', 'like', '%'.$request->get('query').'%')
            ->limit(25)
            ->orderBy('base_type_id', 'ASC')
            ->get(['name', 'id', 'base_type_id'])
            ->toJson();
    }

    public function searchClusters($netId)
    {
        return NetElement::without('netelementtype')
            ->where('base_type_id', array_search('Cluster', NetElementType::$undeletables))
            ->where('net', $netId)
            ->limit(25)
            ->get(['name', 'id', 'base_type_id'])
            ->toJson();
    }
}
