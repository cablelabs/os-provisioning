<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\{Contract, Qos};
use Modules\ProvVoip\Entities\PhoneTariff;

class ContractController extends \BaseController {


	protected $relation_create_button = "Add";


    /**
     * defines the formular fields for the edit and create view
     *
     * @return 	array
     */
	public function view_form_fields($model = null)
	{
		if (!$model)
			$model = new Contract;

		$r = $a = $b = $c1 = $c2 = $d = [];

		// label has to be the same like column in sql table
		$a = array(

			// basic data
			array('form_type' => 'text', 'name' => 'number', 'description' => $model->get_column_description('number'), 'help' => trans('helper.contract_number')),
			array('form_type' => 'text', 'name' => 'number2', 'description' => $model->get_column_description('number2'), 'options' => ['class' => 'collapse']),
			array('form_type' => 'text', 'name' => 'number3', 'description' => $model->get_column_description('number3'), 'help' => 'If left empty contract number will be used as customer number, too.', 'options' => ['class' => 'collapse']),
			array('form_type' => 'text', 'name' => 'number4', 'description' => $model->get_column_description('number4'), 'space' => 1, 'options' => ['class' => 'collapse']),
			array('form_type' => 'text', 'name' => 'company', 'description' => 'Company', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'department', 'description' => 'Department', 'create' => '1'),
			array('form_type' => 'select', 'name' => 'salutation', 'description' => 'Salutation', 'value' => $model->get_salutation_options(), 'create' => '1'),
			array('form_type' => 'select', 'name' => 'academic_degree', 'description' => 'Academic Degree', 'value' => $model->get_academic_degree_options()),
			array('form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname', 'create' => '1', 'space' => '1'),
			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street', 'create' => '1', 'html' =>
				"<div class=col-md-12 style='background-color:whitesmoke'>
				<div class='form-group row'>
					<label for=street class='col-md-4 control-label' style='margin-top: 10px;'>Street * and House Number</label>
						<div class=col-md-5>
							<input class='form-control' name='street' type=text value='".$model['street']."' id='street' style='background-color:whitesmoke'>
						</div>"),
			array('form_type' => 'text', 'name' => 'house_number', 'description' => 'House Number', 'create' => '1', 'html' =>
				"<div class=col-md-2><input class='form-control' name='house_number' type=text value='".$model['house_number']."' id='house_number' style='background-color:whitesmoke'></div>
				</div></div>"),
			array('form_type' => 'text', 'name' => 'zip', 'description' => 'Postcode', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'district', 'description' => 'District', 'create' => '1'),
			array('form_type' => 'text', 'name' => 'phone', 'description' => 'Phone'),
			array('form_type' => 'text', 'name' => 'fax', 'description' => 'Fax'),
			array('form_type' => 'text', 'name' => 'email', 'description' => 'E-Mail Address'),
			array('form_type' => 'text', 'name' => 'birthday', 'description' => 'Birthday', 'create' => '1', 'space' => '1'),

		);

		if (!\Module::collections()->has('Ccc'))
			unset($a[0]['help']);

		if ($model->voip_enabled && !\Module::collections()->has('BillingBase')) {

			$b = array(
				/* array('form_type' => 'text', 'name' => 'voip_contract_start', 'description' => 'VoIP Contract Start'), */
				/* array('form_type' => 'text', 'name' => 'voip_contract_end', 'description' => 'VoIP Contract End'), */
				array('form_type' => 'select', 'name' => 'purchase_tariff', 'description' => 'Purchase tariff', 'value' => PhoneTariff::get_purchase_tariffs()),
				array('form_type' => 'select', 'name' => 'voip_id', 'description' => 'Sale tariff', 'value' => PhoneTariff::get_sale_tariffs()),
				/* array('form_type' => 'text', 'name' => 'next_voip_id', 'description' => 'Phone ID next month', 'space' => '1'), */
				array('form_type' => 'checkbox', 'name' => 'telephony_only', 'description' => 'Telephony only', 'value' => '1', 'help' => 'Customer has only subscribed telephony, i.e. no internet access')
			);
		}

		$c1 = array(
				array('form_type' => 'text', 'name' => 'contract_start', 'description' => 'Contract Start'), // TODO: create default 'value' => date("Y-m-d")
				array('form_type' => 'text', 'name' => 'contract_end', 'description' => 'Contract End'),
			);

		if (\Module::collections()->has('BillingBase')) {

			$c2 = array(
				array('form_type' => 'checkbox', 'name' => 'create_invoice', 'description' => 'Create Invoice', 'value' => '1'),
				array('form_type' => 'select', 'name' => 'costcenter_id', 'description' => 'Cost Center', 'value' => $model->html_list(\Modules\BillingBase\Entities\CostCenter::all(), 'name', true)),
				array('form_type' => 'select', 'name' => 'salesman_id', 'description' => 'Salesman', 'value' => $model->html_list(\Modules\BillingBase\Entities\Salesman::all(), ['firstname', 'lastname'], true, ' - '), 'space' => '1'),
				// NOTE: qos is required as hidden field to automatically create modem with correct contract qos class
				array('form_type' => 'text', 'name' => 'qos_id', 'description' => 'QoS', 'create' => '1', 'hidden' => 1),
				array('form_type' => 'checkbox', 'name' => 'telephony_only', 'description' => 'Telephony only', 'value' => '1', 'help' => 'Customer has only subscribed telephony, i.e. no internet access', 'hidden' => 1)
			);

			if (\Modules\BillingBase\Entities\BillingBase::first()->show_ags)
				$c2[] = array('form_type' => 'select', 'name' => 'contact', 'description' => 'Contact Persons', 'value' => \Modules\BillingBase\Entities\BillingBase::contactPersons());
		}
		else
		{
			$qoss = Qos::all();

			$c2 = array(
				array('form_type' => 'checkbox', 'name' => 'network_access', 'description' => 'Internet Access', 'value' => '1', 'create' => '1', 'checked' => 1),
				array('form_type' => 'select', 'name' => 'qos_id', 'description' => 'QoS', 'create' => '1', 'value' => $model->html_list($qoss, 'name')),
				array('form_type' => 'select', 'name' => 'next_qos_id', 'description' => 'QoS next month', 'value' => $model->html_list($qoss, 'name', true)),
				array('form_type' => 'text', 'name' => 'voip_id', 'description' => 'Phone ID'),
				array('form_type' => 'text', 'name' => 'next_voip_id', 'description' => 'Phone ID next month', 'space' => '1'),
			);
		}

		$d = array(
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'),
		);

		return array_merge($a, $b, $c1, $c2, $d);
	}


	/**
	 * Get all management jobs for envia TEL
	 *
	 * @author Patrick Reichel
	 * @param $contract current contract object
	 * @return array containing linktexts and URLs to perform actions against REST API
	 */
	public static function _get_envia_management_jobs($contract) {

		$provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

		// check if user has the right to perform actions against envia TEL API
		// if not: don't show any actions
		try {
			\App\Http\Controllers\BaseAuthController::auth_check('view', 'Modules\ProvVoipEnvia\Entities\ProvVoipEnvia');
		}
		catch (AuthException $ex) {
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

		// generate contract number
		if (!$data['number'] && \Module::collections()->has('BillingBase') && $data['costcenter_id'])
		{
			// generate contract number
			$num = \Modules\BillingBase\Entities\NumberRange::get_new_number('contract', $data['costcenter_id']);

			if ($num)
				$data['number'] = $num;
			else if (\Modules\BillingBase\Entities\NumberRange::where('type', '=', 'contract')->where('costcenter_id', $data['costcenter_id'])->count()) {
				// show alert when there is a numberrange for costcenter but there are no more free numbers
				session(['alert' => \App\Http\Controllers\BaseViewController::translate_view('Failure','Contract_Numberrange')]);
			}
		}

		$data = parent::prepare_input($data);

		// set this to null if no value is given
		$nullable_fields = array(
			'contract_start',
			'contract_end',
			'voip_contract_start',
			'voip_contract_end',
		);
		$data = $this->_nullify_fields($data, $nullable_fields);
		return $data;
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
}

