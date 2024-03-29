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

namespace Modules\ProvVoip\Entities;

use Illuminate\Support\Collection;

class PhonenumberManagement extends \BaseModel
{
    // get functions for some address select options
    use \App\AddressFunctionsTrait;

    // The associated SQL table for this Model
    public $table = 'phonenumbermanagement';

    // do not auto delete anything related to managements (ATM this can only be PhonebookEntries which will be deleted explicitely)
    protected $delete_children = false;

    // Add your validation rules here
    public function rules()
    {
        return [
            'phonenumber_id' => 'required|exists:phonenumber,id,deleted_at,NULL|min:1',
            'trcclass' => 'required|exists:trcclass,id,deleted_at,NULL',
            'carrier_in' => 'required|exists:carriercode,id,deleted_at,NULL',
            'carrier_out' => 'required|exists:carriercode,id,deleted_at,NULL',
            'ekp_in' => 'required|exists:ekpcode,id,deleted_at,NULL',
            'activation_date' => 'nullable|date',
            'deactivation_date' => 'nullable|date',
        ];
    }

    // Don't forget to fill this array
    protected $fillable = [
        'phonenumber_id',
        'trcclass',
        'activation_date',
        'porting_in',
        'carrier_in',
        'ekp_in',
        'deactivation_date',
        'porting_out',
        'carrier_out',
        'ekp_out',
        'subscriber_company',
        'subscriber_department',
        'subscriber_salutation',
        'subscriber_academic_degree',
        'subscriber_firstname',
        'subscriber_lastname',
        'subscriber_street',
        'subscriber_house_number',
        'subscriber_zip',
        'subscriber_city',
        'subscriber_district',
        'subscriber_country',
        'autogenerated',
    ];

    // Name of View
    public static function view_headline()
    {
        return 'PhonenumberManagement';
    }

    public static function view_icon()
    {
        return '<i class="fa fa-phone text-info"></i>';
    }

    // link title in index view
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();
        $header = isset($this->phonenumber) ? trans_choice('view.Header_PhonenumberManagement', 1)." ({$this->phonenumber->prefix_number}/{$this->phonenumber->number})" : '';

        return ['table' => $this->table,
            'index_header' => [$this->table.'.id'],
            'bsclass' => $bsclass,
            'header' => $header, ];
    }

    public function get_bsclass()
    {
        return 'success';
    }

    /**
     * ALL RELATIONS
     * link with phonenumbers
     */
    public function phonenumber()
    {
        return $this->belongsTo(Phonenumber::class);
    }

    /**
     * The envia TEL contract the related phonenumber currently belongs to
     */
    public function envia_contract()
    {
        if (! \Module::collections()->has('ProvVoipEnvia')) {
            throw new \LogicException(__METHOD__.' only callable if module ProvVoipEnvia as active');
        } else {
            return $this->belongsTo(\Modules\ProvVoipEnvia\Entities\EnviaContract::class, 'enviacontract_id');
        }
    }

    // belongs to an phonenumber
    public function view_belongs_to()
    {
        return $this->phonenumber;
    }

    /**
     * Get relation to trc classes.
     *
     * @author Patrick Reichel
     */
    public function trc_class()
    {
        return $this->hasOne(TRCClass::class, 'trcclass');
    }

    /**
     * Get relation to envia orders.
     *
     * @author Patrick Reichel
     */
    protected function _envia_orders()
    {
        if (! \Module::collections()->has('ProvVoipEnvia')) {
            throw new \LogicException(__METHOD__.' only callable if module ProvVoipEnvia as active');
        }

        /* return $this->phonenumber->hasMany(\Modules\ProvVoipEnvia\Entities\EnviaOrder::class)->withTrashed()->where('ordertype', 'NOT LIKE', 'order/create_attachment'); */
        return $this->phonenumber->enviaorders(true, "ordertype NOT LIKE 'order/create_attachment'");
    }

    /**
     * Get relation to phonebookentry.
     *
     * @author Patrick Reichel
     */
    public function phonebookentry()
    {
        return $this->hasOne(PhonebookEntry::class, 'phonenumbermanagement_id');
    }

    // has zero or one phonebookentry object related
    public function view_has_one()
    {
        return [
            'PhonebookEntry' => $this->phonebookentry,
        ];
    }

    // View Relation.
    public function view_has_many()
    {
        if (! \Module::collections()->has('ProvVoipEnvia')) {
            $ret = [];
        }
        $ret['Edit']['EnviaOrder']['class'] = 'EnviaOrder';
        $ret['Edit']['EnviaOrder']['relation'] = $this->_envia_orders;
        $ret['Edit']['EnviaOrder']['options']['create_button_text'] = trans('provvoipenvia::view.enviaOrder.createButton');
        $ret['Edit']['EnviaOrder']['options']['delete_button_text'] = trans('provvoipenvia::view.enviaOrder.deleteButton');

        $ret['Edit']['EnviaContract']['class'] = 'EnviaContract';
        $enviacontracts = is_null($this->envia_contract) ? new Collection() : collect([$this->envia_contract]);
        $ret['Edit']['EnviaContract']['relation'] = $enviacontracts;
        $ret['Edit']['EnviaContract']['options']['hide_create_button'] = 1;
        $ret['Edit']['EnviaContract']['options']['hide_delete_button'] = 1;

        $ret['Edit']['PhonebookEntry']['class'] = 'PhonebookEntry';

        $relation = $this->phonebookentry;

        // can be created if no one exists, can be deleted if one exists
        if (is_null($relation)) {
            $ret['Edit']['PhonebookEntry']['relation'] = new Collection();
            $ret['Edit']['PhonebookEntry']['options']['hide_delete_button'] = 1;
        } else {
            $ret['Edit']['PhonebookEntry']['relation'] = collect([$relation]);
            $ret['Edit']['PhonebookEntry']['options']['hide_create_button'] = 1;
        }

        // TODO: auth - loading controller from model could be a security issue ?
        $ret['Edit']['EnviaAPI']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
        $ret['Edit']['EnviaAPI']['view']['vars']['extra_data'] = \Modules\ProvVoip\Http\Controllers\PhonenumberManagementController::_get_envia_management_jobs($this);

        return $ret;
    }

    /**
     * Before deleting a phonenumbermanagement we have to check some things
     *
     * @author Patrick Reichel
     */
    public function delete()
    {
        // with activated envia TEL module we have to perform some extra checks
        // we have to check this here as using ModemObserver::deleting() with return false does not prevent the monster from deleting child model instances!
        if (\Module::collections()->has('ProvVoipEnvia')) {
            // check if there is a not completely terminated envia TEL contract related to this management
            if ($this->envia_contract) {
                if (in_array($this->envia_contract->state, ['Aktiv', 'In Realisierung'])) {
                    $msg = trans('provvoipenvia::messages.phonenumbermanagementNotDeletable', [$this->id]).trans('provvoipenvia::messages.phonenumbermanagementNotDeletableReasonActiveEnviaContract');
                    $this->addAboveMessage($msg, 'error');

                    return false;
                }
                if (in_array($this->envia_contract->state, ['Gekündigt', 'Nicht ermittelbar'])) {
                    if ($this->envia_contract->end_date > now()) {
                        $msg = trans('provvoipenvia::messages.phonenumbermanagementNotDeletable', [$this->id]).trans('provvoipenvia::messages.phonenumbermanagementNotDeletableReasonEnviaContractEndDate');
                        $this->addAboveMessage($msg, 'error');

                        return false;
                    }
                }
            }

            // remove PhonebookEntry if one
            if ($this->phonebookentry) {
                $msg = trans('provvoipenvia::messages.phonenumbermanagementNotDeletable', [$this->id]).trans('provvoipenvia::messages.phonenumbermanagementNotDeletableReasonPhonebookentry');
                $this->addAboveMessage($msg, 'error');

                return false;
            }
        }

        // when arriving here: start the standard deletion procedure
        return parent::delete();
    }

    /**
     * BOOT:
     * - init phone observer
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new \Modules\ProvVoip\Observers\PhonenumberManagementObserver);
    }
}
