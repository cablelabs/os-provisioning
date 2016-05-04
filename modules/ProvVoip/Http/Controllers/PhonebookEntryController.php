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

		return array(

			/* todo: write the rest of the form (attention: some special cases!!!) */
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
}
