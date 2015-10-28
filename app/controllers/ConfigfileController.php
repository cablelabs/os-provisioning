<?php

use Models\Configfile;

class ConfigfileController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		if ($model)
			$parents = $model->parents_list();
		else
		{
			$model = new Configfile; 
			$parents = $model->first()->parents_list_all();
		}

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => array('generic' => 'generic', 'network' => 'network', 'vendor' => 'vendor', 'user' => 'user')),
			array('form_type' => 'select', 'name' => 'device', 'description' => 'Device', 'value' => array('cm' => 'CM', 'mta' => 'MTA')),
			array('form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Configfile', 'value' => $parents),
			array('form_type' => 'select', 'name' => 'public', 'description' => 'Public Use', 'value' => array('yes' => 'Yes', 'no' => 'No')),
			array('form_type' => 'textarea', 'name' => 'text', 'description' => 'Config File Parameters'),
		);
	}

}
