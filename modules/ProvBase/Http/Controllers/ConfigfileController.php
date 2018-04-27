<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Configfile;
use Form;
use HTML;

class ConfigfileController extends \BaseController {

	protected $index_tree_view = true;

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		if (!$model)
			$model = new Configfile;

		$parents = $model->html_list(Configfile::where('id', '!=', $model->id)->get()->all(), 'name', true);
		$firmware_files = Configfile::get_files("fw");
		$cvc_files = Configfile::get_files("cvc");

		// label has to be the same like column in sql table
		// TODO: type is without functionality -> hidden
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => array('generic' => 'generic', 'network' => 'network', 'vendor' => 'vendor', 'user' => 'user'), 'hidden' => 1),
			array('form_type' => 'select', 'name' => 'device', 'description' => 'Device', 'value' => array('cm' => 'CM', 'mta' => 'MTA')),
			array('form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Configfile', 'value' => $parents),
			array('form_type' => 'select', 'name' => 'public', 'description' => 'Public Use', 'value' => array('yes' => 'Yes', 'no' => 'No')),
			array('form_type' => 'textarea', 'name' => 'text', 'description' => 'Config File Parameters'),
			array('form_type' => 'select', 'name' => 'firmware', 'description' => 'Choose Firmware File', 'value' => $firmware_files),
			array('form_type' => 'file', 'name' => 'firmware_upload', 'description' => 'or: Upload Firmware File'),
			array('form_type' => 'select', 'name' => 'cvc', 'description' => 'Choose Certificate File', 'value' => $cvc_files, 'help' => $model->get_cvc_help()),
		);
	}

	/**
	 * Returns validation data array with correct device type for validation of config text
	 *
	 * @author Nino Ryschawy
	 */
	public function prepare_rules($rules, $data)
	{
		$rules['text'] .= ':'.$data['device'];
		return $rules;
	}


	/**
	 * Overwrites the base method => we need to handle file uploads
	 * @author Patrick Reichel
	 */
	public function store ($redirect = true) {

		// check and handle uploaded firmware files
		$this->handle_file_upload('firmware', '/tftpboot/fw/');

		// finally: call base method
		return parent::store();
	}

	/**
	 * Overwrites the base method => we need to handle file uploads
	 * @author Patrick Reichel
	 */
	public function update($id) {

		// check and handle uploaded firmware files
		$this->handle_file_upload('firmware', '/tftpboot/fw/');

		// finally: call base method
		return parent::update($id);
	}

}
