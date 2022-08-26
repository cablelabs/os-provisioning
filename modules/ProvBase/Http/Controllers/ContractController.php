<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Modules\ProvBase\Http\Controllers;

use DB;
use Module;
use Bouncer;
use Session;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvBase\Entities\Contract;

class ContractController extends \BaseController
{
    // get functions for some address select options
    use \App\AddressFunctionsTrait;

    protected $relation_create_button = 'Add';

    /**
     * defines the formular fields for the edit and create view
     *
     * @return array
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new Contract;
        }

        // Compose related phonenumbers as readonly info field
        if (Module::collections()->has('ProvVoip')) {
            // Get some necessary relations by one DB query as first step to reduce further queries when accessing related models
            $modems = $model->modems()->with([
                'mtas:id,modem_id',
                'mtas.phonenumbers:id,mta_id,country_code,prefix_number,number',
            ])->get();

            $model->setRelation('modems', $modems);
            $pns = [];

            foreach ($modems as $modem) {
                foreach ($modem->related_phonenumbers() as $pn) {
                    $pns[] = $pn->asString();
                }
            }

            $model->related_phonenrs = implode(', ', $pns);
        }

        $r = $a = $b = $c1 = $c2 = $d = [];

        $selectPropertyMgmt = [];
        if (Module::collections()->has('PropertyManagement')) {
            $selectPropertyMgmt = ['select' => 'noApartment'];
        }

        // label has to be the same like column in sql table
        $a = [
            // basic data
            ['form_type' => 'text', 'name' => 'number', 'description' => $model->get_column_description('number'), 'help' => trans('helper.contract_number')],
            ['form_type' => 'text', 'name' => 'number2', 'description' => $model->get_column_description('number2'), 'options' => ['class' => 'collapse']],
            ['form_type' => 'text', 'name' => 'number3', 'description' => $model->get_column_description('number3'), 'help' => 'If left empty contract number will be used as customer number, too.', 'options' => ['class' => 'collapse']],
            ['form_type' => 'text', 'name' => 'number4', 'description' => $model->get_column_description('number4'), 'space' => 1, 'options' => ['class' => 'collapse']],
            // 'create' makes this field a hidden input field in Modem create form - so the company, etc. will be already set from contract when the user wants to create a new modem
            ['form_type' => 'text', 'name' => 'company', 'description' => 'Company', 'create' => ['Modem']],
            ['form_type' => 'text', 'name' => 'department', 'description' => 'Department', 'create' => ['Modem']],
            ['form_type' => 'select', 'name' => 'salutation', 'description' => 'Salutation', 'value' => $model->getSalutationOptions(), 'create' => ['Modem']],
            ['form_type' => 'select', 'name' => 'academic_degree', 'description' => 'Academic Degree', 'value' => $model->getAcademicDegreeOptions()],
            ['form_type' => 'text', 'name' => 'firstname', 'description' => 'Firstname', 'create' => ['Modem']],
            ['form_type' => 'text', 'name' => 'lastname', 'description' => 'Lastname', 'create' => ['Modem'], 'space' => '1'],

            array_merge(['form_type' => 'text', 'name' => 'street', 'description' => 'Street', 'create' => ['Modem'], 'autocomplete' => []], $selectPropertyMgmt),
            array_merge(['form_type' => 'text', 'name' => 'house_number', 'description' => 'House Number', 'create' => ['Modem']], $selectPropertyMgmt),
            array_merge(['form_type' => 'text', 'name' => 'zip', 'description' => 'Postcode', 'create' => ['Modem'], 'autocomplete' => []], $selectPropertyMgmt),
            array_merge(['form_type' => 'text', 'name' => 'city', 'description' => 'City', 'create' => ['Modem'], 'autocomplete' => []], $selectPropertyMgmt),
            array_merge(['form_type' => 'text', 'name' => 'district', 'description' => 'District', 'create' => ['Modem'], 'autocomplete' => []], $selectPropertyMgmt),
        ];

        if (! Module::collections()->has('Ccc')) {
            unset($a[0]['help']);
        }

        if (Module::collections()->has('PropertyManagement')) {
            $a[] = ['form_type' => 'select', 'name' => 'apartment_id', 'description' => 'Apartment', 'hidden' => 0,
                'value' => $this->setupSelect2Field($model, 'Apartment'),
                'options' => ['class' => 'select2-ajax', 'data-allow-clear' => 'true',
                    'ajax-route' => route('Apartment.select2', ['relation' => 'apartments']), ],
            ];
        } else {
            $a[] = ['form_type' => 'text', 'name' => 'apartment_nr', 'description' => 'Apartment number'];
        }

        $a[] = ['form_type' => 'text', 'name' => 'additional', 'description' => 'Additional info', 'create' => ['Contract'], 'autocomplete' => [], 'space' => 1];

        $b1[] = ['form_type' => 'text', 'name' => 'phone', 'description' => 'Phone'];

        if (Module::collections()->has('ProvVoip')) {
            $b1[] = ['form_type' => 'text', 'name' => 'related_phonenrs', 'description' => trans('provvoip::view.contractRelatedPns'), 'options' => ['readonly']];
        }

        foreach ($model::GROUNDS_FOR_DISMISSAL as $key => $reason) {
            $reasons[$reason] = trans("view.contract.groundsForDismissal.$reason");
        }

        $b2 = [
            ['form_type' => 'text', 'name' => 'fax', 'description' => 'Fax'],
            ['form_type' => 'text', 'name' => 'email', 'description' => 'E-Mail Address'],
        ];

        if (Module::collections()->has('Ccc') && Module::collections()->has('BillingBase') && $model->cccUser) {
            $model->newsletter = $model->cccUser->newsletter;
            $b2[] = ['form_type' => 'checkbox', 'name' => 'newsletter', 'description' => trans('messages.receiveNewsletters')];
        }

        $b3 = [
            ['form_type' => 'date', 'name' => 'birthday', 'description' => 'Birthday', 'create' => ['Modem'], 'space' => '1'],
            ['form_type' => 'date', 'name' => 'contract_start', 'description' => 'Contract Start'],
            ['form_type' => 'date', 'name' => 'contract_end', 'description' => 'Contract End', 'space' => 1],
        ];

        if (Module::collections()->has('BillingBase')) {
            $days = range(0, 28);
            $days[0] = null;

            $c = [
                ['form_type' => 'checkbox', 'name' => 'has_telephony', 'description' => 'Has telephony', 'help' => trans('helper.has_telephony'), 'hidden' => 1],
                ['form_type' => 'checkbox', 'name' => 'create_invoice', 'description' => 'Create Invoice', 'checked' => 1],
                ['form_type' => 'select', 'name' => 'value_date', 'description' => 'Date of value', 'value' => $days, 'help' => trans('helper.contract.valueDate')],
                ['form_type' => 'select', 'name' => 'costcenter_id', 'description' => 'Cost Center', 'value' => selectList('costcenter', 'name', true)],
                // NOTE: qos is required as hidden field to automatically create modem with correct contract qos class
                ['form_type' => 'text', 'name' => 'qos_id', 'description' => 'QoS', 'create' => ['Modem'], 'hidden' => 1],
            ];

            $c[] = ['form_type' => 'select', 'name' => 'salesman_id', 'description' => 'Salesman', 'value' => selectList('salesman', ['firstname', 'lastname'], true, ' - ')];
        } else {
            $qoss = Qos::all();

            $c = [
                ['form_type' => 'checkbox', 'name' => 'internet_access', 'description' => 'Internet Access', 'value' => '1', 'create' => ['Modem'], 'checked' => 1],
                ['form_type' => 'checkbox', 'name' => 'has_telephony', 'description' => 'Has telephony', 'help' => trans('helper.has_telephony')],
                ['form_type' => 'select', 'name' => 'qos_id', 'description' => 'QoS', 'create' => ['Modem'], 'value' => $model->html_list($qoss, 'name')],
                ['form_type' => 'select', 'name' => 'next_qos_id', 'description' => 'QoS next month', 'value' => $model->html_list($qoss, 'name', true)],
            ];

            if (\Module::collections()->has('ProvVoipEnvia')) {
                $purchase_tariffs = \Modules\ProvVoip\Entities\PhoneTariff::get_purchase_tariffs();
                $sales_tariffs = \Modules\ProvVoip\Entities\PhoneTariff::get_sale_tariffs();

                $c2 = [
                    ['form_type' => 'select', 'name' => 'purchase_tariff', 'description' => 'Purchase tariff', 'value' => $purchase_tariffs],
                    ['form_type' => 'select', 'name' => 'voip_id', 'description' => 'Sale tariff', 'value' => $sales_tariffs],
                    ['form_type' => 'text', 'name' => 'next_purchase_tariff', 'description' => 'Purchase tariff next month', 'value' => $purchase_tariffs],
                    ['form_type' => 'text', 'name' => 'next_voip_id', 'description' => 'Sales tariff next month', 'value' => $sales_tariffs],
                ];

                $c = array_merge($c, $c2);
            }
        }

        if (Module::collections()->has('PropertyManagement')) {
            $c[] = ['form_type' => 'checkbox', 'name' => 'group_contract', 'description' => 'Group Contract', 'space' => 1];
            $c[] = ['form_type' => 'select', 'name' => 'contact_id', 'description' => 'Contact',
                'value' => $this->setupSelect2Field($model, 'Contact'),
                'options' => ['class' => 'select2-ajax', 'data-allow-clear' => 'true', 'ajax-route' => route('Contact.select2', ['relation' => 'contacts'])],
            ];
        } else {
            $c[array_key_last($c)]['space'] = 1;
        }

        if (\Module::collections()->has('BillingBase') && cache('billingBase')->show_ags) {
            $c[] = ['form_type' => 'select', 'name' => 'contact', 'description' => 'Contact Persons', 'value' => \Modules\BillingBase\Entities\BillingBase::contactPersons()];
        }

        $d = [
            ['form_type' => 'select', 'name' => 'ground_for_dismissal', 'description' => trans('view.contract.groundForDismissal'),
                'value' => array_merge([null => null], $reasons), ],
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
        ];

        return array_merge($a, $b1, $b2, $b3, $c, $d);
    }

    /**
     * Get all management jobs for envia TEL
     *
     * @author Patrick Reichel
     *
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
        if (! $data['number'] && Module::collections()->has('BillingBase') && $data['costcenter_id']) {
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

                if ($numberrange_exists) {
                    if (! Session::has('tmp_error_above_form')) {
                        Session::push('tmp_error_above_form', trans('messages.contract.numberrange.failure'));
                    }
                } else {
                    Session::push('tmp_error_above_form', trans('messages.contract.numberrange.missing'));
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

    public function prepare_rules($rules, $data)
    {
        foreach ($rules as $name => $rule) {
            $rules[$name] = str_replace('placeholder_salutations_person', implode(',', $this->getSalutationOptionsPerson()), $rules[$name]);
            $rules[$name] = str_replace('placeholder_salutations_institution', implode(',', $this->getSalutationOptionsInstitution()), $rules[$name]);
        }

        return parent::prepare_rules($rules, $data);
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

    /**
     * Show tabs in Contract edit page.
     *
     * @author Roy Schneider
     *
     * @param Modules\ProvBase\Entities\Contract
     * @return array
     */
    protected function editTabs($contract)
    {
        $defaultTabs = parent::editTabs($contract);
        unset($defaultTabs[0]);

        return $defaultTabs;
    }
}
