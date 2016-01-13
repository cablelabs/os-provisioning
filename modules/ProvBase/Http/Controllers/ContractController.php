<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Qos;

class ContractController extends \BaseModuleController {

	// TODO: temporary helper until we have a global config or billing module or field specific visibility
	private $billing = 1;

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		$r = $a = $b = $c = [];

		// label has to be the same like column in sql table
		$a = array(
			array('form_type' => 'text', 'name' => 'number', 'description' => 'ID', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'customer_number', 'description' => 'Customer number', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'contract_number', 'description' => 'Contract number', 'create' => '1'),
		//	array('form_type' => 'text', 'name' => 'number2', 'description' => 'Contract Number 2', 'options' => ['readonly']),
			array('form_type' => 'select', 'name' => 'salutation', 'description' => 'Salutation', 'value' => $model->getPossibleEnumValues('salutation')),
			array('form_type' => 'select', 'name' => 'academic_degree', 'description' => 'Academic degree', 'value' => $model->getPossibleEnumValues('academic_degree')),
			array('form_type' => 'text', 'name' => 'company', 'description' => 'Company'),
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
		);

		if ($this->billing)
			$b = array(
				array('form_type' => 'checkbox', 'name' => 'internet_access', 'description' => 'Internet Access', 'value' => '1', 'create' => '1'),
				array('form_type' => 'checkbox', 'name' => 'phonebook_entry', 'description' => 'Make phonebook entry', 'value' => '1', 'create' => '1'),
				array('form_type' => 'text', 'name' => 'contract_start', 'description' => 'Contract Start'),	
				array('form_type' => 'text', 'name' => 'contract_end', 'description' => 'Contract End', 'space' => '1'),

				array('form_type' => 'select', 'name' => 'qos_id', 'description' => 'QoS', 'create' => '1', 'value' => $model->html_list(Qos::all(), 'name')),
				array('form_type' => 'select', 'name' => 'next_qos_id', 'description' => 'QoS next month', 'value' => $model->html_list(Qos::all(), 'name')),
				array('form_type' => 'text', 'name' => 'void_id', 'description' => 'Phone ID'),
				array('form_type' => 'text', 'name' => 'next_voip_id', 'description' => 'Phone ID next month', 'space' => '1'),

				array('form_type' => 'text', 'name' => 'sepa_holder', 'description' => 'Bank Account Holder'),
				array('form_type' => 'text', 'name' => 'sepa_iban', 'description' => 'IBAN'),
				array('form_type' => 'text', 'name' => 'sepa_bic', 'description' => 'BIC'),
				array('form_type' => 'text', 'name' => 'sepa_institute', 'description' => 'Bank Institute'),
				array('form_type' => 'checkbox', 'name' => 'create_invoice', 'description' => 'Create Invoice', 'value' => '1', 'space' => '1'),
			);

		$c = array (
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),
		);

		return array_merge($a, $b, $c);
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
		else {
			return null;
		}
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
		return $provvoipenvia->get_jobs_for_view($contract, 'contract');
	}
}
