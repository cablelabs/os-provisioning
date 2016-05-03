<?php

namespace Modules\ProvVoip\Http\Controllers;

use Modules\ProvVoip\Entities\Phonenumber;
use Modules\ProvVoip\Entities\Mta;

class PhonenumberController extends \BaseModuleController {


	/**
	 * if set to true a create button on index view is available - set to true in BaseController as standard
	 */
    protected $index_create_allowed = false;
	protected $save_button = 'Save and Restart Modem';


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
			array('form_type' => 'select', 'name' => 'mta_id', 'description' => 'MTA', 'value' => $model->mtas_list_with_dummies(), 'hidden' => '1'),
			array('form_type' => 'text', 'name' => 'port', 'description' => 'Port'),
			array('form_type' => 'text', 'name' => 'username', 'description' => 'Username (autofilled if empty)'),
			array('form_type' => 'text', 'name' => 'password', 'description' => 'Password (autofilled if empty)'),
			array('form_type' => 'text', 'name' => 'sipdomain', 'description' => 'SIP domain'),
			array('form_type' => 'select', 'name' => 'active', 'description' => 'Active?', 'value' => array( '1' => 'Yes', '0' => 'No'))
		);
	}


	/**
	 * Wrapper to get all jobs for the current phonenumber
	 * This can be used as a switch for several providers like envia etc. – simply check if the module exists :-)
	 * If no module is active we return the default value “null” – nothing will be shown
	 *
	 * @author Patrick Reichel
	 */
	protected function _get_extra_data($view_var) {

		if ($this->get_model_obj()->module_is_active('ProvVoipEnvia')) {
			return $this->_get_envia_management_jobs($view_var);
		}

		// default: nothing to do
		return null;
	}

	/**
	 * Get all management jobs for Envia
	 *
	 * @author Patrick Reichel
	 * @param $model current phonenumber object
	 * @return array containing linktexts and URLs to perform actions against REST API
	 */
	protected function _get_envia_management_jobs($phonenumber) {

		$provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

		// check if user has the right to perform actions against Envia API
		// if not: don't show any actions
		try {
			$this->_check_permissions("view", "Modules\ProvVoipEnvia\Entities\ProvVoipEnvia");
		}
		catch (PermissionDeniedError $ex) {
			return null;
		}

		return $provvoipenvia->get_jobs_for_view($phonenumber, 'phonenumber');
	}
}
