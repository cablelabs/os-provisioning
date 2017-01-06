<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Configfile;
use Form;
use HTML;

class ConfigfileController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		if ($model) {
			$parents = $model->parents_list();
		}
		else
		{
			$model = new Configfile;
			$parents = $model->first()->parents_list_all();
		}
		$firmware_files = $model->get_files("fw");
		$cvc_files = $model->get_files("cvc");

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => array('generic' => 'generic', 'network' => 'network', 'vendor' => 'vendor', 'user' => 'user')),
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
	 * Display a listing of all Configfile objects in hierarchical tree structure
	 *
	 * @author Nino Ryschawy
	 */
	public function index()
	{
		$create_allowed = $this->index_create_allowed;
		$roots = Configfile::where('parent_id', 0)->get();
		$cf_used = Configfile::all_in_use();
		// tree_item.blade.php: https://laracasts.com/discuss/channels/laravel/categories-tree-view/replies/114604
		return \View::make('provbase::Configfile.tree', $this->compact_prep_view(compact('view_header', 'roots', 'cf_used', 'create_allowed')));
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
