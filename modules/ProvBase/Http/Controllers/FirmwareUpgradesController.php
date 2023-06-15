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

use App\Http\Controllers\BaseController;
use Illuminate\Validation\Rule;
use Modules\ProvBase\Entities\FirmwareUpgrades;

class FirmwareUpgradesController extends BaseController
{
    protected $many_to_many = [
        [
            'field' => 'fromconfigfile_ids',
        ],
    ];

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new FirmwareUpgrades;
        }

        $fromConfigfiles = $model->fromConfigfile()->pluck('configfile_id', 'configfile_id')->toArray();
        $toConfigfiles = $model->configfile()->pluck('id', 'id')->toArray();

        $form = [
            [
                'form_type' => 'date',
                'name' => 'start_date',
                'description' => 'Start Date',
                'options' => ['placeholder' => 'YYYY-MM-DD'],
                'help' => trans('helper.Item_validTo'),
            ],
            [
                'form_type' => 'time',
                'name' => 'start_time',
                'description' => 'Start Time',
                'options' => ['placeholder' => 'HH:MM'],
                'help' => trans('helper.Item_validTo'),
            ],
            [
                'form_type' => 'date',
                'name' => 'end_date',
                'description' => 'End Date',
                'options' => ['placeholder' => 'YYYY-MM-DD'],
                'help' => trans('helper.Item_validTo'),
            ],
            [
                'form_type' => 'time',
                'name' => 'end_time',
                'description' => 'End Time',
                'options' => ['placeholder' => 'HH:MM'],
                'help' => trans('helper.Item_validTo'),
            ],
            [
                'form_type' => 'text',
                'name' => 'cron_string',
                'description' => 'Cron String',
                'options' => ['placeholder' => '* * * * *'],
                'help' => trans('helper.cron_string'),
            ],
            [
                'form_type' => 'text',
                'name' => 'batch_size',
                'description' => 'Batch Size',
            ],
            [
                'form_type' => 'select',
                'name' => 'fromconfigfile_ids[]',
                'description' => 'From Configfile',
                'value' => $this->setupSelect2FieldForPivotTable($model, 'fromConfigfile', 'Configfile'),
                'options' => [
                    'class' => 'select2-ajax',
                    'multiple' => 'multiple',
                    'data-allow-clear' => 'true',
                    'ajax-route' => route('FirmwareUpgrades.select2', ['relation' => 'configfiles']),
                ],
                'selected' => $fromConfigfiles,
            ],
            [
                'form_type' => 'select',
                'name' => 'to_configfile_id',
                'description' => 'To Configfile',
                'value' => $this->setupSelect2Field($model, 'Configfile'),
                'options' => [
                    'class' => 'select2-ajax',
                    'data-allow-clear' => 'true',
                    'ajax-route' => route('FirmwareUpgrades.select2', ['relation' => 'configfiles']),
                ],
                'selected' => $toConfigfiles,
            ],
        ];

        return $form;
    }

    public function prepare_rules($rules, $data)
    {
        $rules['start_date'] = 'required|date_format:Y-m-d';
        $rules['start_time'] = 'required|date_format:H:i';
        $rules['batch_size'] = ['nullable', 'integer', 'min:1'];

        // Check if 'batch_size' is set in the input data
        if (isset($data['batch_size'])) {
            // If 'batch_size' is not empty, then 'cron_string' must be a valid cron expression
            $rules['cron_string'] = 'required|cron';
        } else {
            // If 'batch_size' is empty, then 'cron_string' can be empty or a valid cron expression
            $rules['cron_string'] = 'nullable|cron';
        }

        $rules['fromconfigfile_ids'] = 'required|array';
        $rules['to_configfile_id'] = ['required', 'integer', 'min:1', Rule::notIn($data['fromconfigfile_ids'] ?? [])];

        return parent::prepare_rules($rules, $data);
    }
}
