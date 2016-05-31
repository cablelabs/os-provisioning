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
				'subscriber_salutation' => $contract->salutation,
				'subscriber_academic_degree' => $contract->academic_degree,
				'subscriber_firstname' => $contract->firstname,
				'subscriber_lastname' => $contract->lastname,
				'subscriber_street' => $contract->street,
				'subscriber_house_number' => $contract->house_number,
				'subscriber_zip' => $contract->zip,
				'subscriber_city' => $contract->city,
			);
		}
		// edit
		else {
			$init_values = array();
		}

		// preset subscriber data => this comes from model


		// label has to be the same like column in sql table
		$ret_tmp = array(
			array('form_type' => 'select', 'name' => 'phonenumber_id', 'description' => 'Phonenumber', 'value' => $model->html_list($model->phonenumber(), 'id'), 'hidden' => '1'),
			array('form_type' => 'select', 'name' => 'trcclass', 'description' => 'TRC class', 'value' => TRCClass::trcclass_list_for_form_select()),
			array('form_type' => 'text', 'name' => 'activation_date', 'description' => 'Activation date'),
			array('form_type' => 'checkbox', 'name' => 'porting_in', 'description' => 'Incoming porting'),
			array('form_type' => 'select', 'name' => 'carrier_in', 'description' => 'Carrier in', 'value' => CarrierCode::carrier_list_for_form_select(False), 'help' => 'In case of a new number set this to EnviaTEL'),
			array('form_type' => 'select', 'name' => 'ekp_in', 'description' => 'EKP in', 'value' => EkpCode::ekp_list_for_form_select(False), 'help' => 'In case of a new number set this to EnviaTEL'),
			array('form_type' => 'text', 'name' => 'deactivation_date', 'description' => 'Termination date'),
			array('form_type' => 'checkbox', 'name' => 'porting_out', 'description' => 'Outgoing porting'),
			array('form_type' => 'select', 'name' => 'carrier_out', 'description' => 'Carrier out', 'value' => CarrierCode::carrier_list_for_form_select(True)),

			array('form_type' => 'text', 'name' => 'subscriber_company', 'description' => 'Subscriber company'),
			array('form_type' => 'select', 'name' => 'subscriber_salutation', 'description' => 'Subscriber salutation', 'value' => $model->get_salutation_options()),
			array('form_type' => 'select', 'name' => 'subscriber_academic_degree', 'description' => 'Subscriber academic degree', 'value' => $model->get_academic_degree_options()),
			array('form_type' => 'text', 'name' => 'subscriber_firstname', 'description' => 'Subscriber firstname'),
			array('form_type' => 'text', 'name' => 'subscriber_lastname', 'description' => 'Subscriber lastname'),
			array('form_type' => 'text', 'name' => 'subscriber_street', 'description' => 'Subscriber street'),
			array('form_type' => 'text', 'name' => 'subscriber_house_number', 'description' => 'Subscriber house number'),
			array('form_type' => 'text', 'name' => 'subscriber_zip', 'description' => 'Subscriber zipcode'),
			array('form_type' => 'text', 'name' => 'subscriber_city', 'description' => 'Subscriber city'),
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

}
