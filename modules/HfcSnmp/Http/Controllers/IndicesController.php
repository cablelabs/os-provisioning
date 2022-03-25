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

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcReq\Entities\NetElement;
use App\Http\Controllers\BaseController;

class IndicesController extends BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        $netelement = NetElement::findOrFail($model->netelement_id ?: request('netelement_id'));

        // get params from parent netgw for cluster
        if ($netelement->netelementtype_id == 2) {
            $netelement = $netelement->getParentNetelementOfType(3);
        }

        // label has to be the same like column in sql table
        return [
            [
                'form_type' => 'text',
                'name' => 'netelement_id',
                'description' => 'NetElement',
                'hidden' => 1,
            ],
            [
                'form_type' => 'select',
                'name' => 'parameter_id',
                'description' => 'Parameter',
                'value' => $netelement ? $netelement->netelementtype->parameterList() : [],
            ],
            [
                'form_type' => 'text',
                'name' => 'indices',
                'description' => 'Indices',
                'options' => [
                    'placeholder' => '2,3,8...',
                ],
            ],
        ];
    }
}
