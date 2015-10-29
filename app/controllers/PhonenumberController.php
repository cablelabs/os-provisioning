<?php

use Models\Phonenumber;
use Models\Mta;

class PhonenumberController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		if (!$model)
			$model = new Phonenumber;

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'country_code', 'description' => 'Country Code', 'value' => Phonenumber::getPossibleEnumValues('country_code')),
			array('form_type' => 'text', 'name' => 'prefix_number', 'description' => 'Prefix Number'),
			array('form_type' => 'text', 'name' => 'number', 'description' => 'Number'),
			array('form_type' => 'select', 'name' => 'mta_id', 'description' => 'MTA', 'value' => $model->mtas_list_with_dummies()),
			array('form_type' => 'text', 'name' => 'port', 'description' => 'Port'),
			array('form_type' => 'text', 'name' => 'username', 'description' => 'Username'),
			array('form_type' => 'select', 'name' => 'password', 'description' => 'Password'),
			array('form_type' => 'select', 'name' => 'active', 'description' => 'Active?', 'value' => array( '1' => 'Yes', '0' => 'No')),
		);
	}



	/**
	 * Show the form for creating a new phonenumber
	 *
	 * @return Response
	 */
	// public function create()
	// {
	// 	// set mta_id if given (if phonenumber creation is started from mta edit view)
	// 	$mta_id = Input::get('mta_id', 0);
	// 	// don't use is_int as form data is always a string!
	// 	if (!is_numeric($mta_id)) {
	// 		$mta_id = 0;
	// 	}

	// 	return View::make('phonenumbers.create')->with('mtas', $this->mtas_list_with_dummies())->with('country_codes', Phonenumber::getPossibleEnumValues('country_code'))->with('mta_id', $mta_id);
	// }



	/**
	 * Show the form for editing the specified phonenumber.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	// public function edit($id)
	// {
	// 	$phonenumber = Phonenumber::findOrFail($id);

	// 	return View::make('phonenumbers.edit', compact('phonenumber'))->with('mtas', $this->mtas_list_with_dummies())->with('country_codes', Phonenumber::getPossibleEnumValues('country_code'));
	// }


}
