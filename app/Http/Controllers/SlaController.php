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

namespace App\Http\Controllers;

class SlaController extends BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        // label has to be the same like column in sql table
        return [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => ['placeholder' => 'xs | '.implode(' | ', \App\Sla::$names)]],
            ['form_type' => 'text', 'name' => 'license', 'description' => 'License'],
            // ['form_type' => 'text', 'name' => 'num_contracts', 'description' => ''],
            // ['form_type' => 'text', 'name' => 'num_modems', 'description' => ''],
            // ['form_type' => 'text', 'name' => 'num_netgw', 'description' => ''],
            // ['form_type' => 'text', 'name' => 'system_status', 'description' => ''],
        ];
    }

    /**
     * Set Session key that is used later when support request is actually made
     */
    public function clicked_sla()
    {
        \Session::push('clicked_sla', true);
        \Log::debug('Get SLA clicked');
    }
}
