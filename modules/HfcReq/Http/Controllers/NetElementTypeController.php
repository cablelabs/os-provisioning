<?php

namespace Modules\HfcReq\Http\Controllers;

use Modules\HfcSnmp\Entities\OID;
use Modules\HfcSnmp\Entities\Parameter;
use Modules\HfcReq\Entities\NetElementType;

class NetElementTypeController extends HfcReqController
{
    protected $index_tree_view = true;

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        $hidden = in_array($model->name, ['Net', 'Cluster']);
        $parents = $model->html_list(NetElementType::whereNotIn('name', ['Net', 'Cluster'])->get(['id', 'name']), 'name', true);

        // label(name) has to be the same like column in sql table
        $a = [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => $hidden ? ['readonly'] : []],
            ['form_type' => 'text', 'name' => 'vendor', 'description' => 'Vendor', 'hidden' => $hidden ? '1' : '0'],
            ['form_type' => 'text', 'name' => 'version', 'description' => 'Version', 'hidden' => $hidden ? '1' : '0'],
            ['form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Device Type', 'value' => $parents, 'hidden' => $hidden ? '1' : '0', 'space' => 1],
            // possibly load only OIDs from Mibs that are related to this Device/NetElement-Type
            ['form_type' => 'select', 'name' => 'pre_conf_oid_id', 'description' => 'OID for PreConfiguration Setting', 'value' => OID::oid_list(true)],
            ['form_type' => 'text', 'name' => 'pre_conf_value', 'description' => 'PreConfiguration Value'],
            ['form_type' => 'text', 'name' => 'pre_conf_time_offset', 'description' => 'PreConfiguration Time Offset', 'space' => 1, 'help' => trans('helper.netelementtype_time_offset')],
            ['form_type' => 'text', 'name' => 'page_reload_time', 'description' => 'Reload Time - Controlling View', 'help' => trans('helper.netelementtype_reload')],
            ['form_type' => 'text', 'name' => 'icon_name', 'description' => 'Icon'],
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
        ];

        if ($hidden) {
            $a[0]['help'] = trans('helper.undeleteables');
        }

        return $a;
    }

    /**
     * This Function gives the Opportunity to quickly set html_frame or html_id of multiple Parameters
     * to order the Netelement Controlling View
     * Note: Input comes from NetElementType.settings.blade.php
     *
     * @param 	id  	Integer 	NetElementType ID
     * @return 	Edit View of NetElementType
     */
    public function settings($id)
    {
        if (! \Request::has('param_id')) {
            return \Redirect::back();
        }

        $html_frame = \Request::input('html_frame');
        $html_id = \Request::input('html_id');

        if (! $html_frame && ! $html_id) {
            return \Redirect::back();
        }

        $params = Parameter::find(\Request::input('param_id'));

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

        return \Redirect::route('NetElementType.edit', $id);
    }
}
