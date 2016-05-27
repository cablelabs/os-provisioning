<?php

namespace Modules\ProvVoip\Http\Controllers;

use Modules\ProvVoip\Entities\PhoneTariff;

class PhoneTariffController extends \BaseController {


    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		if (!$model)
			$model = new PhoneTariff;

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'external_identifier', 'description' => 'External identifier'),
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => PhoneTariff::getPossibleEnumValues('type')),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),
			array('form_type' => 'checkbox', 'name' => 'usable', 'description' => 'Usable', 'create' => '1'),
		);

	}
}
