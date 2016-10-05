<?php

namespace Modules\ProvVoip\Http\Controllers;

use Modules\ProvVoip\Entities\PhonenumberManagement;
use Modules\ProvVoip\Entities\Phonenumber;
use Modules\ProvVoip\Entities\CarrierCode;
use Modules\ProvVoip\Entities\EkpCode;
use Modules\ProvVoipEnvia\Entities\TRCClass;

class PhonenumberManagementController extends \BaseController {


	/**
	 * if set to true a create button on index view is available - set to true in BaseController as standard
	 */
    protected $index_create_allowed = false;


    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// create
		if (!$model) {
			$model = new PhonenumberManagement;
		}

		// in most cases the subscriber is identical to contract partner ⇒ on create we prefill these values with data from contract
		if (!$model->exists) {
			$phonenumber = Phonenumber::findOrFail(\Input::get('phonenumber_id'));
			$contract = $phonenumber->mta->modem->contract;

			$init_values = array(
				'subscriber_company' => $contract->company,
				'subscriber_department' => $contract->department,
				'subscriber_salutation' => $contract->salutation,
				'subscriber_academic_degree' => $contract->academic_degree,
				'subscriber_firstname' => $contract->firstname,
				'subscriber_lastname' => $contract->lastname,
				'subscriber_street' => $contract->street,
				'subscriber_house_number' => $contract->house_number,
				'subscriber_zip' => $contract->zip,
				'subscriber_city' => $contract->city,
				'subscriber_district' => $contract->district,
			);
		}
		// edit
		else {
			$init_values = array();
		}

		// set hide flags for some entries depending on the current state
		$hide_flags = array(
			'external_activation_date' => '1',
			'deactivation_date' => '1',
			'external_deactivation_date' => '1',
			'porting_out' => '1',
		);

		// if activation date is set: show information about external activation and allow deactivation
		if (!is_null($model->activation_date)) {
			$hide_flags['external_activation_date'] = '0';
			$hide_flags['deactivation_date'] = '0';
			$hide_flags['porting_out'] = '0';
		}

		// if deactivation is set: show information about external deactivation
		if (!is_null($model->deactivation_date)) {
			$hide_flags['external_deactivation_date'] = '0';
		}

		/* d($model); */

		// help text for carrier/ekp settings
		if (\PPModule::is_active('ProvVoipEnvia')) {
			$trc_help = 'If changed here this has to be sent to Envia, too.';
			$carrier_in_help = 'In case of a new number set this to EnviaTEL';
			$ekp_in_help = 'In case of a new number set this to EnviaTEL';
		}
		else {
			$trc_help = 'This is for information only. Real changes have to be performed at your Telco.';
			$carrier_in_help = 'On incoming porting: set to previous Telco';
			$ekp_in_help = 'On incoming porting: set to previous Telco';
		}

		// label has to be the same like column in sql table
		$ret_tmp = array(
			array('form_type' => 'select', 'name' => 'phonenumber_id', 'description' => 'Phonenumber', 'value' => $model->html_list($model->phonenumber(), 'id'), 'hidden' => '1'),
			array('form_type' => 'select', 'name' => 'trcclass', 'description' => 'TRC class', 'value' => TRCClass::trcclass_list_for_form_select(), 'help' => $trc_help, 'space' => '1'),
			array('form_type' => 'text', 'name' => 'activation_date', 'description' => 'Activation date'),
			array('form_type' => 'text', 'name' => 'external_activation_date', 'description' => 'External activation date', 'options' => ['readonly'], 'hidden' => $hide_flags['external_activation_date']),
			array('form_type' => 'checkbox', 'name' => 'porting_in', 'description' => 'Incoming porting'),
			array('form_type' => 'select', 'name' => 'carrier_in', 'description' => 'Carrier in', 'value' => CarrierCode::carrier_list_for_form_select(False), 'help' => trans('helper.PhonenumberManagement_CarrierIn'), 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'select', 'name' => 'ekp_in', 'description' => 'EKP in', 'value' => EkpCode::ekp_list_for_form_select(False), 'help' => trans('helper.PhonenumberManagement_EkpIn'), 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'text', 'name' => 'deactivation_date', 'description' => 'Termination date'),
			array('form_type' => 'text', 'name' => 'external_deactivation_date', 'description' => 'External deactivation date', 'options' => ['readonly']),
			array('form_type' => 'checkbox', 'name' => 'porting_out', 'description' => 'Outgoing porting'),
			array('form_type' => 'select', 'name' => 'carrier_out', 'description' => 'Carrier out', 'value' => CarrierCode::carrier_list_for_form_select(True), 'checkbox' => 'show_on_porting_out'),

			// preset subscriber data => this comes from model
			array('form_type' => 'text', 'name' => 'subscriber_company', 'description' => 'Subscriber company', 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'text', 'name' => 'subscriber_department', 'description' => 'Subscriber department', 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'select', 'name' => 'subscriber_salutation', 'description' => 'Subscriber salutation', 'value' => $model->get_salutation_options(), 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'select', 'name' => 'subscriber_academic_degree', 'description' => 'Subscriber academic degree', 'value' => $model->get_academic_degree_options(), 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'text', 'name' => 'subscriber_firstname', 'description' => 'Subscriber firstname', 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'text', 'name' => 'subscriber_lastname', 'description' => 'Subscriber lastname', 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'text', 'name' => 'subscriber_street', 'description' => 'Subscriber street', 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'text', 'name' => 'subscriber_house_number', 'description' => 'Subscriber house number', 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'text', 'name' => 'subscriber_zip', 'description' => 'Subscriber zipcode', 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'text', 'name' => 'subscriber_city', 'description' => 'Subscriber city', 'checkbox' => 'show_on_porting_in'),
			array('form_type' => 'text', 'name' => 'subscriber_district', 'description' => 'Subscriber district', 'space' => '1', 'checkbox' => 'show_on_porting_in'),

			array('form_type' => 'text', 'name' => 'deactivation_date', 'description' => 'Termination date', 'hidden' => $hide_flags['deactivation_date']),
			array('form_type' => 'text', 'name' => 'external_deactivation_date', 'description' => 'External deactivation date', 'options' => ['readonly'], 'hidden' => $hide_flags['external_deactivation_date']),
			array('form_type' => 'checkbox', 'name' => 'porting_out', 'description' => 'Outgoing porting', 'hidden' => $hide_flags['porting_out']),
			array('form_type' => 'select', 'name' => 'carrier_out', 'description' => 'Carrier out', 'value' => CarrierCode::carrier_list_for_form_select(True), 'checkbox' => 'show_on_porting_out'),
		);

		// add init values if set
		$ret = array();
		foreach ($ret_tmp as $elem) {

			if (array_key_exists($elem['name'], $init_values)) {
				$elem['init_value'] = $init_values[$elem['name']];
			}
			array_push($ret, $elem);
		}

		return $ret;

	}


	/**
	 * Get all management jobs for Envia
	 *
	 * @author Patrick Reichel
	 * @param $model current phonenumber object
	 * @return array containing linktexts and URLs to perform actions against REST API
	 */
	public static function _get_envia_management_jobs($phonenumbermanagement) {

		$provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

		// check if user has the right to perform actions against Envia API
		\App\Http\Controllers\BaseAuthController::auth_check('view', 'Modules\ProvVoipEnvia\Entities\ProvVoipEnvia');

		return $provvoipenvia->get_jobs_for_view($phonenumbermanagement, 'phonenumbermanagement');
	}


	/**
	 * Overwrite BaseController method => not required dates should be set to null if not set
	 * Otherwise we get entries like 0000-00-00, which cause crashes on validation rules in case of update
	 *
	 * @author Patrick Reichel
	 */
	protected function prepare_input($data) {

		$data = parent::prepare_input($data);

		$nullable_fields = array(
			'activation_date',
			'deactivation_date',
		);
		$data = $this->_nullify_fields($data, $nullable_fields);


		return $data;
	}
}
