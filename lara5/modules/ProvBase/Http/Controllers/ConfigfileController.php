<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Configfile;

class ConfigfileController extends \BaseModuleController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		if ($model) {
			$parents = $model->parents_list();
		}
		else
		{
			$model = new Configfile;
			$parents = $model->first()->parents_list_all();
		}
		$firmware_files = $model->firmware_files();

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => array('generic' => 'generic', 'network' => 'network', 'vendor' => 'vendor', 'user' => 'user')),
			array('form_type' => 'select', 'name' => 'device', 'description' => 'Device', 'value' => array('cm' => 'CM', 'mta' => 'MTA')),
			array('form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Configfile', 'value' => $parents),
			array('form_type' => 'select', 'name' => 'public', 'description' => 'Public Use', 'value' => array('yes' => 'Yes', 'no' => 'No')),
			array('form_type' => 'textarea', 'name' => 'text', 'description' => 'Config File Parameters'),
			array('form_type' => 'select', 'name' => 'firmware', 'description' => 'Choose firmware file', 'value' => $firmware_files),
			array('form_type' => 'file', 'name' => 'firmware_upload', 'description' => 'or: Upload firmware file'),
		);
	}

	/**
	 * Overwrites the base method => we need to handle file uploads
	 * @author Patrick Reichel
	 */
	protected function store() {

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
