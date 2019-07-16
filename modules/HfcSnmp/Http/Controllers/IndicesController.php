<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcReq\Entities\NetElement;
use Modules\HfcReq\Entities\NetElementType;

class IndicesController extends \BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        $netelement_id = $model->netelement_id ?: \Request::input('netelement_id');

        $netelem = NetElement::find($netelement_id);

        // get params from parent cmts for cluster
        if ($netelem->netelementtype_id == 2) {
            $netelem = $netelem->get_parent_cmts();
        }

        $params = $netelem ? NetElementType::param_list($netelem->netelementtype_id) : [];

        // label has to be the same like column in sql table
        $a = [
            ['form_type' => 'text', 'name' => 'netelement_id', 'description' => 'NetElement', 'hidden' => 1],
            ['form_type' => 'select', 'name' => 'parameter_id', 'description' => 'Parameter', 'value' => $params],
            ['form_type' => 'text', 'name' => 'indices', 'description' => 'Indices', 'options' => ['placeholder' => '2,3,8...']],
            ];

        return $a;
    }
}
