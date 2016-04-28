<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Qos;

// TODO: @Nino Ryschawy: directly includes does not work if billing module is disables
use Modules\BillingBase\Entities\Product;
use Modules\BillingBase\Entities\Item;
use Modules\BillingBase\Entities\CostCenter;
use Modules\BillingBase\Entities\Salesman;

class ContractController extends \BaseModuleController {


	protected $relation_create_button = "Add";

	// TODO: @Nino Ryschawy: add function documentation for the following stuff ..
	private function _qos_next_month($model)
	{
		$h = $model->html_list(Qos::all(), 'name');
		$h['0'] = '';
		asort($h);

		return $h;
	}

	private function _salesmen()
	{
		$salesmen[0] = null;
		foreach (Salesman::all() as $sm)
			$salesmen[$sm->id] = $sm->firstname.' '. $sm->lastname;
		return $salesmen;
	}

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		$r = $a = $b = $c = $d = [];

		// label has to be the same like column in sql table
		$a = array(

			// basic data
			array('form_type' => 'text', 'name' => 'number', 'description' => $model->get_column_description('number'), 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'number2', 'description' => $model->get_column_description('number2'), 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'number3', 'description' => $model->get_column_description('number3'), 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'number4', 'description' => $model->get_column_description('number4'), 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'company', 'description' => 'Company'),
			array('form_type' => 'select', 'name' => 'salutation', 'description' => 'Salutation', 'value' => $model->get_salutation_options()),
			array('form_type' => 'select', 'name' => 'academic_degree', 'description' => 'Academic degree', 'value' => $model->get_academic_degree_options()),
			array('form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname', 'create' => '1', 'space' => '1'),
			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'house_number', 'description' => 'House number', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'zip', 'description' => 'Postcode', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'phone', 'description' => 'Phone'),
			array('form_type' => 'text', 'name' => 'fax', 'description' => 'Fax'),
			array('form_type' => 'text', 'name' => 'email', 'description' => 'E-Mail Address'),
			array('form_type' => 'text', 'name' => 'birthday', 'description' => 'Birthday', 'space' => '1'),

			array('form_type' => 'checkbox', 'name' => 'network_access', 'description' => 'Internet Access', 'value' => '1', 'create' => '1'),
		);


		if ($model->voip_enabled) {

			$b = array(
				/* array('form_type' => 'text', 'name' => 'voip_contract_start', 'description' => 'VoIP Contract Start'), */
				/* array('form_type' => 'text', 'name' => 'voip_contract_end', 'description' => 'VoIP Contract End'), */
				array('form_type' => 'checkbox', 'name' => 'phonebook_entry', 'description' => 'Make phonebook entry', 'value' => '1', 'create' => '1'),

				/* array('form_type' => 'text', 'name' => 'voip_id', 'description' => 'Phone ID'), */
				/* array('form_type' => 'text', 'name' => 'next_voip_id', 'description' => 'Phone ID next month', 'space' => '1'), */
			);
		}

		if ($model->billing_enabled) {

			$c = array(
				array('form_type' => 'text', 'name' => 'contract_start', 'description' => 'Contract Start'), // TODO: create default 'value' => date("Y-m-d")
				array('form_type' => 'text', 'name' => 'contract_end', 'description' => 'Contract End'),
				array('form_type' => 'checkbox', 'name' => 'create_invoice', 'description' => 'Create Invoice', 'value' => '1'),
				array('form_type' => 'select', 'name' => 'costcenter_id', 'description' => 'Cost Center', 'value' => $model->html_list(CostCenter::all(), 'name')),
				array('form_type' => 'select', 'name' => 'salesman_id', 'description' => 'Salesman', 'value' => $this->_salesmen(), 'space' => '1'),
			);
		}
		else
		{
			$c = array(
				array('form_type' => 'select', 'name' => 'qos_id', 'description' => 'QoS', 'create' => '1', 'value' => $model->html_list(Qos::all(), 'name')),
				array('form_type' => 'select', 'name' => 'next_qos_id', 'description' => 'QoS next month', 'value' => $this->_qos_next_month($model)),
				array('form_type' => 'text', 'name' => 'voip_id', 'description' => 'Phone ID'),
				array('form_type' => 'text', 'name' => 'next_voip_id', 'description' => 'Phone ID next month', 'space' => '1'),
			);
		}

		$d = array (
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),
		);

		return array_merge($a, $b, $c, $d);
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

		// default: do nothing
		return null;
	}

	/**
	 * Get all management jobs for Envia
	 *
	 * @author Patrick Reichel
	 * @param $model current phonenumber object
	 * @return array containing linktexts and URLs to perform actions against REST API
	 */
	protected function _get_envia_management_jobs($contract) {

		$provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

		// check if user has the right to perform actions against Envia API
		// if not: don't show any actions
		try {
			$this->_check_permissions("view", 'Modules\ProvVoipEnvia\Entities\ProvVoipEnvia');
		}
		catch (PermissionDeniedError $ex) {
			return null;
		}

		return $provvoipenvia->get_jobs_for_view($contract, 'contract');
	}


	/**
	 * Overwrite BaseController method => not required dates should be set to null if not set
	 * Otherwise we get entries like 0000-00-00, which cause crashes on validation rules in case of update
	 *
	 * @author Patrick Reichel
	 */
	protected function default_input($data) {

		$data = parent::default_input($data);

		$nullable_fields = array(
			'contract_start',
			'contract_end',
			'voip_contract_start',
			'voip_contract_end',
		);

		foreach ($this->get_form_fields($this->get_model_obj()) as $field) {
			if (array_key_exists($field['name'], $data)) {
				if (array_search($field['name'], $nullable_fields) !== False) {
					if ($data[$field['name']] == '') {
						$data[$field['name']] = null;
					}
				}
			}
		}

		return $data;
	}

}
