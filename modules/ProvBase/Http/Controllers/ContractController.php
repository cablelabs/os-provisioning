<?php

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
     * @return 	array
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new Contract;
        }

        // Compose related phonenumbers as readonly info field
        if (Module::collections()->has('ProvVoip')) {
            // Get some necessary relations by one DB query as first step to reduce further queries when accessing related models
            $modems = $model->modems()->with('mtas.phonenumbers')->get();
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
            $hasModems = $model->modems->count() ? true : false;
            $selectPropertyMgmt = ! $hasModems ? ['select' => 'noApartment'] : [];

            // Only group Contracts are assigned to a Contact
            $contacts = DB::table('contact')->where('administration', 1)->whereNull('deleted_at')->get();
            $contactList[null] = null;
            foreach ($contacts as $contact) {
                $contactList[$contact->id] = \Modules\PropertyManagement\Entities\Contact::labelFromData($contact);
            }
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
            // array_merge(['form_type' => 'text', 'name' => 'street', 'description' => 'Street', 'create' => ['Modem'], 'autocomplete' => [], 'html' => "<div class=col-md-12 style='background-color:whitesmoke'>
            //     <div class='form-group row'>
            //         <label for=street class='col-md-4 control-label' style='margin-top: 10px;'>Street * and House Number *</label>
            //             <div class=col-md-5>
            //                 <input class='form-control' name='street' type=text value='${model['street']}' id='street' style='background-color:whitesmoke'>
            //             </div>"], $selectPropertyMgmt),
            // array_merge(['form_type' => 'text', 'name' => 'house_number', 'description' => 'House Number', 'create' => ['Modem'], 'html' => "<div class=col-md-2><input class='form-control' name='house_number' type=text value='".$model['house_number']."' id='house_number' style='background-color:whitesmoke'></div>
            //     </div></div>"], $selectPropertyMgmt),
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
            if (! $hasModems) {
                $a[] = ['form_type' => 'select', 'name' => 'apartment_id', 'value' => $model->getSelectableApartments(), 'select' => 'noContact',  'description' => 'Apartment', 'hidden' => 0];
                $a[] = ['form_type' => 'select', 'name' => 'contact_id', 'value' => $contactList, 'select' => 'noApartment', 'description' => 'Contact', 'hidden' => 0];
            } else {
                $a[14]['space'] = 1;
            }
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
            ['form_type' => 'date', 'name' => 'birthday', 'description' => 'Birthday', 'create' => ['Modem'], 'space' => '1'],
            ['form_type' => 'date', 'name' => 'contract_start', 'description' => 'Contract Start'],
            ['form_type' => 'date', 'name' => 'contract_end', 'description' => 'Contract End'],
            ['form_type' => 'select', 'name' => 'ground_for_dismissal', 'description' => trans('view.contract.groundForDismissal'),
                'value' => array_merge([null => null], $reasons), ],
        ];

        if (Module::collections()->has('BillingBase')) {
            $days = range(0, 28);
            $days[0] = null;

            $c = [
                ['form_type' => 'checkbox', 'name' => 'has_telephony', 'description' => 'Has telephony', 'value' => '1', 'help' => trans('helper.has_telephony'), 'hidden' => 1],
                ['form_type' => 'checkbox', 'name' => 'create_invoice', 'description' => 'Create Invoice', 'checked' => 1],
                ['form_type' => 'select', 'name' => 'value_date', 'description' => 'Date of value', 'value' => $days, 'help' => trans('helper.contract.valueDate')],
                ['form_type' => 'select', 'name' => 'costcenter_id', 'description' => 'Cost Center', 'value' => selectList('costcenter', 'name', true)],
                // NOTE: qos is required as hidden field to automatically create modem with correct contract qos class
                ['form_type' => 'text', 'name' => 'qos_id', 'description' => 'QoS', 'create' => ['Modem'], 'hidden' => 1],
            ];

            if (\Modules\BillingBase\Entities\BillingBase::first()->show_ags) {
                $c[] = ['form_type' => 'select', 'name' => 'contact', 'description' => 'Contact Persons', 'value' => \Modules\BillingBase\Entities\BillingBase::contactPersons()];
            }

            $c[] = ['form_type' => 'select', 'name' => 'salesman_id', 'description' => 'Salesman', 'value' => selectList('salesman', ['firstname', 'lastname'], true, ' - '), 'space' => '1'];
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
                    ['form_type' => 'text', 'name' => 'next_voip_id', 'description' => 'Sales tariff next month', 'value' => $sales_tariffs, 'space' => '1'],
                ];

                $c = array_merge($c, $c2);
            }
        }

        $d = [
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
        ];

        return array_merge($a, $b1, $b2, $c, $d);
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
        if (\Module::collections()->has('PropertyManagement')) {
            // Only group contracts without modems can belong to a Contact directly - with CMs contact_id must be null
            if (isset($data['contact_id']) && $data['contact_id'] && isset($data['id'])) {
                $modems = Contract::join('modem', 'modem.contract_id', 'contract.id')
                    ->where('contract.id', $data['id'])
                    ->whereNull('modem.deleted_at')
                    ->count();

                if ($modems) {
                    $rules['contact_id'] = 'empty';
                }
            }
        }

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
}
