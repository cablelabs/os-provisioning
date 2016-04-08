<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Qos;
use Modules\BillingBase\Entities\Product;
use Modules\BillingBase\Entities\Item;
use Modules\BillingBase\Entities\CostCenter;

class ContractController extends \BaseModuleController {

	protected $relation_create_button = "Add";


	private function _data_tariff($model)
	{
		$h = $model->html_list(Product::where('type', '=', 'Internet')->get(), 'name');
		$h[0] = null;
		ksort($h);
		return $h;
	}

	private function _voip_tariff($model)
	{
		$voip_tariff_list = $model->html_list(Product::where('type', '=', 'Voip')->get(), 'name');
		$voip_tariff_list[0] = null;
		ksort($voip_tariff_list);
		return $voip_tariff_list;
	}

	private function _tv_tariff($model)
	{
		$tv_tariff_list = $model->html_list(Product::where('type', '=', 'TV')->get(), 'name');
		$tv_tariff_list[0] = null;
		ksort($tv_tariff_list);
		return $tv_tariff_list;
	}

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		$r = $a = $b = $c = [];
		$m = new \BaseModel;
	
		// dd(\Artisan::call('nms:accounting'));

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
		{
			$b = array(
				array('form_type' => 'checkbox', 'name' => 'network_access', 'description' => 'Internet Access', 'checked' => true, 'value' => '1', 'create' => '1'),
				array('form_type' => 'text', 'name' => 'contract_start', 'description' => 'Contract Start'), // TODO: create default 'value' => date("Y-m-d")	
				array('form_type' => 'text', 'name' => 'contract_end', 'description' => 'Contract End', 'space' => '1'),
				array('form_type' => 'select', 'name' => 'costcenter_id', 'description' => 'Cost Center', 'value' => $model->html_list(CostCenter::all(), 'name')),
				array('form_type' => 'checkbox', 'name' => 'create_invoice', 'description' => 'Create Invoice', 'value' => '1', 'space' => '1'),

				// array('form_type' => 'select', 'name' => 'price_id', 'description' => 'Data Tariff', 'create' => '1', 'value' => $this->_data_tariff($model)),
				// array('form_type' => 'select', 'name' => 'next_price_id', 'description' => 'Data Tariff next month', 'value' => $this->_data_tariff($model)),
				// array('form_type' => 'select', 'name' => 'voip_price_id', 'description' => 'Voip Tariff', 'value' => $this->_voip_tariff($model)),
				// array('form_type' => 'select', 'name' => 'next_voip_price_id', 'description' => 'Voip Tariff next month', 'value' => $this->_voip_tariff($model)),
				// array('form_type' => 'select', 'name' => 'tv_price_id', 'description' => 'TV Tariff', 'value' => $this->_tv_tariff($model)),
				// array('form_type' => 'select', 'name' => 'next_tv_price_id', 'description' => 'TV Tariff next month', 'value' => $this->_tv_tariff($model), 'space' => '1'),

				array('form_type' => 'text', 'name' => 'sepa_holder', 'description' => 'Bank Account Holder', 'options' => ['readonly']),
				array('form_type' => 'text', 'name' => 'sepa_iban', 'description' => 'IBAN', 'options' => ['readonly']),
				array('form_type' => 'text', 'name' => 'sepa_bic', 'description' => 'BIC', 'options' => ['readonly']),
				array('form_type' => 'text', 'name' => 'sepa_institute', 'description' => 'Bank Institute', 'options' => ['readonly']),
			);
		}
		else
		{
			$b = array(
				array('form_type' => 'select', 'name' => 'qos_id', 'description' => 'QoS', 'create' => '1', 'value' => $model->html_list(Qos::all(), 'name')),
				array('form_type' => 'select', 'name' => 'next_qos_id', 'description' => 'QoS next month', 'value' => $this->_qos_next_month($model)),
				array('form_type' => 'text', 'name' => 'voip_id', 'description' => 'Phone ID'),
				array('form_type' => 'text', 'name' => 'next_voip_id', 'description' => 'Phone ID next month', 'space' => '1'),
			);
		}

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

		// $products = Product::where('type', '=', 'device')->orWhere('type', '=', 'other')->orderBy('type')->get();
		$products = Product::orderBy('id')->get();

		return parent::edit($id)->with('products', $products);
	}

}
