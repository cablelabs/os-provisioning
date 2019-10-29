<?php

namespace Modules\ProvBase\Http\Controllers;

class NasController extends \BaseController
{
    protected $index_create_allowed = false;

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        $fields = [
            ['form_type' => 'select', 'name' => 'shortname', 'description' => 'NetGw', 'hidden' => 'E', 'value' => $model->html_list($model->netgws(), 'hostname')],
            ['form_type' => 'text', 'name' => 'nasname', 'description' => 'IP/Hostname'],
            ['form_type' => 'text', 'name' => 'secret', 'description' => 'Secret'],
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
        ];

        // create NAS
        if (\Request::has('netgw_id')) {
            $netgw = \Modules\ProvBase\Entities\NetGw::findOrFail(\Request::get('netgw_id'));
            $fields[0]['init_value'] = $netgw->id;
            $fields[1]['init_value'] = $netgw->ip;
        }

        return  $fields;
    }
}
