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

use Nwidart\Modules\Facades\Module;
use Modules\ProvBase\Entities\ProvBase;

class QosController extends \BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        // label has to be the same like column in sql table
        $ret = [];

        $ret[] = [
            'form_type' => 'text',
            'name' => 'name',
            'description' => 'Name',
        ];

        if ((! $model) || ('smartont' != $model->type)) {
            $ret[] = [
                'form_type' => 'text',
                'name' => 'ds_rate_max',
                'description' => 'DS Rate [MBit/s]',
            ];
            $ret[] = [
                'form_type' => 'text',
                'name' => 'us_rate_max',
                'description' => 'US Rate [MBit/s]',
            ];
            $ret[] = [
                'form_type' => 'text',
                'name' => 'ds_name',
                'description' => 'DS PPPoE Name',
            ];
            $ret[] = [
                'form_type' => 'text',
                'name' => 'us_name',
                'description' => 'US PPPoE Name',
            ];
        }

        if (Module::collections()->has('SmartOnt')) {
            $types = [
                'default' => 'Default',
                'smartont' => 'SmartOnt',
            ];
            $ret[] = [
                'form_type' => 'select',
                'name' => 'type',
                'value' => $types,
                'description' => 'Type',
            ];
            $ret[] = [
                'form_type' => 'text',
                'name' => 'vlan_id',
                'description' => 'VLAN ID',
            ];
            if ('GESA' == config('smartont.flavor.active')) {
                $ret[] = [
                    'form_type' => 'text',
                    'name' => 'ont_line_profile_id',
                    'description' => 'ONT line profile ID',
                ];
            }
            $ret[] = [
                'form_type' => 'text',
                'name' => 'gem_port',
                'description' => 'GEM port',
            ];
            if ('GESA' == config('smartont.flavor.active')) {
                $ret[] = [
                    'form_type' => 'text',
                    'name' => 'traffic_table_in',
                    'description' => 'Traffic table in',
                ];
                $ret[] = [
                    'form_type' => 'text',
                    'name' => 'traffic_table_out',
                    'description' => 'Traffic table out',
                ];
            }
        }

        return $ret;
    }

    /**
     * Set nullable fields.
     *
     * @author Patrick Reichel
     */
    public function prepare_input($data)
    {
        $data = parent::prepare_input($data);

        if (is_null($data['vlan_id'])) {
            $data['vlan_id'] = 0;
        }
        if ('smartont' == $data['type']) {
            $data['ds_rate_max'] = $data['ds_rate_max'] ?? 0;
            $data['us_rate_max'] = $data['us_rate_max'] ?? 0;
        }

        return $data;
    }

    public function prepare_input_post_validation($data)
    {
        $pb = ProvBase::first();
        $data['ds_rate_max_help'] = $data['ds_rate_max'] * 1000 * 1000 * $pb->ds_rate_coefficient;
        $data['us_rate_max_help'] = $data['us_rate_max'] * 1000 * 1000 * $pb->us_rate_coefficient;

        return parent::prepare_input_post_validation($data);
    }
}
