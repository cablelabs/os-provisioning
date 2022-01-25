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

namespace Modules\Dreamfiber\Http\Controllers;

use Modules\Dreamfiber\Entities\DfSubscriptionEvent;

class DfSubscriptionEventController extends \BaseController
{
    /**
     * if set to true a create button on index view is available - set to true in BaseController as standard
     */
    protected $index_create_allowed = false;

    /**
     * Defines the formular fields for the edit and create view.
     *
     * @author Patrick Reichel
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new DfSubscriptionEvent;
        }

        $fields = [
            [
                'form_type' => 'textarea',
                'name' => 'description',
                'description' => 'description',
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'status',
                'description' => 'status',
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'timestamp',
                'description' => 'timestamp',
                'options' => ['readonly'],
            ],
        ];

        return $fields;
    }

    /**
     * Modify the ruleset
     *
     * @author Patrick Reichel
     */
    public function prepare_rules($rules, $data)
    {
        return parent::prepare_rules($rules, $data);
    }
}
