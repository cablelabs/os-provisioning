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

namespace Modules\ProvVoip\Http\Controllers;

use Modules\ProvVoip\Entities\PhoneTariff;

class PhoneTariffController extends \BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new PhoneTariff;
        }

        // label has to be the same like column in sql table
        // TODO: Voip Protocol: only SIP is implemented and chhosing has no effect -> hidden
        return [
            ['form_type' => 'text', 'name' => 'external_identifier', 'description' => 'External Identifier'],
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name'],
            ['form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'options' => ['translate' => true], 'value' => PhoneTariff::getPossibleEnumValues('type')],
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
            ['form_type' => 'select', 'name' => 'voip_protocol', 'description' => 'VoIP protocol', 'value' => PhoneTariff::getPossibleEnumValues('voip_protocol', true), 'hidden' => 1],
            ['form_type' => 'checkbox', 'name' => 'usable', 'description' => 'Usable'],
        ];
    }
}
