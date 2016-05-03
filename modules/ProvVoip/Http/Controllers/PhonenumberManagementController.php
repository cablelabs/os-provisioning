<?php

namespace Modules\ProvVoip\Http\Controllers;

use Modules\ProvVoip\Entities\PhonenumberManagement;
use Modules\ProvVoip\Entities\Phonenumber;
use Modules\ProvVoip\Entities\CarrierCode;
use Modules\ProvVoipEnvia\Entities\TRCClass;

class PhonenumberManagementController extends \BaseModuleController {


	/**
	 * if set to true a create button on index view is available - set to true in BaseController as standard
	 */
    protected $index_create_allowed = false;


    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		// create
		if (!$model) {
			$model = new PhonenumberManagement;
		}

		// in most cases the subscriber is identical to contract partner ⇒ on create we prefill these values with data from contract
		if (!$model->exists) {
			$phonenumber = Phonenumber::findOrFail(\Input::get('phonenumber_id'));
			$contract = $phonenumber->mta->modem->contract;

			$subscriber = array(
				'company' => $contract->company,
				'salutation' => $contract->salutation,
				'academic_degree' => $contract->academic_degree,
				'firstname' => $contract->firstname,
				'lastname' => $contract->lastname,
				'street' => $contract->street,
				'house_number' => $contract->house_number,
				'zip' => $contract->zip,
				'city' => $contract->city,
			);
		}
		// edit
		else {
			$subscriber = array(
				'company' => $model->subscriber_company,
				'salutation' => $model->subscriber_salutation,
				'academic_degree' => $model->subscriber_academic_degree,
				'firstname' => $model->subscriber_firstname,
				'lastname' => $model->subscriber_lastname,
				'street' => $model->subscriber_street,
				'house_number' => $model->subscriber_house_number,
				'zip' => $model->subscriber_zip,
				'city' => $model->subscriber_city,
			);
		}

		// preset subscriber data => this comes from model


		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'phonenumber_id', 'description' => 'Phonenumber', 'value' => $model->phonenumber_list_with_dummies(), 'hidden' => '1'),
			array('form_type' => 'select', 'name' => 'trcclass', 'description' => 'TRC class', 'value' => TRCClass::trcclass_list_for_form_select()),
			array('form_type' => 'text', 'name' => 'activation_date', 'description' => 'Activation date'),
			array('form_type' => 'checkbox', 'name' => 'porting_in', 'description' => 'Incoming porting'),
			array('form_type' => 'select', 'name' => 'carrier_in', 'description' => 'Carrier in', 'value' => CarrierCode::carrier_list_for_form_select()),
			array('form_type' => 'text', 'name' => 'deactivation_date', 'description' => 'Termination date'),
			array('form_type' => 'checkbox', 'name' => 'porting_out', 'description' => 'Outgoing porting'),
			array('form_type' => 'select', 'name' => 'carrier_out', 'description' => 'Carrier out', 'value' => CarrierCode::carrier_list_for_form_select()),

			array('form_type' => 'text', 'name' => 'subscriber_company', 'description' => 'Subscriber company', 'value' => $subscriber['company']),
			array('form_type' => 'select', 'name' => 'subscriber_salutation', 'description' => 'Subscriber salutation', 'value' => $model->get_salutation_options(), 'options' => array('selected' => $subscriber['salutation'])),
			array('form_type' => 'select', 'name' => 'subscriber_academic_degree', 'description' => 'Subscriber academic degree', 'value' => $model->get_academic_degree_options(), 'options' => array('selected' => $subscriber['academic_degree'])),
			array('form_type' => 'text', 'name' => 'subscriber_firstname', 'description' => 'Subscriber firstname', 'value' => $subscriber['firstname']),
			array('form_type' => 'text', 'name' => 'subscriber_lastname', 'description' => 'Subscriber lastname', 'value' => $subscriber['lastname']),
			array('form_type' => 'text', 'name' => 'subscriber_street', 'description' => 'Subscriber street', 'value' => $subscriber['street']),
			array('form_type' => 'text', 'name' => 'subscriber_house_number', 'description' => 'Subscriber house number', 'value' => $subscriber['house_number']),
			array('form_type' => 'text', 'name' => 'subscriber_zip', 'description' => 'Subscriber zipcode', 'value' => $subscriber['zip']),
			array('form_type' => 'text', 'name' => 'subscriber_city', 'description' => 'Subscriber city', 'value' => $subscriber['city']),

			todo: write the rest of the form (attention: some special cases!!!)
			array('form_type' => 'checkbox', 'name' => 'phonebook_entry', 'description' => 'Phonebook entry'),
			array('form_type' => 'checkbox', 'name' => 'reverse_search', 'description' => 'Reverse search'),
			array('form_type' => 'checkbox', 'name' => 'phonebook_publish_in_print_media', 'description' => 'Usage in print media'),
			array('form_type' => 'checkbox', 'name' => 'phonebook_publish_in_electronic_media', 'description' => 'Usage electronic media'),
			array('form_type' => 'text', 'name' => 'phonebook_directory_assistance', 'description' => 'Directory assistance', 'help' => 'N – keine Auskunft, S – Rufnummernauskunft, J – Rufnummernauskunft mit weiteren Angaben', 'options' => array('size' => '1', 'maxlength' => '1')),
			array('form_type' => 'text', 'name' => 'phonebook_entry_type', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_publish_address', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_company', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_academic_degree', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_noble_rank', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_nobiliary_particle', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_lastname', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_other_name_suffix', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_firstname', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_street', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_houseno', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_zipcode', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_city', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_urban_district', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_business', 'description' => ''),
			array('form_type' => 'text', 'name' => 'phonebook_usage', 'description' => 'Usage type in print media', 'help' => 'T(elefon), F(ax) or K(ombiniert)', 'options' => array('size' => '1', 'maxlength' => '1')),
			array('form_type' => 'text', 'name' => 'phonebook_tag', 'description' => ''),

		);
	}

	/**
	 * Wrapper to get all jobs for the current phonenumber
	 * This can be used as a switch for several providers like envia etc. – simply check if the module exists :-)
	 * If no module is active we return the default value “null” – nothing will be shown
	 *
	 * @author Patrick Reichel
	 */
	protected function _get_extra_data($view_var) {

		if ($this->get_model_obj()->module_is_active('ProvVoipEnvia')) {
			return $this->_get_envia_management_jobs($view_var);
		}

		// default: nothing to do
		return null;
	}

	/**
	 * Get all management jobs for Envia
	 *
	 * @author Patrick Reichel
	 * @param $model current phonenumber object
	 * @return array containing linktexts and URLs to perform actions against REST API
	 */
	protected function _get_envia_management_jobs($phonenumbermanagement) {

		$provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

		// check if user has the right to perform actions against Envia API
		// if not: don't show any actions
		try {
			$this->_check_permissions("view", "Modules\ProvVoipEnvia\Entities\ProvVoipEnvia");
		}
		catch (PermissionDeniedError $ex) {
			return null;
		}

		return $provvoipenvia->get_jobs_for_view($phonenumbermanagement, 'phonenumbermanagement');
	}

}
