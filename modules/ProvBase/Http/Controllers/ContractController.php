<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvVoip\Entities\PhoneTariff;

// TODO: @Nino Ryschawy: directly includes does not work if billing module is disables
use Modules\BillingBase\Entities\Product;
use Modules\BillingBase\Entities\Item;
use Modules\BillingBase\Entities\CostCenter;
use Modules\BillingBase\Entities\Salesman;

class ContractController extends \BaseController {


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
	public function view_form_fields($model = null)
	{
		if (!$model)
			$model = new Contract;

		$r = $a = $b = $c = $d = [];

		// label has to be the same like column in sql table
		$a = array(

			// basic data
			array('form_type' => 'text', 'name' => 'number', 'description' => $model->get_column_description('number'), 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'number2', 'description' => $model->get_column_description('number2'), 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'number3', 'description' => $model->get_column_description('number3'), 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'number4', 'description' => $model->get_column_description('number4'), 'options' => ['readonly'], 'space' => 1),
			array('form_type' => 'text', 'name' => 'company', 'description' => 'Company', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'department', 'description' => 'Department', 'create' => '1'),
			array('form_type' => 'select', 'name' => 'salutation', 'description' => 'Salutation', 'value' => $model->get_salutation_options(), 'create' => '1'),
			array('form_type' => 'select', 'name' => 'academic_degree', 'description' => 'Academic degree', 'value' => $model->get_academic_degree_options()),
			array('form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname', 'create' => '1', 'space' => '1'),
			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'house_number', 'description' => 'House number', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'zip', 'description' => 'Postcode', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'district', 'description' => 'District', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'phone', 'description' => 'Phone'),
			array('form_type' => 'text', 'name' => 'fax', 'description' => 'Fax'),
			array('form_type' => 'text', 'name' => 'email', 'description' => 'E-Mail Address'),
			array('form_type' => 'text', 'name' => 'birthday', 'description' => 'Birthday', 'create' => '1', 'space' => '1'),

			array('form_type' => 'checkbox', 'name' => 'network_access', 'description' => 'Internet Access', 'value' => '1', 'create' => '1', 'checked' => 1),
		);

		// TODO: replace with static command
		if ($model->voip_enabled) {

			$b = array(
				/* array('form_type' => 'text', 'name' => 'voip_contract_start', 'description' => 'VoIP Contract Start'), */
				/* array('form_type' => 'text', 'name' => 'voip_contract_end', 'description' => 'VoIP Contract End'), */
				array('form_type' => 'select', 'name' => 'purchase_tariff', 'description' => 'Purchase tariff', 'value' => PhoneTariff::get_purchase_tariffs()),

				array('form_type' => 'select', 'name' => 'voip_id', 'description' => 'Sale tariff', 'value' => PhoneTariff::get_sale_tariffs()),
				/* array('form_type' => 'text', 'name' => 'next_voip_id', 'description' => 'Phone ID next month', 'space' => '1'), */
			);
		}

		if (\PPModule::is_active('billingbase')) {

			$c = array(
				array('form_type' => 'text', 'name' => 'contract_start', 'description' => 'Contract Start'), // TODO: create default 'value' => date("Y-m-d")
				array('form_type' => 'text', 'name' => 'contract_end', 'description' => 'Contract End'),
				array('form_type' => 'checkbox', 'name' => 'create_invoice', 'description' => 'Create Invoice', 'value' => '1'),
				array('form_type' => 'select', 'name' => 'costcenter_id', 'description' => 'Cost Center', 'value' => $model->html_list(CostCenter::all(), 'name')),
				array('form_type' => 'select', 'name' => 'salesman_id', 'description' => 'Salesman', 'value' => $this->_salesmen(), 'space' => '1'),

				// NOTE: qos is required as hidden field to automatically create modem with correct contract qos class
				// TODO: @Nino Ryschawy: please review and test while merging ..
				array('form_type' => 'select', 'name' => 'qos_id', 'description' => 'QoS', 'create' => '1', 'value' => $model->html_list(Qos::all(), 'name'), 'hidden' => 1),
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
	 * Get all management jobs for Envia
	 *
	 * @author Patrick Reichel
	 * @param $model current phonenumber object
	 * @return array containing linktexts and URLs to perform actions against REST API
	 */
	public static function _get_envia_management_jobs($contract) {

		$provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

		// check if user has the right to perform actions against Envia API
		// if not: don't show any actions
		try {
			\App\Http\Controllers\BaseAuthController::auth_check('view', 'Modules\ProvVoipEnvia\Entities\ProvVoipEnvia');
		}
		catch (PermissionDeniedError $ex) {
			return null;
		}

		return $provvoipenvia->get_jobs_for_view($contract, 'contract');
	}


	/**
	 * Set contract Start date - TODO: move to default_input(), when it is executed in BaseController
	 */
	public function prepare_input($data)
	{
		$data['contract_start'] = $data['contract_start'] ? : date('Y-m-d');

		return parent::prepare_input($data);
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

		foreach ($this->view_form_fields(static::get_model_obj()) as $field) {
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



	/**
	 * Create and Download Connection Information

	 * @param type $filename download filename, replace '/' with '__' in URL context
	 * @return type response() - download box from browser
	 *
	 * @author Torsten Schmidt
	 */
	public function connection_info_download ($id)
	{
		// TODO: @Nino Ryschawy: create connection information under storage path
		//       and set $pdf to created pdf file (recursive under storage/apps)
		$filename = 'test.pdf';

		$pdf = response()->download(storage_path().'/app/'.$filename);

		return $pdf;
	}

}
