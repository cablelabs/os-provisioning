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

use Request;
use Redirect;
use Nwidart\Modules\Facades\Module;
use Modules\HfcReq\Entities\NetElementType;

class NetElementTypeController extends HfcReqController
{
    protected $index_tree_view = true;
    protected $edit_left_md_size = 6;
    protected $edit_right_md_size = 6;

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        $hidden4Net = in_array($model->id, [1, 2]) ? 1 : 0;     // Net, Cluster
        $hidden4Tap = in_array($model->id, [8, 9]) ? 1 : 0;     // Tap, Tap-Port

        // label(name) has to be the same like column in sql table
        $a = [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => $hidden4Net ? ['readonly'] : []],
            ['form_type' => 'text', 'name' => 'vendor', 'description' => 'Vendor', 'hidden' => $hidden4Net],
            ['form_type' => 'text', 'name' => 'version', 'description' => 'Version', 'hidden' => $hidden4Net],
            ['form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Device Type', 'value' => $this->setupSelect2Field($model, 'Parent'), 'hidden' => $hidden4Net || $hidden4Tap, 'space' => 1, 'options' => ['class' => 'select2-ajax', 'data-allow-clear' => 'true', 'ajax-route' => route('NetElementType.select2', ['model' => $model, 'relation' => 'parent'])]],
        ];

        if (Module::collections()->has('HfcSnmp')) {
            // possibly load only OIDs from Mibs that are related to this Device/NetElement-Type
            $a[] = ['form_type' => 'select', 'name' => 'pre_conf_oid_id', 'description' => 'OID for PreConfiguration Setting', 'hidden' => $hidden4Net || $hidden4Tap, 'value' => $this->setupSelect2Field($model, 'Oid', 'pre_conf_oid'), 'options' => ['class' => 'select2-ajax', 'data-allow-clear' => 'true', 'ajax-route' => route('NetElementType.select2', ['relation' => 'oids'])]];
        }

        $b = [
            ['form_type' => 'text', 'name' => 'pre_conf_value', 'description' => 'PreConfiguration Value', 'hidden' => $hidden4Net || $hidden4Tap],
            ['form_type' => 'text', 'name' => 'pre_conf_time_offset', 'description' => 'PreConfiguration Time Offset', 'hidden' => $hidden4Net || $hidden4Tap, 'space' => 1, 'help' => trans('helper.netelementtype_time_offset')],
            ['form_type' => 'text', 'name' => 'page_reload_time', 'description' => 'Reload Time - Controlling View', 'hidden' => $hidden4Tap, 'help' => trans('helper.netelementtype_reload')],
            ['form_type' => 'text', 'name' => 'icon_name', 'description' => 'Icon'],
        ];

        if (Module::collections()->has('CoreMon')) {
            $b[] = ['form_type' => 'text', 'name' => 'sidebar_pos', 'description' => 'Sidebar position', 'help' => trans('helper.sidebarPos')];
        }

        $b[array_key_last($b)]['space'] = 1;
        $b[] = ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'];

        if ($hidden4Net) {
            $a[0]['help'] = trans('helper.undeleteables');
        }

        return array_merge($a, $b);
    }

    /**
     * This Function gives the Opportunity to quickly set html_frame or html_id of multiple Parameters
     * to order the Netelement Controlling View
     * Note: Input comes from NetElementType.settings.blade.php
     *
     * @param 	id  	Integer 	NetElementType ID
     * @return Edit View of NetElementType
     */
    public function settings($id)
    {
        if (! Request::filled('param_id') || ! Module::collections()->has('HfcSnmp')) {
            return Redirect::back();
        }

        $html_frame = Request::input('html_frame');
        $html_id = Request::input('html_id');

        if (! $html_frame && ! $html_id) {
            return Redirect::back();
        }

        $params = \Modules\HfcSnmp\Entities\Parameter::find(Request::input('param_id'));

        // TODO: If this gets slow we could easily optimize it by doing direct sql updates
        foreach ($params as $param) {
            if ($html_frame) {
                $param->html_frame = $html_frame;
            }

            if ($html_id) {
                $param->html_id = $html_id;
            }

            $param->save();
        }

        return Redirect::route('NetElementType.edit', $id);
    }
}
