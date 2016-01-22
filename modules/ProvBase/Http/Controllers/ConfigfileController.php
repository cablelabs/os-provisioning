<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Configfile;
use Form;
use HTML;

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

	public static $INDEX = 0;

	/**
	 * writes whole index view data in string
	 *
	 * @param array with all configfiles in hierarchical tree structure
	 *
	 * @author Nino Ryschawy
	 */
	public function create_index_view_data($cf_tree)
	{
		$data = '';
		foreach ($cf_tree as $object)
		{
			if ($object == [])
				continue;

			if (is_array($object))
			{
				self::$INDEX += 1;
				$data .= $this->create_index_view_data($object);
			}
			else
				$data .= $this->print_cf_entry($object);
		}
		self::$INDEX -= 1;
		return $data;
	}

	/**
	 * writes whole index view data in string
	 *
	 * @param configfile or array with configfile(s) and arrays of configfiles
	 *
	 * @author Nino Ryschawy
	 */
	public function print_cf_entry($object)
	{
		$cur_model_complete = get_class($object);
		$cur_model_parts = explode('\\', $cur_model_complete);
		$cur_model = array_pop($cur_model_parts);

		// $data = '<tr>';
		$data = '';
		$cnt = 0;
		do
		{
			$cnt++;
			// $data .= '<td>&nbsp;&nbsp;&nbsp;</td>';
			$data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			var_dump(self::$INDEX);
		} while ($cnt <= self::$INDEX);

		// $data .= '<td>'.Form::checkbox('ids['.$object->id.']').'</td>';
		// $data .= '<td>'.HTML::linkRoute($cur_model.'.edit', $object->get_view_link_title(), $object->id).'</td>';
		// $data .= '</tr>';

		$data .= Form::checkbox('ids['.$object->id.']', 1, Null, null, ['style' => 'simple']).'&nbsp;&nbsp;';
		$data .= HTML::linkRoute($cur_model.'.edit', $object->get_view_link_title(), $object->id);
		$data .= '<br>';

		return $data;
	}

	/**
	 * Display a listing of all Configfile objects in hierarchical tree structure
	 *
	 * @author Nino Ryschawy
	 */
	public function index()
	{
		try {
			$this->_check_permissions("view");
		}
		catch (Exceptions $ex) {
			throw new AuthExceptions($e->getMessage());
		}
		
		$create_allowed = $this->index_create_allowed;

		$children = Configfile::all()->where('parent_id', 0)->all();

		$cf_tree = [];
		foreach ($children as $cf)
		{
			array_push($cf_tree, $cf);
			array_push($cf_tree, $cf->search_children());
		}

		$view_var = $this->create_index_view_data($cf_tree);

		return \View::make('provbase::Configfile.tree', $this->compact_prep_view(compact('panel_right', 'view_header_right', 'view_var', 'create_allowed', 'file', 'target', 'route_name', 'view_header', 'body_onload', 'field', 'search', 'preselect_field', 'preselect_value')));
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
