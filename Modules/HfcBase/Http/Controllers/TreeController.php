<?php

namespace Modules\HfcBase\Http\Controllers;

use Modules\HfcBase\Entities\Tree;

class TreeController extends HfcBaseController {

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
			$model = new Tree;
			$parents = $model->first()->parents_list_all();
		}

		$kml_files = $model->kml_files();

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'value' => ''),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => 
				array('NET' => 'NET', 'CMTS' => 'CMTS', 'DATA' => 'DATA', 'CLUSTER' => 'CLUSTER', 'NODE' => 'NODE', 'AMP' => 'AMP')),
			array('form_type' => 'text', 'name' => 'ip', 'description' => 'IP Address', 'value' => ''),
			array('form_type' => 'text', 'name' => 'link', 'description' => 'HTML Link', 'value' => ''),
			array('form_type' => 'text', 'name' => 'pos', 'description' => 'Geoposition', 'value' => ''),
			array('form_type' => 'select', 'name' => 'parent', 'description' => 'Parent Object', 'value' => $parents),
			array('form_type' => 'select', 'name' => 'state', 'description' => 'State', 'value' => 
				array('OK' => 'OK', 'YELLOW' => 'YELLOW', 'RED' => 'RED'), 'options' => ['readonly']),		
			array('form_type' => 'text', 'name' => 'options', 'description' => 'Options', 'value' => ''),

			array('form_type' => 'select', 'name' => 'kml_file', 'description' => 'Choose KML file', 'value' => $kml_files),
			array('form_type' => 'file', 'name' => 'kml_file_upload', 'description' => 'or: Upload KML file'),

			array('form_type' => 'textarea', 'name' => 'descr', 'description' => 'Description', 'value' => ''),
			
				
		);
	}


	/**
	 * Overwrites the base method 
	 */
	protected function store() 
	{
		// check and handle uploaded KML files
		$this->handle_file_upload('kml_file', $this->get_model_obj()->kml_path);

		// call base method
		$ret = parent::store();

		Tree::relation_index_build_all();

		return $ret;
	}

	/**
	 * Overwrites the base method 
	 */
	public function update($id) 
	{
		// check and handle uploaded KML files
		$this->handle_file_upload('kml_file', $this->get_model_obj()->kml_path);

		// call base method
		$ret = parent::update($id);

		Tree::relation_index_build_all();

		return $ret;
	}

	/**
	 * Overwrites the base method 
	 */
	public function destroy ($id)
	{
		// call base method
		$ret = parent::destroy($id);

		Tree::relation_index_build_all();

		return $ret;
	}

	/**
	 * Overwrites the base method 
	 * Usage: ERD - right click - delete
	 * Note: needs special GET route in routes.php
	 */
    public function delete ($id)
    {
    	parent::destroy($id);

    	Tree::relation_index_build_all();

    	return \Redirect::back();
    }

}
