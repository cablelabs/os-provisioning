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

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\ProvBase;

class QosController extends \BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        // label has to be the same like column in sql table
        return [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name'],
            ['form_type' => 'text', 'name' => 'ds_rate_max', 'description' => 'DS Rate [MBit/s]'],
            ['form_type' => 'text', 'name' => 'us_rate_max', 'description' => 'US Rate [MBit/s]'],
            ['form_type' => 'text', 'name' => 'ds_name', 'description' => 'DS PPPoE Name'],
            ['form_type' => 'text', 'name' => 'us_name', 'description' => 'US PPPoE Name'],
        ];
    }

    public function prepare_input_post_validation($data)
    {
        $pb = ProvBase::first();
        $data['ds_rate_max_help'] = $data['ds_rate_max'] * 1000 * 1000 * $pb->ds_rate_coefficient;
        $data['us_rate_max_help'] = $data['us_rate_max'] * 1000 * 1000 * $pb->us_rate_coefficient;

        return parent::prepare_input_post_validation($data);
    }
}
