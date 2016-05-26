<?php

namespace Modules\ProvVoip\Http\Controllers;

use Modules\ProvVoip\Entities\Phonenumber;
use Modules\ProvVoip\Entities\Mta;

class PhonenumberController extends \BaseModuleController {


	/**
	 * if set to true a create button on index view is available - set to true in BaseController as standard
	 */
    protected $index_create_allowed = false;
	protected $save_button = 'Save / Restart';


    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		if (!$model)
			$model = new Phonenumber;

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'country_code', 'description' => 'Country Code', 'value' => Phonenumber::getPossibleEnumValues('country_code')),
			array('form_type' => 'text', 'name' => 'prefix_number', 'description' => 'Prefix Number'),
			array('form_type' => 'text', 'name' => 'number', 'description' => 'Number'),
			array('form_type' => 'select', 'name' => 'mta_id', 'description' => 'MTA', 'value' => $model->mtas_list_with_dummies(), 'hidden' => '1'),
			array('form_type' => 'text', 'name' => 'port', 'description' => 'Port'),
			array('form_type' => 'text', 'name' => 'username', 'description' => 'Username', 'options' => array('placeholder' => 'autofilled if empty')),
			array('form_type' => 'text', 'name' => 'password', 'description' => 'Password', 'options' => array('placeholder' => 'autofilled if empty')),
			array('form_type' => 'text', 'name' => 'sipdomain', 'description' => 'SIP domain'),
			array('form_type' => 'checkbox', 'name' => 'active', 'description' => 'Active', 'checked' => true, 'create' => '1')
		);
	}


	/**
	 * Get all management jobs for Envia
	 *
	 * @author Patrick Reichel
	 * @param $model current phonenumber object
	 * @return array containing linktexts and URLs to perform actions against REST API
	 */
	public static function _get_envia_management_jobs($phonenumber) {

		$provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

		// check if user has the right to perform actions against Envia API
		\App\Http\Controllers\BaseAuthController::auth_check('view', 'Modules\ProvVoipEnvia\Entities\ProvVoipEnvia');

		return $provvoipenvia->get_jobs_for_view($phonenumber, 'phonenumber');
	}
}
