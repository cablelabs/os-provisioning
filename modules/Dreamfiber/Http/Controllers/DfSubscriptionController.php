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

use Modules\ProvBase\Entities\Contract;
use Modules\Dreamfiber\Entities\Dreamfiber;
use Modules\Dreamfiber\Entities\DfSubscription;

class DfSubscriptionController extends \BaseController
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
        $dreamfiber = Dreamfiber::first();
        if (! $model) {
            $model = new DfSubscription();
        }
        $contract_id = intval(\Request::get('contract_id'));
        $contract = Contract::find($contract_id);

        $fields = [
            [
                'form_type' => 'text',
                'name' => 'service_name',
                'description' => 'service_name',
                'init_value' => $model->exists ? '' : $dreamfiber->default_service_name,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'service_type',
                'description' => 'service_type',
                'init_value' => $model->exists ? '' : $dreamfiber->default_service_type,
                'space' => 1,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'contact_no',
                'description' => 'contact_no',
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'contact_first_name',
                'description' => 'contact_first_name',
                'init_value' => $model->exists ? '' : $contract->firstname,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'contact_last_name',
                'description' => 'contact_last_name',
                'init_value' => $model->exists ? '' : $contract->lastname,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'contact_company_name',
                'description' => 'contact_company_name',
                'init_value' => $model->exists ? '' : $contract->company,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'contact_street',
                'description' => 'contact_street',
                'init_value' => $model->exists ? '' : $contract->street,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'contact_street_no',
                'description' => 'contact_street_no',
                'init_value' => $model->exists ? '' : $contract->house_number,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'contact_postal_code',
                'description' => 'contact_postal_code',
                'init_value' => $model->exists ? '' : $contract->zip,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'contact_city',
                'description' => 'contact_city',
                'init_value' => $model->exists ? '' : $contract->city,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'contact_country',
                'description' => 'contact_country',
                'init_value' => $model->exists ? '' : $contract->country_code,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'contact_phone',
                'description' => 'contact_phone',
                'init_value' => $model->exists ? '' : $contract->phone,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'contact_email',
                'description' => 'contact_email',
                'init_value' => $model->exists ? '' : $contract->email,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'textarea',
                'name' => 'contact_notes',
                'description' => 'contact_notes',
                'space' => 1,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'subscription_id',
                'description' => 'subscription_id',
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'subscription_end_point_id',
                'description' => 'subscription_end_point_id',
                'init_value' => $model->exists ? '' : $contract->sep_id,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'sf_sla',
                'description' => 'sf_sla',
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
                'name' => 'wishdate',
                'description' => 'wishdate',
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'switchdate',
                'description' => 'switchdate',
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'modificationdate',
                'description' => 'modificationdate',
                'space' => 1,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'l1_handover_equipment_name',
                'description' => 'l1_handover_equipment_name',
                'init_value' => $model->exists ? '' : $contract->omdf_id,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'l1_handover_equipment_rack',
                'description' => 'l1_handover_equipment_rack',
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'l1_handover_equipment_slot',
                'description' => 'l1_handover_equipment_slot',
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'l1_handover_equipment_port',
                'description' => 'l1_handover_equipment_port',
                'space' => 1,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'l1_breakout_cable',
                'description' => 'l1_breakout_cable',
                'init_value' => $model->exists ? '' : $contract->boc_label,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'l1_breakout_fiber',
                'description' => 'l1_breakout_fiber',
                'init_value' => $model->exists ? '' : $contract->bof_label,
                'space' => 1,
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'text',
                'name' => 'alau_order_ref',
                'description' => 'alau_order_ref',
                'options' => ['readonly'],
            ],
            [
                'form_type' => 'textarea',
                'name' => 'note',
                'description' => 'note',
                'options' => ['readonly'],
            ],
        ];

        return $fields;
    }

    /**
     * Modifies the ruleset.
     *
     * @author Patrick Reichel
     */
    public function prepare_rules($rules, $data)
    {
        return parent::prepare_rules($rules, $data);
    }
}
