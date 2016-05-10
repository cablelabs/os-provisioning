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

		return array(

			/* todo: write the rest of the form (attention: some special cases!!!) */
			array('form_type' => 'select', 'name' => 'phonenumbermanagement_id', 'description' => 'PhonenumberManagement', 'value' => $model->html_list($model->phonenumbermanagement(), 'id'), 'hidden' => '1'),
			array('form_type' => 'checkbox', 'name' => 'reverse_search', 'description' => 'Reverse search'),
			array('form_type' => 'select', 'name' => 'publish_in_print_media', 'description' => 'Entry in print media', 'value' => $model->get_options_from_list('publish_in_print_media')),
			array('form_type' => 'select', 'name' => 'publish_in_electronic_media', 'description' => 'Entry electronic media', 'value' => $model->get_options_from_list('publish_in_electronic_media')),
			array('form_type' => 'select', 'name' => 'directory_assistance', 'description' => 'Directory assistance', 'value' => $model->get_options_from_list('directory_assistance')),
			array('form_type' => 'select', 'name' => 'entry_type', 'description' => 'Entry type', 'value' => $model->get_options_from_list('entry_type')),
			array('form_type' => 'select', 'name' => 'publish_address', 'description' => 'Publish address', 'value' => $model->get_options_from_list('publish_address')),
			array('form_type' => 'text', 'name' => 'company', 'description' => 'Company', 'value' => $phonebook_data['company']),
			array('form_type' => 'text', 'name' => 'academic_degree', 'description' => 'Academic degree', 'value' => $phonebook_data['academic_degree']),
			array('form_type' => 'text', 'name' => 'noble_rank', 'description' => 'Noble rank'),
			array('form_type' => 'text', 'name' => 'nobiliary_particle', 'description' => 'Nobiliary particle'),
			array('form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname', 'value' => $phonebook_data['lastname']),
			array('form_type' => 'text', 'name' => 'other_name_suffix', 'description' => 'Other name suffix'),
			array('form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname', 'value' => $phonebook_data['firstname']),
			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street', 'value' => $phonebook_data['street']),
			array('form_type' => 'text', 'name' => 'houseno', 'description' => 'House number', 'value' => $phonebook_data['house_number']),
			array('form_type' => 'text', 'name' => 'zipcode', 'description' => 'Zipcode', 'value' => $phonebook_data['zip']),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City', 'value' => $phonebook_data['city']),
			array('form_type' => 'text', 'name' => 'urban_district', 'description' => 'Urban district'),
			array('form_type' => 'text', 'name' => 'business', 'description' => 'Business'),
			array('form_type' => 'select', 'name' => 'number_usage', 'description' => 'Number usage', 'value' => $model->get_options_from_list('usage')),
			array('form_type' => 'text', 'name' => 'tag', 'description' => 'Tag'),

		);

	}
}
