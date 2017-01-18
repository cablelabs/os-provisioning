<?php

namespace Modules\ProvVoip\Http\Controllers;

use Modules\ProvVoip\Entities\Phonenumber;
use Modules\ProvVoip\Entities\Mta;

class PhonenumberController extends \BaseController {


	/**
	 * if set to true a create button on index view is available - set to true in BaseController as standard
	 */
    protected $index_create_allowed = false;
	protected $save_button = 'Save / Restart';

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		if (!$model)
			$model = new Phonenumber;

		if (\PPModule::is_active('provvoipenvia')) {
			$login_placeholder = 'Autofilled if empty.';
		}
		else {
			$login_placeholder = '';
		}

		// if there is no phonenumbermanagement: make checkbox changeable
		// TODO: should this be the case or do we want to need a management in each case?
		if (is_null($model->phonenumbermanagement)) {
			$active_checkbox = array('form_type' => 'checkbox', 'name' => 'active', 'description' => 'Active', 'help' => 'If you create a PhonenumberManagement this checkbox will be set depending on its (de)activation date.');
		}
		// otherwise: store value in hidden form field and show symbol to indicate the current state instead
		else {

			// TODO: move style to css file or use existing styles
			$active_symbol_style = "font-size: 1.4em; padding-top:0.4em; padding-left: 4.8em";

			// prepare the data to be stored and the symbol to be shown
			if ($model->active) {
				$active_state = '1';
				$active_symbol = '<div style="color: #080; '.$active_symbol_style.'">✔</div>';

			}
			else {
				$active_state = '0';
				$active_symbol = '<div style="color: #f00; '.$active_symbol_style.'">✘</div>';
			}

			$active_checkbox = array('form_type' => 'checkbox', 'name' => 'active', 'description' => 'Active', 'html' =>
				'<div class="col-md-12" style="background-color:white">
					<div class="form-group"><label for="active" style="margin-top: 10px;" class="col-md-4 control-label">Active</label>
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
				);
		}

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'country_code', 'description' => 'Country Code', 'value' => Phonenumber::getPossibleEnumValues('country_code')),
			array('form_type' => 'text', 'name' => 'prefix_number', 'description' => 'Prefix Number', 'help' => 'Has to be available on modem address.'),
			array('form_type' => 'text', 'name' => 'number', 'description' => 'Number', 'help' => 'The phonenumber to port or a free number given by your provider.'),
			array('form_type' => 'select', 'name' => 'mta_id', 'description' => 'MTA', 'value' => $model->mtas_list_only_contract_assigned(), 'hidden' => 'C', 'help' => 'Can be used to assign the phonenumber (and related data) to another MTA. Useful on modem changes and for testing. You will probably have to change the port, too.'),
			array('form_type' => 'text', 'name' => 'port', 'description' => 'Port'),
			array('form_type' => 'text', 'name' => 'username', 'description' => 'Username', 'options' => array('placeholder' => $login_placeholder)),
			array('form_type' => 'text', 'name' => 'password', 'description' => 'Password', 'options' => array('placeholder' => $login_placeholder)),
			array('form_type' => 'text', 'name' => 'sipdomain', 'description' => 'SIP domain'),
			$active_checkbox,
		);
	}


	/**
	 * Adds the check for unique ports per MTA.
	 *
	 * @author Patrick Reichel
	 */
	public function prepare_rules($rules, $data) {

		// check if there is an phonenumber id (= updating), else set to -1 (a not used database id)
		$id = $rules['id'];
		if (!$id) {
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
	 * Get all management jobs for Envia
	 *
	 * @author Patrick Reichel
	 * @param $phonenumber current phonenumber object
	 * @return array containing linktexts and URLs to perform actions against REST API
	 */
	public static function _get_envia_management_jobs($phonenumber) {

		$provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

		// check if user has the right to perform actions against Envia API
		\App\Http\Controllers\BaseAuthController::auth_check('view', 'Modules\ProvVoipEnvia\Entities\ProvVoipEnvia');

		return $provvoipenvia->get_jobs_for_view($phonenumber, 'phonenumber');
	}
}
