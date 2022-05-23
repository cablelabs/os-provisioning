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

use Bouncer;
use Modules\ProvVoip\Entities\Phonenumber;

class PhonenumberController extends \BaseController
{
    /**
     * if set to true a create button on index view is available - set to true in BaseController as standard
     */
    protected $index_create_allowed = false;
    protected $save_button_name = 'Save / Restart';

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new Phonenumber;
        }

        $hasProvVoipEnvia = \Module::collections()->has('ProvVoipEnvia');
        $provVoip = \Modules\ProvVoip\Entities\ProvVoip::first();

        $roOption = $model->contract_external_id && $hasProvVoipEnvia ? ['readonly'] :
        ['placeholder' => 'Leave empty on phonenumbers to be created.'];
        $ajaxOption = [
            'class' => 'select2-ajax',
            'ajax-route' => route('Phonenumber.select2', ['relation' => 'mtas']),
        ];

        $help = 'Can be used to assign the phonenumber (and related data) to another MTA.';
        if ($hasProvVoipEnvia) {
            $help .= 'MTA has to belong to the same contract and modem installation addresses have to be equal.';
            $ajaxOption = [];
        }

        // label has to be the same like column in sql table
        $ret = [
            [
                'form_type' => 'select',
                'name' => 'mta_id',
                'description' => 'MTA',
                'value' => $hasProvVoipEnvia ?
                    $model->mtasWhenEnviaEnabled() :
                    $this->setupSelect2Field($model, 'Mta'),
                'hidden' => 'C',
                'help' => $help,
                'options' => $ajaxOption,
            ],
            [
                'form_type' => 'text',
                'name' => 'port',
                'description' => 'Port',
                'space' => 1,
            ],
            [
                'form_type' => 'text',
                'name' => 'country_code',
                'description' => 'International prefix',
                'help' => 'Usually, 4 digit number required for international calls.',
                'autocomplete' => [],
                'init_value' => $provVoip->default_country_code,
            ],
            [
                'form_type' => 'text',
                'name' => 'prefix_number',
                'description' => 'Prefix Number',
                'help' => 'Has to be available on modem address.',
            ],
            [
                'form_type' => 'text',
                'name' => 'number',
                'description' => 'Number',
                'help' => 'The phonenumber to port or a free number given by your provider.',
                'space' => 1,
            ],
            [
                'form_type' => 'text',
                'name' => 'username',
                'description' => 'Username',
                'options' => $roOption,
            ],
            [
                'form_type' => 'text',
                'name' => 'password',
                'description' => 'Password',
                'space' => 1,
                'options' => $hasProvVoipEnvia ? ['placeholder' => 'Autofilled if empty.'] : [],
            ],
            [
                'form_type' => 'text',
                'name' => 'sipdomain',
                'description' =>'SIP domain',
                'autocomplete' => [],
                'init_value' => $hasProvVoipEnvia ? '' : ($model->exists ? $model->sipdomain : $provVoip->default_sip_registrar),
                'options' =>  $roOption,
            ],
        ];

        // if there is no phonenumbermanagement: make checkbox changeable
        // TODO: should this be the case or do we want to need a management in each case?
        if (is_null($model->phonenumbermanagement)) {
            $active_checkbox = [
                'form_type' => 'checkbox',
                'name' => 'active',
                'description' => 'Active',
                'help' => 'If you create a PhonenumberManagement this checkbox will be set depending on its (de)activation date.',
            ];
        }
        // otherwise: store value in hidden form field and show symbol to indicate the current state instead
        else {
            // TODO: move style to css file or use existing styles
            $active_symbol_style = 'font-size: 1.4em; padding-top:0.4em; padding-left: 4.8em';

            // prepare the data to be stored and the symbol to be shown
            if ($model->active) {
                $active_state = '1';
                $active_symbol = '<div style="color: #080; '.$active_symbol_style.'">✔</div>';
            } else {
                $active_state = '0';
                $active_symbol = '<div style="color: #f00; '.$active_symbol_style.'">✘</div>';
            }

            $active_checkbox = ['form_type' => 'html', 'name' => 'active', 'description' => 'Active',
                'html' => '<div class="col-md-7">
                        <input name="active" type="hidden" id="active" value="'.$active_state.'">'.$active_symbol.'
                    </div>',
                'help' => 'Automatically set by (de)activation date in phonenumber management',
            ];
        }

        array_push(
            $ret,
            $active_checkbox
        );

        return $ret;
    }

    /**
     * Adds the check for unique ports per MTA.
     *
     * @author Patrick Reichel
     */
    public function prepare_rules($rules, $data)
    {
        // check if there is an phonenumber id (= updating), else set to -1 (a not used database id)
        $id = $rules['id'];
        if (! $id) {
            $id = -1;
        }

        // remove id from rules
        unset($rules['id']);

        // verify that the chosen port is unique for this mta
        $rules['port'][] = 'unique:phonenumber,port,'.$id.',id,deleted_at,NULL,mta_id,'.$data['mta_id'];

        // a phonenumber can only exist once for the same country_code/prefix_number combination
        $rules['number'][] = 'unique:phonenumber,number,'.$id.',id,deleted_at,NULL,country_code,'.$data['country_code'].',prefix_number,'.$data['prefix_number'];

        return parent::prepare_rules($rules, $data);
    }

    /**
     * Get all management jobs for envia TEL
     *
     * @author Patrick Reichel
     *
     * @param $phonenumber current phonenumber object
     * @return array containing linktexts and URLs to perform actions against REST API
     */
    public static function _get_envia_management_jobs($phonenumber)
    {
        if (Bouncer::cannot('view', 'Modules\ProvVoipEnvia\Entities\ProvVoipEnvia')) {
            return;
        }

        $provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

        return $provvoipenvia->get_jobs_for_view($phonenumber, 'phonenumber');
    }
}
