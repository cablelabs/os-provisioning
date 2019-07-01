<?php

namespace Modules\ProvBase\Http\Controllers;

use Bouncer;
use Session;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvBase\Entities\Contract;
use Modules\ProvVoip\Entities\PhoneTariff;

class ContractController extends \BaseController
{
    protected $relation_create_button = 'Add';

    /**
     * defines the formular fields for the edit and create view
     *
     * @return 	array
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new Contract;
        }

        $r = $a = $b = $c1 = $c2 = $d = [];

        // label has to be the same like column in sql table
        $a = [

            // basic data
            ['form_type' => 'text', 'name' => 'number', 'description' => $model->get_column_description('number'), 'help' => trans('helper.contract_number')],
            ['form_type' => 'text', 'name' => 'number2', 'description' => $model->get_column_description('number2'), 'options' => ['class' => 'collapse']],
            ['form_type' => 'text', 'name' => 'number3', 'description' => $model->get_column_description('number3'), 'help' => 'If left empty contract number will be used as customer number, too.', 'options' => ['class' => 'collapse']],
            ['form_type' => 'text', 'name' => 'number4', 'description' => $model->get_column_description('number4'), 'space' => 1, 'options' => ['class' => 'collapse']],
            ['form_type' => 'text', 'name' => 'company', 'description' => 'Company', 'create' => '1'],
            ['form_type' => 'text', 'name' => 'department', 'description' => 'Department', 'create' => '1'],
            ['form_type' => 'select', 'name' => 'salutation', 'description' => 'Salutation', 'value' => $model->get_salutation_options(), 'create' => '1'],
            ['form_type' => 'select', 'name' => 'academic_degree', 'description' => 'Academic Degree', 'value' => $model->get_academic_degree_options()],
            ['form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname', 'create' => '1'],
            ['form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname', 'create' => '1', 'space' => '1'],
            ['form_type' => 'text', 'name' => 'street', 'description' => 'Street', 'create' => '1', 'html' => "<div class=col-md-12 style='background-color:whitesmoke'>
				<div class='form-group row'>
					<label for=street class='col-md-4 control-label' style='margin-top: 10px;'>Street * and House Number *</label>
						<div class=col-md-5>
							<input class='form-control' name='street' type=text value='${model['street']}' id='street' style='background-color:whitesmoke'>
						</div>"],
            ['form_type' => 'text', 'name' => 'house_number', 'description' => 'House Number', 'create' => '1', 'html' => "<div class=col-md-2><input class='form-control' name='house_number' type=text value='".$model['house_number']."' id='house_number' style='background-color:whitesmoke'></div>
				</div></div>"],
            ['form_type' => 'text', 'name' => 'zip', 'description' => 'Postcode', 'create' => '1'],
            ['form_type' => 'text', 'name' => 'city', 'description' => 'City', 'create' => '1'],
            ['form_type' => 'text', 'name' => 'district', 'description' => 'District', 'create' => '1'],
            ['form_type' => 'text', 'name' => 'phone', 'description' => 'Phone'],
            ['form_type' => 'text', 'name' => 'fax', 'description' => 'Fax'],
            ['form_type' => 'text', 'name' => 'email', 'description' => 'E-Mail Address'],
            ['form_type' => 'text', 'name' => 'birthday', 'description' => 'Birthday', 'create' => '1', 'space' => '1'],
            ['form_type' => 'text', 'name' => 'contract_start', 'description' => 'Contract Start'], // TODO: create default 'value' => date("Y-m-d")
            ['form_type' => 'text', 'name' => 'contract_end', 'description' => 'Contract End'],

        ];

        if (! \Module::collections()->has('Ccc')) {
            unset($a[0]['help']);
        }

        if (\Module::collections()->has('BillingBase')) {
            $days = range(0, 28);
            $days[0] = null;

            $b = [
                    ['form_type' => 'checkbox', 'name' => 'has_telephony', 'description' => 'Has telephony', 'value' => '1', 'help' => trans('helper.has_telephony'), 'hidden' => 1],
                    ['form_type' => 'checkbox', 'name' => 'create_invoice', 'description' => 'Create Invoice', 'checked' => 1],
                    ['form_type' => 'select', 'name' => 'value_date', 'description' => 'Date of value', 'value' => $days, 'help' => trans('helper.contract.valueDate')],
                    ['form_type' => 'select', 'name' => 'costcenter_id', 'description' => 'Cost Center', 'value' => $model->html_list(\Modules\BillingBase\Entities\CostCenter::all(), 'name', true)],
                    // NOTE: qos is required as hidden field to automatically create modem with correct contract qos class
                    ['form_type' => 'text', 'name' => 'qos_id', 'description' => 'QoS', 'create' => '1', 'hidden' => 1],
                ];

            if (\Modules\BillingBase\Entities\BillingBase::first()->show_ags) {
                $b[] = ['form_type' => 'select', 'name' => 'contact', 'description' => 'Contact Persons', 'value' => \Modules\BillingBase\Entities\BillingBase::contactPersons()];
            }

            $b[] = ['form_type' => 'select', 'name' => 'salesman_id', 'description' => 'Salesman', 'value' => $model->html_list(\Modules\BillingBase\Entities\Salesman::all(), ['firstname', 'lastname'], true, ' - '), 'space' => '1'];
        } else {
            $qoss = Qos::all();

            $b = [
                ['form_type' => 'checkbox', 'name' => 'internet_access', 'description' => 'Internet Access', 'value' => '1', 'create' => '1', 'checked' => 1],
                ['form_type' => 'checkbox', 'name' => 'has_telephony', 'description' => 'Has telephony', 'help' => trans('helper.has_telephony')],
                ['form_type' => 'select', 'name' => 'qos_id', 'description' => 'QoS', 'create' => '1', 'value' => $model->html_list($qoss, 'name')],
                ['form_type' => 'select', 'name' => 'next_qos_id', 'description' => 'QoS next month', 'value' => $model->html_list($qoss, 'name', true)],
            ];

            if ($model->external_voip_enabled) {
                $purchase_tariffs = PhoneTariff::get_purchase_tariffs();
                $sales_tariffs = PhoneTariff::get_sale_tariffs();

                $b2 = [
                    ['form_type' => 'select', 'name' => 'purchase_tariff', 'description' => 'Purchase tariff', 'value' => $purchase_tariffs],
                    ['form_type' => 'select', 'name' => 'voip_id', 'description' => 'Sale tariff', 'value' => $sales_tariffs],
                    ['form_type' => 'text', 'name' => 'next_purchase_tariff', 'description' => 'Purchase tariff next month', 'value' => $purchase_tariffs],
                    ['form_type' => 'text', 'name' => 'next_voip_id', 'description' => 'Sales tariff next month', 'value' => $sales_tariffs, 'space' => '1'],
                ];

                $b = array_merge($b, $b2);
            }
        }

        $c = [
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
        ];

        return array_merge($a, $b, $c);
    }

    /**
     * Get all management jobs for envia TEL
     *
     * @author Patrick Reichel
     * @param $contract current contract object
     * @return array containing linktexts and URLs to perform actions against REST API
     */
    public static function _get_envia_management_jobs($contract)
    {
        $provvoipenvia = new \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia();

        // check if user has the right to perform actions against envia TEL API
        if (Bouncer::cannot('view', \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia::class)) {
            return;
        }

        return $provvoipenvia->get_jobs_for_view($contract, 'contract');
    }

    /**
     * Set contract Start date - TODO: move to default_input(), when it is executed in BaseController
     */
    public function prepare_input($data)
    {
        $data['contract_start'] = $data['contract_start'] ?: date('Y-m-d');

        // generate contract number
        if (! $data['number'] && \Module::collections()->has('BillingBase') && $data['costcenter_id']) {
            // generate contract number
            $num = \Modules\BillingBase\Entities\NumberRange::get_new_number('contract', $data['costcenter_id']);

            if ($num) {
                $data['number'] = $num;

                if (! Session::has('alert')) {
                    Session::forget('alert');
                }
            } else {
                // show default alert when there is a numberrange for costcenter but there are no more
                // free numbers and no more specific error message is already set
                $numberrange_exists = \Modules\BillingBase\Entities\NumberRange::where('type', '=', 'contract')
                    ->where('costcenter_id', $data['costcenter_id'])->count();

                if ($numberrange_exists && ! Session::has('alert')) {
                    session(['alert' => trans('messages.contract_numberrange_failure')]);
                }
            }
        }

        $data = parent::prepare_input($data);

        // set this to null if no value is given
        $nullable_fields = [
            'contract_start',
            'contract_end',
            'voip_contract_start',
            'voip_contract_end',
            'birthday',
            'value_date',
        ];
        $data = $this->_nullify_fields($data, $nullable_fields);

        return $data;
    }

    /**
     * Overwrite BaseController method => not required dates should be set to null if not set
     * Otherwise we get entries like 0000-00-00, which cause crashes on validation rules in case of update
     *
     * @author Patrick Reichel
     */
    protected function default_input($data)
    {
        $data = parent::default_input($data);

        $nullable_fields = [
            'contract_start',
            'contract_end',
            'voip_contract_start',
            'voip_contract_end',
        ];

        foreach ($this->view_form_fields(static::get_model_obj()) as $field) {
            if (array_key_exists($field['name'], $data)) {
                if (array_search($field['name'], $nullable_fields) !== false) {
                    if ($data[$field['name']] == '') {
                        $data[$field['name']] = null;
                    }
                }
            }
        }

        return $data;
    }
}
