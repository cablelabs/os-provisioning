<?php

namespace Modules\ProvVoip\Http\Controllers;

use Modules\ProvVoip\Entities\PhonenumberManagement;
use Modules\ProvVoip\Entities\Phonenumber;
use Modules\ProvVoipEnvia\Entities\ProvVoipEnvia;

class PhonenumberManagementController extends \BaseModuleController {


	/**
	 * if set to true a create button on index view is available - set to true in BaseController as standard
	 */
    protected $index_create_allowed = false;


    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		if (!$model)
			$model = new PhonenumberManagement;

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'phonenumber_id', 'description' => 'Phonenumber', 'value' => $model->phonenumber_list_with_dummies(), 'hidden' => '1'),
			array('form_type' => 'text', 'name' => 'activation_date', 'description' => 'Activation date'),
			array('form_type' => 'checkbox', 'name' => 'porting_in', 'description' => 'Incoming porting'),
			array('form_type' => 'select', 'name' => 'carrier_in', 'description' => 'Carrier in', 'value' => array('08/15' => 'TODO: store carriers in database', 'DE001' => 'Deutsche Telekom')),
			array('form_type' => 'text', 'name' => 'deactivation_date', 'description' => 'Termination date'),
			array('form_type' => 'checkbox', 'name' => 'porting_out', 'description' => 'Outgoing porting'),
			array('form_type' => 'select', 'name' => 'carrier_out', 'description' => 'Carrier out', 'value' => array('08/15' => 'TODO: store carriers in database', 'DE001' => 'Deutsche Telekom')),
			array('form_type' => 'text', 'name' => 'subscriber_company', 'description' => 'Subscriber company'),
			array('form_type' => 'select', 'name' => 'subscriber_salutation', 'description' => 'Subscriber salutation', 'value' => PhonenumberManagement::getPossibleEnumValues('subscriber_salutation')),
			array('form_type' => 'select', 'name' => 'subscriber_academic_degree', 'description' => 'Subscriber academic degree', 'value' => PhonenumberManagement::getPossibleEnumValues('subscriber_academic_degree')),
			array('form_type' => 'text', 'name' => 'subscriber_firstname', 'description' => 'Subscriber firstname'),
			array('form_type' => 'text', 'name' => 'subscriber_lastname', 'description' => 'Subscriber lastname'),
			array('form_type' => 'text', 'name' => 'subscriber_street', 'description' => 'Subscriber street'),
			array('form_type' => 'text', 'name' => 'subscriber_house_number', 'description' => 'Subscriber house number'),
			array('form_type' => 'text', 'name' => 'subscriber_zip', 'description' => 'Subscriber zipcode'),
			array('form_type' => 'text', 'name' => 'subscriber_city', 'description' => 'Subscriber city'),

		);
	}

	/**
	 * Wrapper to get all jobs for the current phonenumber
	 * This can be used as a switch for several providers like envia etc. – simply check if the module exists :-)
	 *
	 * @author Patrick Reichel
	 */
	protected function _get_extra_data($view_var) {
		return $this->_get_envia_management_jobs($view_var);
	}

	/**
	 * Get all management jobs for Envia
	 *
	 * @author Patrick Reichel
	 * @param $model current phonenumber object
	 * @return array containing linktexts and URLs to perform actions against REST API
	 */
	protected function _get_envia_management_jobs($phonenumbermanagement) {

		$provvoipenvia = new ProvVoipEnvia();

		return $provvoipenvia->get_jobs_for_view($phonenumbermanagement, 'phonenumbermanagement');
	}


}
