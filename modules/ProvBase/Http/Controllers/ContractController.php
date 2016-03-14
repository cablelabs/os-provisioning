<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Qos;
use Modules\BillingBase\Entities\Price;
use Modules\BillingBase\Entities\Item;

class ContractController extends \BaseModuleController {

	protected $relation_create_button = "Add";

	// TODO: temporary helper until we have a global config or billing module or field specific visibility
	// private $billing = 1;


	private function _data_tariff($model)
	{
		// $h = $model->html_list(Qos::all(), 'name');
		$h = $model->html_list(Price::where('type', '=', 'Internet')->get(), 'name');
		$h[0] = null;
		asort($h);
		return $h;
	}

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		$r = $a = $b = $c = [];
		$m = new \BaseModel;

		// label has to be the same like column in sql table
		$a = array(
			array('form_type' => 'text', 'name' => 'number', 'description' => 'Contract Number', 'options' => ['readonly']),
		//	array('form_type' => 'text', 'name' => 'number2', 'description' => 'Contract Number 2', 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'salutation', 'description' => 'Salutation'),
			array('form_type' => 'text', 'name' => 'company', 'description' => 'Company'),
			array('form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname', 'create' => '1', 'space' => '1'),

			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'zip', 'description' => 'Postcode', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City', 'create' => '1'),

			array('form_type' => 'text', 'name' => 'phone', 'description' => 'Phone'),
			array('form_type' => 'text', 'name' => 'fax', 'description' => 'Fax'),
			array('form_type' => 'text', 'name' => 'email', 'description' => 'E-Mail Address'),
			array('form_type' => 'text', 'name' => 'birthday', 'description' => 'Birthday', 'space' => '1'),
		);

		if ($m->module_is_active('Billingbase'))
			$b = array(
				array('form_type' => 'checkbox', 'name' => 'network_access', 'description' => 'Internet Access', 'checked' => true, 'value' => '1', 'create' => '1'),
				array('form_type' => 'text', 'name' => 'contract_start', 'description' => 'Contract Start'), // TODO: create default 'value' => date("Y-m-d")	
				array('form_type' => 'text', 'name' => 'contract_end', 'description' => 'Contract End', 'space' => '1'),

				array('form_type' => 'select', 'name' => 'price_id', 'description' => 'Data Tariff', 'create' => '1', 'value' => $this->_data_tariff($model)),
				array('form_type' => 'select', 'name' => 'next_price_id', 'description' => 'Data Tariff next month', 'value' => $this->_data_tariff($model)),
				array('form_type' => 'select', 'name' => 'voip_tariff', 'description' => 'Voip Tariff', 'value' => Price::getPossibleEnumValues('voip_tariff')),
				array('form_type' => 'select', 'name' => 'next_voip_tariff', 'description' => 'Voip Tariff next month', 'value' => Price::getPossibleEnumValues('voip_tariff'), 'space' => '1'),

				array('form_type' => 'select', 'name' => 'cost_center', 'description' => 'Cost Center', 'value' => Contract::getPossibleEnumValues('cost_center')),
				array('form_type' => 'text', 'name' => 'sepa_holder', 'description' => 'Bank Account Holder', 'options' => ['readonly']),
				array('form_type' => 'text', 'name' => 'sepa_iban', 'description' => 'IBAN', 'options' => ['readonly']),
				array('form_type' => 'text', 'name' => 'sepa_bic', 'description' => 'BIC', 'options' => ['readonly']),
				array('form_type' => 'text', 'name' => 'sepa_institute', 'description' => 'Bank Institute', 'options' => ['readonly']),
				array('form_type' => 'checkbox', 'name' => 'create_invoice', 'description' => 'Create Invoice', 'value' => '1', 'space' => '1'),
			);

		$c = array (
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),
		);

		return array_merge($a, $b, $c);
	}


	public function edit($id)
	{
		$bm = new \BaseModel;
		if (!$bm->module_is_active('Billingbase'))
			return parent::edit($id);

		$price_entries = Price::where('type', '=', 'device')->orWhere('type', '=', 'other')->orderBy('type')->get();

		return parent::edit($id)->with('price_entries', $price_entries);
	}

}
