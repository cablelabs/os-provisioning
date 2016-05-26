<?php
namespace Modules\Provvoip\Http\Controllers;

use App\Http\Controllers\BaseModuleController;

class ProvVoipController extends BaseModuleController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'startid_mta', 'description' => 'Start ID MTA´s'),
			);
	}
}