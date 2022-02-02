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

namespace Modules\Dreamfiber\Entities;

class DfSubscription extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'dfsubscription';

    protected $fillable = [];

    // Helper to map dfsubscription and contract fields
    // while content may differ they are deeply related
    public $contractFieldMap = [
        'service_name' => '',
        'service_type' => '',
        'contact_no' => '',
        'contact_first_name' => 'firstname',
        'contact_last_name' => 'lastname',
        'contact_company_name' => 'company',
        'contact_street' => 'street',
        'contact_street_no' => 'house_number',
        'contact_postal_code' => 'zip',
        'contact_city' => 'city',
        'contact_country' => 'country_code',
        'contact_phone' => 'phone',
        'contact_email' => 'email',
        'contact_notes' => '',
        'subscription_id' => '',
        'subscription_end_point_id' => 'sep_id',
        'sf_sla' => '',
        'status' => '',
        'wishdate' => '',
        'switchdate' => '',
        'modificationdate' => '',
        'l1_handover_equipment_name' => 'omdf_id',
        'l1_handover_equipment_rack' => '',
        'l1_handover_equipment_slot' => '',
        'l1_handover_equipment_port' => '',
        'l1_breakout_cable' => '',
        'l1_breakout_fiber' => '',
        'alau_order_ref' => '',
        'note' => '',
    ];

    /**
     * View Stuff
     */

    // Name of View
    public static function view_headline()
    {
        return 'Dreamfiber subscription';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-pencil-square"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        return ['table' => $this->table,
            'index_header' => [
                $this->table.'.id',
                $this->table.'.subscription_id',
                $this->table.'.wishdate',
                $this->table.'.switchdate',
                $this->table.'.status',
                $this->table.'.subscription_end_point_id',
                $this->table.'.contact_postal_code',
                $this->table.'.contact_city',
                $this->table.'.contact_street',
                $this->table.'.contact_street_no',
            ],
            'header' => $this->label(),
            'bsclass' => $this->get_bsclass(),
            'edit' => [],
            'eager_loading' => ['contract'],
        ];
    }

    public function get_bsclass()
    {
        return match ($this->status) {
            'ACCEPTED-C' => 'warning',
            'ACCEPTED-O' => 'warning',
            'CEASE' => 'warning',
            'COMMISSION' => 'warning',
            'DECOMMISSION' => 'warning',
            'DELETED' => 'info',
            'EXCEPTION-PC' => 'danger',
            'EXCEPTION-PD' => 'danger',
            'NEW' => 'warning',
            'REJECTED-C' => 'danger',
            'REJECTED-O' => 'danger',
            'RUNNING' => 'success',
            'TERMINATED' => 'info',
            default => 'info',
        };
    }

    public function label()
    {
        return "$this->subscription_id ($this->status – $this->switchdate)";
    }

    public function view_belongs_to()
    {
        return $this->contract;
    }

    public function view_has_many()
    {
        $this->setRelation('subscriptionevents', $this->dfsubscriptionevents()->get());
        $ret['Edit']['DfSubscriptionEvent']['class'] = 'DfSubscriptionEvent';
        $ret['Edit']['DfSubscriptionEvent']['relation'] = $this->dfsubscriptionevents;
        $ret['Edit']['DfSubscriptionEvent']['options']['hide_create_button'] = 1;
        $ret['Edit']['DfSubscriptionEvent']['options']['hide_delete_button'] = 1;

        $ret['Edit']['Dreamfiber']['view']['view'] = 'dreamfiber::Dreamfiber.actions';
        $ret['Edit']['Dreamfiber']['view']['vars']['apiActions'] = $this->viewApiActions();

        return $ret;
    }

    /**
     * Provide a view usable array of possible API jobs
     *
     * @return array for use in view
     *
     * @author Patrick Reichel
     */
    public function viewApiActions()
    {
        $actions = $this->possibleApiActions();

        return $this->prepareApiActionsForView($actions);
    }

    /**
     * Create the view data for the possible API actions.
     *
     * @param array of possible actions
     * @return array with view data
     *
     * @author Patrick Reichel
     */
    public function prepareApiActionsForView($actions)
    {
        $ret = [];
        foreach ($actions as $action) {
            $url = \URL::route('Dreamfiber.action', ['type' => $action, 'subscription_id' => $this->id]);
            $data = [
                'url' => $url,
                'linktext' => trans($action),
            ];
            $ret[] = $data;
        }

        return $ret;
    }

    /**
     * Returns all possible API actions depending of the current status
     *
     * @return array containing possible actions
     *
     * @author Patrick Reichel
     */
    public function possibleApiActions()
    {
        $actions = [
            'NEW' => ['cancelSubscription'],
            'ACCEPTED-O' => ['cancelSubscription'],
            'RUNNING' => ['terminateSubscription', 'updateSubscription'],
            'CEASE' => ['cancelSubscription'],
            'ACCEPTED-C' => ['cancelSubscription'],
            'DECOMMISSION' => ['cancelSubscription'],
            'terminateSubscriptionD' => ['createSubscription'],
        ];

        // freshly createSubscriptiond in local system – and still not known to Dreamfiber
        if (in_array($this->status, ['', 'n/a'])) {
            return ['createSubscription'];
        }

        // all other statuses have been updateSubscriptiond via Dreamfiber API – so we should be able to getSubscriptionInformation
        // informations about
        if (! array_key_exists($this->status, $actions)) {
            return ['getSubscriptionInformation'];
        }

        $ret = $actions[$this->status];
        array_unshift($ret, 'getSubscriptionInformation');

        return $ret;
    }

    public function contract()
    {
        return $this->belongsTo(\Modules\ProvBase\Entities\Contract::class, 'contract_id');
    }

    public function dfsubscriptionevents()
    {
        return $this->hasMany(DfSubscriptionEvent::class, 'dfsubscription_id');
    }

    /**
     * Fills the current dfSubscription with date form API.
     *
     * @param $data Raw data from Dreamfiber API
     * @return true if success, else false
     *
     * @author Patrick Reichel
     */
    public function fillModelFromApi($data)
    {
        //  check if we have to determine the contract for this subscription
        if (! $this->contract_id) {
            $id = \DB::table('contract')->where('sep_id', '=', $data->subscriptionId)->pluck('id')->first();
            if (is_null($id)) {
                $this->logAndPrint('No contract with subscription end point ID “'.$this->subscription_end_point_id.'” – ignoring subscription with ID “'.$data->subscriptionId.'”', 'warning');

                return false;
            }
            $this->contract_id = \DB::table('contract')->where('sep_id', '=', $this->subscription_end_point_id)->pluck('id')->first();
        }

        // fill the model
        $this->service_name = $data->service->serviceName ?? null;
        $this->service_type = $data->service->service_type ?? null;
        $this->contact_no = $data->contact->contactNo ?? null;
        $this->contact_first_name = $data->contact->contactFirstName ?? null;
        $this->contact_last_name = $data->contact->contactLastName ?? null;
        $this->contact_company_name = $data->contact->contactCompanyName ?? null;
        $this->contact_street = $data->contact->contactStreet ?? null;
        $this->contact_street_no = $data->contact->contactStreetNo ?? null;
        $this->contact_postal_code = $data->contact->contactPostalCode ?? null;
        $this->contact_city = $data->contact->contactCity ?? null;
        $this->contact_country = $data->contact->contactCountry ?? null;
        $this->contact_phone = $data->contact->contactPhone ?? null;
        $this->contact_email = $data->contact->contactEmail ?? null;
        $this->contact_notes = $data->contact->contactNotes ?? null;
        $this->subscription_id = $data->subscriptionId ?? null;
        $this->subscription_end_point_id = $data->subscriptionEndPointId ?? null;
        $this->sf_sla = $data->sfSla ?? null;
        $this->status = $data->status ?? null;
        $this->wishdate = $data->wishdate ?? null;
        $this->switchdate = $data->switchdate ?? null;
        $this->modificationdate = $data->modificationDateTime ?? null;
        $this->l1_handover_equipment_name = $data->l1HandoverEquipment->l1HandoverEquipmentName ?? null;
        $this->l1_handover_equipment_rack = $data->l1HandoverEquipment->l1HandoverEquipmentRack ?? null;
        $this->l1_handover_equipment_slot = $data->l1HandoverEquipment->l1HandoverEquipmentSlot ?? null;
        $this->l1_handover_equipment_port = $data->l1HandoverEquipment->l1HandoverEquipmentPort ?? null;
        $this->l1_breakout_cable = $data->L1Breakout->l1BreakoutCable ?? null;
        $this->l1_breakout_fiber = $data->L1Breakout->l1BreakoutFiber ?? null;
        $this->alau_order_ref = $data->alauOrderRef ?? null;
        $this->note = $data->note ?? null;

        return true;
    }

    public function fillApiFromModel()
    {
    }
}
