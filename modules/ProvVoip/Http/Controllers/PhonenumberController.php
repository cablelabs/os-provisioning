<?php

namespace Modules\ProvVoip\Http\Controllers;

use Bouncer;
use Modules\ProvVoip\Entities\Mta;
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

        if (\Module::collections()->has('ProvVoipEnvia')) {
            $login_placeholder = 'Autofilled if empty.';
        } else {
            $login_placeholder = '';
        }

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

            $active_checkbox = ['form_type' => 'checkbox', 'name' => 'active', 'description' => 'Active', 'html' => '<div class="col-md-12" style="background-color:white">
					<div class="form-group row"><label for="active" style="margin-top: 10px;" class="col-md-4 control-label">Active</label>
						<div class="col-md-7">
							<input name="active" type="hidden" id="active" value="'.$active_state.'">
							'.$active_symbol.'
						</div>
						<div title="Automatically set by (de)activation date in phonenumber management" name=active-help class=col-md-1>'.\HTML::image(asset('images/help.png'), '?', ['width' => 20]).'</div>
						<div class=col-md-4>
						</div>
						<div class=col-md-8>
						</div>
					</div>
				</div>',
                ];
        }

        $reassign_help = 'Can be used to assign the phonenumber (and related data) to another MTA.';
        if (\Module::collections()->has('ProvVoipEnvia')) {
            $reassign_help .= 'MTA has to belong to the same contract and modem installation addresses have to be equal.';
        }

        // get a list of MTAs the modem can be moved to
        if ($model->exists) {
            $mta_list = $model->mtas_list_phonenumber_can_be_reassigned_to();
        } else {
            $mta_list = [];
        }

        // label has to be the same like column in sql table
        $ret = [
            [
                'form_type' => 'text',
                'name' => 'country_code',
                'description' => 'International prefix',
                'help' => 'Usually, 4 digit number required for international calls.',
                'autocomplete' => [],
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
            ],
            [
                'form_type' => 'select',
                'name' => 'mta_id',
                'description' => 'MTA',
                'value' => $mta_list,
                'hidden' => 'C',
                'help' => $reassign_help,
            ],
            [
                'form_type' => 'text',
                'name' => 'port',
                'description' => 'Port',
            ],
        ];

        // create the form field for SIP username – envia TEL causes special handling
        $options = [];
        if (\Module::collections()->has('ProvVoipEnvia')) {
            if ($model->contract_external_id) {
                $options = ['readonly'];
            } else {
                $options = ['placeholder' => 'Leave empty on phonenumbers to be created.'];
            }
        }
        $username = [
            'form_type' => 'text',
            'name' => 'username',
            'description' => 'Username',
        ];
        if ($options) {
            $username['options'] = $options;
        }

        // create the form field for SIP password – envia TEL causes special handling
        $options = [];
        if (\Module::collections()->has('ProvVoipEnvia')) {
            $options = ['placeholder' => 'Autofilled if empty.'];
        }
        $password = [
            'form_type' => 'text',
            'name' => 'password',
            'description' => 'Password',
        ];
        if ($options) {
            $password['options'] = $options;
        }

        // create the form field for SIP domain – envia TEL causes special handling
        $options = [];
        if (\Module::collections()->has('ProvVoipEnvia')) {
            if ($model->contract_external_id) {
                $options = ['readonly'];
            } else {
                $options = ['placeholder' => 'Leave empty on phonenumbers to be created.'];
            }
        }
        $sipdomain = [
            'form_type' => 'text',
            'name' => 'sipdomain',
            'description' => trans('messages.SIP domain'),
        ];
        if ($options) {
            $sipdomain['options'] = $options;
        }

        array_push(
            $ret,
            $username,
            $password,
            $sipdomain,
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
        $rules['port'] .= '|unique:phonenumber,port,'.$id.',id,deleted_at,NULL,mta_id,'.$data['mta_id'];

        // a phonenumber can only exist once for the same country_code/prefix_number combination
        $rules['number'] .= '|unique:phonenumber,number,'.$id.',id,deleted_at,NULL,country_code,'.$data['country_code'].',prefix_number,'.$data['prefix_number'];

        return parent::prepare_rules($rules, $data);
    }

    /**
     * Get all management jobs for envia TEL
     *
     * @author Patrick Reichel
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
