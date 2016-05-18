<?php

namespace Modules\ProvVoip\Http\Controllers;

use Modules\ProvVoip\Entities\PhonebookEntry;
use Modules\ProvVoip\Entities\PhonenumberManagement;

class PhonebookEntryController extends \BaseModuleController {


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
			$model = new PhonebookEntry;
		}

		// set reference for later use
		$this->model = $model;

		// in most cases the phonebook data is identical to contract's data ⇒ on create we prefill these values with data from contract
		if (!$model->exists) {
			$phonenumbermanagement = PhonenumberManagement::findOrFail(\Input::get('phonenumbermanagement_id'));
			$contract = $phonenumbermanagement->phonenumber->mta->modem->contract;

			$phonebook_data = array(
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
			$phonebook_data = array(
				'company' => $model->company,
				'salutation' => $model->salutation,
				'academic_degree' => $model->academic_degree,
				'firstname' => $model->firstname,
				'lastname' => $model->lastname,
				'street' => $model->street,
				'house_number' => $model->houseno,
				'zip' => $model->zipcode,
				'city' => $model->city,
			);
		}

		// helper to set selected correctly
		// if nothing is set we need to return an empty string – on null every entry in dropdown get a selected option
		/* $get_selected = function($field) { */

		/* 	if (!is_null($this->model->$field)) { */
		/* 		dd($this->model->$field); */
		/* 		return $this->model->$field; */
		/* 	} */
		/* 	else { */
		/* 		return ''; */
		/* 	} */
		/* }; */

		return array(

			/* todo: write the rest of the form (attention: some special cases!!!) */
			array('form_type' => 'select', 'name' => 'phonenumbermanagement_id', 'description' => 'PhonenumberManagement', 'value' => $model->html_list($model->phonenumbermanagement(), 'id'), 'hidden' => '1'),
			array('form_type' => 'select', 'name' => 'reverse_search', 'description' => 'Reverse search', 'value' => $model->get_options_from_list('reverse_search')),
			array('form_type' => 'select', 'name' => 'publish_in_print_media', 'description' => 'Entry in print media', 'value' => $model->get_options_from_list('publish_in_print_media')),
			array('form_type' => 'select', 'name' => 'publish_in_electronic_media', 'description' => 'Entry electronic media', 'value' => $model->get_options_from_list('publish_in_electronic_media')),
			array('form_type' => 'select', 'name' => 'directory_assistance', 'description' => 'Directory assistance', 'value' => $model->get_options_from_list('directory_assistance')),
			array('form_type' => 'select', 'name' => 'entry_type', 'description' => 'Entry type', 'value' => $model->get_options_from_list('entry_type')),
			array('form_type' => 'select', 'name' => 'publish_address', 'description' => 'Publish address', 'value' => $model->get_options_from_list('publish_address')),
			array('form_type' => 'text', 'name' => 'company', 'description' => 'Company', 'value' => $phonebook_data['company']),
			array('form_type' => 'select', 'name' => 'academic_degree', 'description' => 'Academic degree', 'value' => $model->get_options_from_file('academic_degree')),
			array('form_type' => 'select', 'name' => 'noble_rank', 'description' => 'Noble rank', 'value' => $model->get_options_from_file('noble_rank')),
			array('form_type' => 'select', 'name' => 'nobiliary_particle', 'description' => 'Nobiliary particle', 'value' => $model->get_options_from_file('nobiliary_particle')),
			array('form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname', 'value' => $phonebook_data['lastname']),
			array('form_type' => 'text', 'name' => 'other_name_suffix', 'description' => 'Other name suffix'),
			array('form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname', 'value' => $phonebook_data['firstname']),
			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street', 'value' => $phonebook_data['street']),
			array('form_type' => 'text', 'name' => 'houseno', 'description' => 'House number', 'value' => $phonebook_data['house_number']),
			array('form_type' => 'text', 'name' => 'zipcode', 'description' => 'Zipcode', 'value' => $phonebook_data['zip']),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City', 'value' => $phonebook_data['city']),
			array('form_type' => 'text', 'name' => 'urban_district', 'description' => 'Urban district'),
			array('form_type' => 'select', 'name' => 'business', 'description' => 'Business', 'value' => $model->get_options_from_file('business')),
			array('form_type' => 'select', 'name' => 'number_usage', 'description' => 'Number usage', 'value' => $model->get_options_from_list('number_usage')),
			array('form_type' => 'select', 'name' => 'tag', 'description' => 'Tag', 'value' => $model->get_options_from_file('tag')),

		);

	}


	/**
	 * Replaces the placeholders (named like the array key inside the data array/sql columns)
	 * in the rules array with the needed data of the data array;
	 *
	 * used in own validation
	 *
	 * @author Nino Ryschawy
	 * @author Patrick Reichel
	 */
	public function prep_rules($rules, $data) {

		// lambda to replace strings after a colon
		$replace_after_colon = function(&$subject, $key, $replacement_data=['search'=>'', 'replace'=>'']) {

			$search = $replacement_data['search'];
			$replace = $replacement_data['replace'];

			// split on colon
			$parts = explode(':', $subject);

			// check number of colons
			$colons = substr_count($subject, ':');
			if ($colons == 0) {
				// nothing to do
				return;
			}
			elseif ($colons > 1) {
				// should only occur on regex which we don't use in this validations; let us throw some Exceptions to avoid unexpected behaviour
				if (\Str::startsWith($subject, 'regex')) {
					throw new \UnexpectedValueException('Replacement in validation rule regex not (yet) implemented');
				}
				else {
					throw new \InvalidArgumentException('There are multiple colons on validation rule '.$parts[0].'. This is not (yet) implemented');
				}
			}

			// don't replace on required_* rules
			if (\Str::startsWith($parts[0], 'required_')) {
				return;
			}

			$parts[1] = str_replace($search, $replace, $parts[1]);
			$subject = implode(':', $parts);

		};

		// process rules for each form field (= name)
		foreach ($rules as $form_name => $form_name_rules)
		{
			$form_name_rules = explode('|', $form_name_rules);

			// we need to go through complete data => e.g. we need to replace lastname AND entry_type in valitation of lastname
			foreach ($data as $varname => $value) {

				// replace varnames after colons by there value
				$replacement_data = array('search' => $varname, 'replace' => $value);
				array_walk($form_name_rules, $replace_after_colon, $replacement_data);
			}

			// rebuild the rule
			$rules[$form_name] = implode('|', $form_name_rules);
		};

		return $rules;
	}


	/**
	 * Wrapper to get all jobs for the current phoneboookentry
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
	protected function _get_envia_management_jobs($phonebookentry) {

		$provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

		// check if user has the right to perform actions against Envia API
		// if not: don't show any actions
		try {
			$this->_check_permissions("view", "Modules\ProvVoipEnvia\Entities\ProvVoipEnvia");
		}
		catch (PermissionDeniedError $ex) {
			return null;
		}

		return $provvoipenvia->get_jobs_for_view($phonebookentry, 'phonebookentry');
	}
}
