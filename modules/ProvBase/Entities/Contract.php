<?php

namespace Modules\ProvBase\Entities;

use DB;
use Module;
use Illuminate\Support\Facades\Log;

class Contract extends \BaseModel
{
    // get functions for some address select options
    use \App\AddressFunctionsTrait;

    // The associated SQL table for this Model
    public $table = 'contract';

    // temporary Variables filled during accounting command execution (Billing)
    public $expires = false;			// flag if contract expires this month - used in accounting command
    public $charge = [];				// total charge for each different Sepa Account with net and tax values

    // temporary variable used during daily conversion
    private $changes_on_daily_conversion = false;

    // Via modems -> mtas -> phonenumbers assigned Phonenumbers shown as read-only info field in edit view
    public $guarded = ['related_phonenrs'];

    // Add your validation rules here
    // TODO: dependencies of active modules (billing)
    public function rules()
    {
        $id = $this->id;

        $rules = [
            'number' => 'string|unique:contract,number,'.$id.',id,deleted_at,NULL',
            'number2' => 'nullable|string|unique:contract,number2,'.$id.',id,deleted_at,NULL',
            'number3' => 'nullable|string|unique:contract,number3,'.$id.',id,deleted_at,NULL',
            // 'number4' => 'string|unique:contract,number4,'.$id.',id,deleted_at,NULL', 	// old customer number must not be unique!
            'company' => 'required_if:salutation,placeholder_salutations_institution',
            'firstname' => 'required_if:salutation,placeholder_salutations_person',
            'lastname' => 'required_if:salutation,placeholder_salutations_person',
            'email' => 'nullable|email',
            'birthday' => 'nullable|date_format:Y-m-d',
            'contract_start' => 'date_format:Y-m-d',
            'contract_end' => 'nullable|date_format:Y-m-d', // |after:now -> implies we can not change stuff in an out-dated contract
        ];

        $addressKeys = ['street', 'house_number', 'zip', 'city'];
        foreach ($addressKeys as $key) {
            if (Module::collections()->has('PropertyManagement')) {
                $rules[$key] = 'required_without_all:realty_id,apartment_id';
            } else {
                $rules[$key] = 'required';
            }
        }

        if (Module::collections()->has('BillingBase')) {
            $rules['costcenter_id'] = 'required|numeric|min:1';
        } else {
            $rules['number'] .= '|nullable';
        }

        if (Module::collections()->has('BillingBase') || Module::collections()->has('ProvVoipEnvia')) {
            $rules['birthday'] .= '|required_if:salutation,placeholder_salutations_person';
        }

        return $rules;
    }

    // Name of View
    public static function view_headline()
    {
        return 'Contract';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-address-book-o"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        $ret = ['table' => $this->table,
            'index_header' => [$this->table.'.number', $this->table.'.firstname', $this->table.'.lastname', 'company', 'email', $this->table.'.zip', $this->table.'.city', 'district', $this->table.'.street', $this->table.'.house_number',  $this->table.'.additional', $this->table.'.contract_start', $this->table.'.contract_end'],
            'header' =>  self::labelFromData($this),
            'bsclass' => $bsclass,
            'order_by' => ['0' => 'asc'], ];

        if (Module::collections()->has('BillingBase')) {
            $ret['index_header'][] = 'costcenter.name';
            $ret['eager_loading'] = ['costcenter'];
            $ret['edit']['costcenter.name'] = 'get_costcenter_name';
        }

        if (Module::collections()->has('PropertyManagement')) {
            $ret['index_header'][] = 'contact_id';
            $ret['edit']['contact_id'] = 'isGroupContractAsString';
            $ret['filter']['contact_id'] = $this->groupContractFilterQuery();
        }

        return $ret;
    }

    public function isGroupContractAsString()
    {
        return $this->isGroupContract() ? trans('view.true') : trans('view.false');
    }

    /**
     * @return string  Bootstrap Color Class
     */
    public function get_bsclass()
    {
        $bsclass = 'success';

        if ($this->isGroupContract()) {
            return 'info';
        }

        if (! ($this->internet_access || $this->has_telephony)) {
            $bsclass = 'active';

            // '$this->id' to dont check when index table header is determined!
            if ($this->id && $this->isValid('now')) {
                $bsclass = 'warning';
            }
        }

        return $bsclass;
    }

    /**
     * @return string
     */
    public static function labelFromData($contract)
    {
        return $contract->number.' - '.$contract->firstname.' '.$contract->lastname;
    }

    public function get_costcenter_name()
    {
        return $costcenter = $this->costcenter ? $this->costcenter->name : trans('messages.noCC');
    }

    // View Relation.
    public function view_has_many()
    {
        $ret['Edit']['Modem']['class'] = 'Modem';
        $ret['Edit']['Modem']['relation'] = $this->modems;

        if (Module::collections()->has('BillingBase')) {
            // view has many version 2
            $ret['Edit']['Item']['class'] = 'Item';
            $ret['Edit']['Item']['relation'] = $this->items;
            $ret['Billing']['Item']['class'] = 'Item';
            $ret['Billing']['Item']['relation'] = $this->items;
            $ret['Edit']['SepaMandate']['class'] = 'SepaMandate';
            $ret['Edit']['SepaMandate']['relation'] = $this->sepamandates;
            $ret['Billing']['SepaMandate']['class'] = 'SepaMandate';
            $ret['Billing']['SepaMandate']['relation'] = $this->sepamandates;

            if (Module::collections()->has('PropertyManagement')) {
                if ($this->contact_id || $this->apartment_id) {
                    $ret['Edit']['Modem']['options']['hide_delete_button'] = 1;
                    $ret['Edit']['Modem']['options']['hide_create_button'] = 1;
                    $infoKey = $this->contact_id ? 'groupContract.modem' : 'tvContract';
                    $ret['Edit']['Modem']['info'] = trans("propertymanagement::messages.$infoKey");
                }

                // Check if contract belongs to a group contract
                $groupContract = $this->belongsToGroupContract();
                if ($groupContract) {
                    $msg = $groupContract === true ? trans('propertymanagement::messages.groupContract.item') : trans('propertymanagement::messages.groupContract.probably');

                    $ret['Edit']['Item']['info'] = $msg;
                    $ret['Billing']['Item']['info'] = $msg;
                }

                // Check if contract is a group contract
                if ($this->isGroupContract()) {
                    $tabName = trans('propertymanagement::view.propertyManagement');
                    $ret[$tabName]['Realty']['class'] = 'Realty';
                    $ret[$tabName]['Realty']['relation'] = $this->realties;
                }
            }

            if (Module::collections()->has('OverdueDebts')) {
                // resulting outstanding amount
                $ret['Edit']['DebtResult']['view']['view'] = 'overduedebts::Debt.result';
                $resultingDebt = $this->getResultingDebt();
                $ret['Edit']['DebtResult']['view']['vars']['debt'] = $resultingDebt['amount'];
                $ret['Edit']['DebtResult']['view']['vars']['bsclass'] = $resultingDebt['bsclass'];
            }

            // Show invoices in 2 panels
            if (! $this->relationLoaded('invoices')) {
                $this->setRelation('invoices', $this->invoices()->orderBy('id', 'desc')->get());
            }

            $invoicesPanel1 = collect();
            $countPanel1 = $this->invoices->count() > 15 ? 15 : $this->invoices->count();

            for ($i = 0; $i < $countPanel1; $i++) {
                $invoicesPanel1->push($this->invoices[$i]);
            }

            if (Module::collections()->has('OverdueDebts')) {
                $ret['Billing']['Debt']['class'] = 'Debt';
                $ret['Billing']['Debt']['relation'] = $this->debts;
            }

            $ret['Billing']['Invoice']['class'] = 'Invoice';
            $ret['Billing']['Invoice']['relation'] = $invoicesPanel1;
            $ret['Billing']['Invoice']['options']['hide_delete_button'] = 1;
            $ret['Billing']['Invoice']['options']['hide_create_button'] = 1;

            // 2nd panel with old invoices - collapsed and in 2 columns
            if ($this->invoices->count() > 15) {
                $invoicesPanel2 = collect();

                for ($i = 15; $i < $this->invoices->count(); $i++) {
                    $invoicesPanel2->push($this->invoices[$i]);
                }

                $ret['Billing']['OldInvoices']['view']['view'] = 'billingbase::Contract.oldInvoices';
                $ret['Billing']['OldInvoices']['view']['vars']['invoices'] = $invoicesPanel2;
                $ret['Billing']['OldInvoices']['panelOptions']['display'] = 'none';
            }
        }

        if (Module::collections()->has('ProvVoipEnvia') &&
            (! Module::collections()->has('PropertyManagement') || (! $this->isGroupContract() && ! $this->apartment_id))) {
            $ret['envia TEL']['EnviaContract']['class'] = 'EnviaContract';
            $ret['envia TEL']['EnviaContract']['relation'] = $this->enviacontracts;
            $ret['envia TEL']['EnviaContract']['options']['hide_create_button'] = 1;
            $ret['envia TEL']['EnviaContract']['options']['hide_delete_button'] = 1;

            $ret['envia TEL']['EnviaOrder']['class'] = 'EnviaOrder';
            $ret['envia TEL']['EnviaOrder']['relation'] = $this->_envia_orders;
            $ret['envia TEL']['EnviaOrder']['options']['create_button_text'] = trans('provvoipenvia::view.enviaOrder.createButton');
            $ret['envia TEL']['EnviaOrder']['options']['delete_button_text'] = trans('provvoipenvia::view.enviaOrder.deleteButton');

            // TODO: auth - loading controller from model could be a security issue ?
            $ret['envia TEL']['EnviaAPI']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
            $ret['envia TEL']['EnviaAPI']['view']['vars']['extra_data'] = \Modules\ProvBase\Http\Controllers\ContractController::_get_envia_management_jobs($this);

            // for better navigation: show modems also in envia TEL blade
            $ret['envia TEL']['Modem']['class'] = 'Modem';
            $ret['envia TEL']['Modem']['relation'] = $this->modems;
        }

        if (Module::collections()->has('Ccc') && Module::collections()->has('BillingBase')) {
            $ret['Create Connection Infos']['Connection Information']['view']['view'] = 'ccc::prov.conn_info';
        }

        if (Module::collections()->has('Mail')) {
            $ret['Email']['Email'] = $this->emails;
        }

        $this->addViewHasManyTickets($ret);

        return $ret;
    }

    public function view_belongs_to()
    {
        if (! Module::collections()->has('PropertyManagement')) {
            return;
        }

        if ($this->realty_id) {
            return $this->realty;
        }

        if ($this->apartment_id) {
            return $this->apartment;
        }

        if ($this->modems->isNotEmpty()) {
            return \Modules\PropertyManagement\Entities\Apartment::join('modem', 'apartment.id', 'modem.apartment_id')
                ->join('contract', 'contract.id', 'modem.contract_id')
                ->where('contract.id', $this->id)
                ->whereNull('modem.deleted_at')
                ->whereNull('apartment.deleted_at')
                ->select('apartment.*')
                ->first();
        }
    }

    /*
     * Relations
     */
    public function debts()
    {
        return $this->hasMany(\Modules\OverdueDebts\Entities\Debt::class)->orderBy('date', 'desc')->orderBy('id', 'desc');
    }

    public function modems()
    {
        return $this->hasMany(Modem::class);
    }

    /**
     * related enviacontracts
     */
    public function enviacontracts()
    {
        if (! Module::collections()->has('ProvVoipEnvia')) {
            throw new \LogicException(__METHOD__.' only callable if module ProvVoipEnvia as active');
        } else {
            return $this->hasMany(\Modules\ProvVoipEnvia\Entities\EnviaContract::class);
        }
    }

    /**
     * Get the purchase tariff
     */
    public function phonetariff_purchase()
    {
        return $this->belongsTo(\Modules\ProvVoip\Entities\PhoneTariff::class, 'purchase_tariff');
    }

    /**
     * Get the next purchase tariff
     */
    public function phonetariff_purchase_next()
    {
        return $this->belongsTo(\Modules\ProvVoip\Entities\PhoneTariff::class, 'next_purchase_tariff');
    }

    /**
     * Get the sale tariff
     */
    public function phonetariff_sale()
    {
        return $this->belongsTo(\Modules\ProvVoip\Entities\PhoneTariff::class, 'voip_id');
    }

    /**
     * Get the next sale tariff
     */
    public function phonetariff_sale_next()
    {
        return $this->belongsTo(\Modules\ProvVoip\Entities\PhoneTariff::class, 'next_voip_id');
    }

    /**
     * Get relation to envia orders.
     *
     * @author Patrick Reichel
     */
    protected function _envia_orders()
    {
        if (! Module::collections()->has('ProvVoipEnvia')) {
            throw new \LogicException(__METHOD__.' only callable if module ProvVoipEnvia as active');
        }

        return $this->hasMany(\Modules\ProvVoipEnvia\Entities\EnviaOrder::class)->where('ordertype', 'NOT LIKE', 'order/create_attachment');
    }

    public function items()
    {
        return $this->hasMany(\Modules\BillingBase\Entities\Item::class);
    }

    public function items_sorted_by_valid_from_desc()
    {
        return $this->hasMany(\Modules\BillingBase\Entities\Item::class)->orderBy('valid_from', 'desc');
    }

    public function sepamandates()
    {
        return $this->hasMany(\Modules\BillingBase\Entities\SepaMandate::class);
    }

    public function emails()
    {
        return $this->hasMany(\Modules\NmsMail\Entities\Email::class);
    }

    public function get_email_count()
    {
        $tariff = $this->_get_valid_tariff_item_and_count('Internet');

        return $tariff['count'] ? $tariff['item']->product->email_count : 0;
    }

    public function costcenter()
    {
        return $this->belongsTo(\Modules\BillingBase\Entities\CostCenter::class, 'costcenter_id');
    }

    public function salesman()
    {
        return $this->belongsTo(\Modules\BillingBase\Entities\Salesman::class);
    }

    public function invoices()
    {
        return $this->hasMany(\Modules\BillingBase\Entities\Invoice::class);
        // $srs  = SettlementRun::where('verified', '=', '0')->get(['id'])->pluck('id')->all();
        // $hide = $srs ? : 0;
        // return $this->hasMany(\Modules\BillingBase\Entities\Invoice::class)->where('contract_id', '=', $this->id)->where('settlementrun_id', '!=', [$hide]);
    }

    public function CccUser()
    {
        return $this->hasOne(\Modules\Ccc\Entities\CccUser::class);
    }

    public function realties()
    {
        return $this->hasMany(\Modules\PropertyManagement\Entities\Realty::class)->orderBy('street')->orderBy('house_nr');
    }

    public function apartment()
    {
        return $this->belongsTo(\Modules\PropertyManagement\Entities\Apartment::class);
    }

    /**
     * Emulate a belongsTo contract->realty relation consisting actually of chain contract->modem->apartment->realty
     * Note: A direct contract->realty relation can not exist
     *
     * @return \Modules\PropertyManagement\Entities\Realty
     */
    public function getRealtyAttribute()
    {
        if ($this->relationLoaded('realty')) {
            return $this->getRelation('realty');
        }

        $realty = $this->realty();
        $this->setRelation('realty', $realty);

        return $realty;
    }

    public function realty()
    {
        // TODO - Laravel5.8/L5.8: use hasManyThrough or intermediate Tables (see https://laravel.com/docs/5.8/eloquent-relationships#has-one-through and https://laravel.com/docs/5.8/eloquent-relationships#defining-custom-intermediate-table-models)
        return self::join('modem', 'modem.contract_id', 'contract.id')
            ->join('apartment', 'apartment.id', 'modem.apartment_id')
            ->join('realty', 'apartment.realty_id', 'realty.id')
            ->where('contract.id', $this->id)
            ->whereNull('modem.deleted_at')
            ->whereNull('apartment.deleted_at')
            ->whereNull('realty.deleted_at')
            ->select('realty.*')
            ->first();
    }

    /**
     * Generate use a new user login password
     * This does not save the involved model
     */
    public function generate_password($length = 10)
    {
        $this->password = \Acme\php\Password::generate_password($length);
    }

    /**
     * Helper to get the customer number (may be identical with the contract number).
     * As there is no hard coded customer number in database we have to use this mapper. The semantic meaning of number…number4 can be defined in global configuration.
     *
     * @author Patrick Reichel
     *
     * @todo: in this first step the relation is hardcoded within the function. Later on we have to check the mapping against the configuration.
     * @return current customer number
     */
    public function customer_number()
    {
        if (boolval($this->number3) && (\Str::lower($this->number3 != 'n/a'))) {
            $customer_number = $this->number3;
        } else {
            $customer_number = $this->number;
        }

        return $customer_number;
    }

    /**
     * Helper to get the legacy customer number (may be identical wtih the legacy contract number).
     * As there is no hard coded customer number in database we have to use this mapper. The semantic meaning of number…number4 can be defined in global configuration.
     *
     * @author Patrick Reichel
     *
     * @todo: in this first step the relation is hardcoded within the function. Later on we have to check the mapping against the configuration.
     * @return current customer number
     */
    public function customer_number_legacy()
    {
        if (boolval($this->number4) && (\Str::lower($this->number4 != 'n/a'))) {
            $customer_number_lecacy = $this->number4;
        } else {
            $customer_number_lecacy = $this->number2;
        }

        return $customer_number_lecacy;
    }

    /**
     * Helper to get all phonenumbers related to contract.
     *
     * @author Patrick Reichel
     */
    public function related_phonenumbers()
    {

        // if voip module is not active: there can be no phonenumbers
        if (! Module::collections()->has('ProvVoip')) {
            return [];
        }

        $phonenumbers_on_contract = [];

        // else: search all mtas on all modems
        foreach ($this->modems as $modem) {
            foreach ($modem->mtas as $mta) {
                foreach ($mta->phonenumbers as $phonenumber) {
                    array_push($phonenumbers_on_contract, $phonenumber);
                }
            }
        }

        return $phonenumbers_on_contract;
    }

    /*
     * Convert a 'YYYY-MM-DD' to Carbon Time Object
     *
     * We use this to convert a SQL start / end contract date to a carbon
     * object. Carbon Time Objects can be compared with lt(), gt(), ..
     *
     * TODO: move this stuff to extensions
     */
    private function _date_to_carbon($date)
    {
        // createFromFormat crashes if nothing given
        if (! boolval($date)) {
            return;
        }

        return \Carbon\Carbon::createFromFormat('Y-m-d', $date);
    }

    /*
     * Check if Carbon date is null
     *
     * NOTE: This is a little bit of pain, but it works.
     *       But string compare to '0000' is even more pain and
     *       also other tutorials point this out to be a freaky problem.
     * See: http://stackoverflow.com/questions/25959324/comparing-null-date-carbon-object-in-laravel-4-blade-templates
     *
     * TODO: move this stuff to extensions
     *
     * Note by Patrick: null dates can either be Carbons or strings with “0000-00-00”/“0000-00-00 00:00:00” or “NULL”
     */
    private function _date_null($date)
    {
        if (! boolval($date)) {
            return true;
        }

        if (is_string($date)) {
            return \Str::startswith($date, '0000');
        }

        // Carbon object
        return ! ($date->year > 1900);
    }

    /**
     * The Daily Scheduling Function
     *
     * Tasks:
     *  1. Check if $this contract end date is expired -> disable internet_access
     *  2. Check if $this is a new contract and activate it -> enable internet_access
     *  3. Change QoS id and Voip id if actual valid (billing-) tariff changes
     *
     * @return none
     * @author Torsten Schmidt, Nino Ryschawy, Patrick Reichel
     */
    public function daily_conversion()
    {
        // we don't check old invalid items that ended before this number of days in past
        // 90 days is a security span in case it was somehow not possible to examine this function for some days
        // To let the contractCommand belatedly correct everything all former items are needed as well
        // (e.g. in case valid_to_fixed=0 and end date would have always set to today)
        $item_max_ended_before = 90;

        Log::debug('Starting daily conversion for contract '.$this->number, [$this->id]);

        if (! Module::collections()->has('BillingBase')) {
            $this->_update_internet_access_from_contract();
        } else {

            // Get items by only 1 db query & set them as contract relations to work with them in next functions
            // with that there are no more refresh database queries necessary (items do not have to be reloaded again)
            $items = $this->items()
                ->leftJoin('product', 'product.id', '=', 'item.product_id')
                ->whereIn('product.type', ['Internet', 'Voip'])
                ->where(whereLaterOrEqual('item.valid_to', date('Y-m-d', strtotime("-$item_max_ended_before days"))))
                // ->orderBy('valid_from', 'desc')
                ->select('item.*')
                ->with('product')
                ->get();

            $this->setRelations(['items' => $items]);

            // Task 3: Check and possibly update item's valid_from and valid_to dates
            $this->_update_inet_voip_dates();

            // deprecated - but kept for reference
            // $this->load('items'); $this->fresh();

            // Task 4: Check and possibly change product related data (qos_id, voip, purchase_tariff)
            // for this contract depending on the start/end times of its items
            $this->update_product_related_data();

            // NOTE: Keep this order! - update network access after all adaptions are made
            // Task 1 & 2 included
            $this->_update_service_access_from_items();

            if (Module::collections()->has('Mail')) {
                $this->_update_email_index();
            }
        }

        if ($this->changes_on_daily_conversion) {
            $this->observer_enabled = false;
            $this->save();
            $this->push_to_modems();
        }
    }

    /**
     * This enables/disables internet_access according to start and end date of the contract.
     * Used if billing is disabled.
     *
     * @author Torsten Schmidt
     */
    protected function _update_internet_access_from_contract()
    {
        $now = \Carbon\Carbon::now();

        // Task 1: Check if $this contract end date is expired -> disable internet_access
        if ($this->contract_end) {
            $end = $this->_date_to_carbon($this->contract_end);
            if ($end->lt($now) && ! $this->_date_null($end) && $this->internet_access == 1) {
                Log::info('daily: contract: disable based on ending contract date for '.$this->id);

                $this->internet_access = 0;
                $this->changes_on_daily_conversion = true;
            }
        }

        // Task 2: Check if $this is a new contract and activate it -> enable internet_access
        // Note: to avoid enabling contracts which are disabled manually, we also check if
        //       maximum time beetween start contract and now() is not older than 1 day.
        // Note: This requires the daily scheduling to run well
        //       Otherwise the contracts must be enabled manually
        // TODO: give them a good testing
        if ($this->contract_start) {
            $start = $this->_date_to_carbon($this->contract_start);
            if ($start->lt($now) && ! $this->_date_null($start) && $start->diff($now)->days <= 1 && $this->internet_access == 0) {
                Log::info('daily: contract: enable contract based on start contract date for '.$this->id);

                $this->internet_access = 1;
                $this->changes_on_daily_conversion = true;
            }
        }
    }

    /**
     * This enables/disables network/telephony access based on
     * validity of contract
     * existence of currently active items of types Internet and Voip
     *
     * @author Patrick Reichel, Nino Ryschawy
     */
    protected function _update_service_access_from_items()
    {
        // check if DB update is required
        $contract_changed = false;

        $active_tariff_info_internet = $this->_get_valid_tariff_item_and_count('Internet');
        $active_tariff_info_voip = $this->_get_valid_tariff_item_and_count('Voip');

        $active_count_internet = $active_tariff_info_internet['count'];
        $active_count_voip = $active_tariff_info_voip['count'];

        if ($this->isValid('Now')) {
            // valid internet tariff
            if ($active_count_internet && ! $this->internet_access) {
                $this->internet_access = 1;
                $this->changes_on_daily_conversion = true;
                Log::info('daily: contract: enabling internet_access based on active internet/voip items for contract '.$this->id);
            // no valid internet tariff
            } elseif (! $active_count_internet && $this->internet_access) {
                $this->internet_access = 0;
                $this->changes_on_daily_conversion = true;
                Log::info('daily: contract: disabling internet_access based on active internet/voip items for contract '.$this->id);
            }

            if ($active_count_voip && ! $this->has_telephony) {
                $this->has_telephony = 1;
                $this->changes_on_daily_conversion = true;
                Log::info('daily: contract: switch to has_telephony', [$this->id]);
            } elseif (! $active_count_voip && $this->has_telephony) {
                $this->has_telephony = 0;
                $this->changes_on_daily_conversion = true;
                Log::info('daily: contract: switch from has_telephony to no telephony tariff', [$this->id]);
            }
        } else {
            // invalid contract - disable every access
            if ($this->internet_access) {
                $this->internet_access = 0;
                $this->changes_on_daily_conversion = true;
                Log::info('daily: contract: disabling internet_access based on active internet/voip items for contract '.$this->id);
            }

            if ($this->has_telephony) {
                $this->has_telephony = 0;
                $this->changes_on_daily_conversion = true;
                Log::info('daily: contract: Unset has_telephony as contract is invalid!', [$this->id]);
            }
        }
    }

    /**
     * This helper updates dates for Internet & Voip items on this contract under the following conditions:
     *	- valid_from:
     *		- valid_from_fixed is false
     *		- valid_from is before tomorrow
     *		- if both are true: set to tomorrow
     *	- valid_to:
     *		- valid_to_fixed is false
     *		- valid_to is before today
     *		- if both are true: set to today
     *
     *	This way we ensure:
     *		- items with not fixed end dates are valid today
     *		- items with not fixed start dates are not active
     *
     * Attention: Have in mind that changing item dates also fires in ItemObserver::updating()
     * which for example possibly changes contracts (voip_id, purchase_tariff) etc.!
     *
     * @author Patrick Reichel, Nino Ryschawy
     *
     * @return null
     */
    protected function _update_inet_voip_dates()
    {
        // items only exist if Billingbase is enabled
        if (! Module::collections()->has('BillingBase')) {
            return;
        }

        $tomorrow = \Carbon\Carbon::tomorrow();
        $today = \Carbon\Carbon::today();

        foreach ($this->items as $key => $item) {
            if (! $item->product) {
                Log::error("Product of item $item->id (ID) of contract ".$item->contract->number.' (number) is missing');
                unset($this->items[$key]);

                continue;
            }

            // flag to decide if item has to be saved at the end of the loop
            $item_changed = false;
            $type = $item->product->type;

            // if the startdate is fixed: ignore
            if (! boolval($item->valid_from_fixed)) {
                // set to tomorrow if there is a start date but this is less then tomorrow
                if (! $this->_date_null($item->valid_from)) {
                    $from = $this->_date_to_carbon($item->valid_from);
                    if ($from->lt($tomorrow)) {
                        $new_date = $tomorrow->toDateString();
                        $item->valid_from = $new_date;
                        $item_changed = true;
                        Log::info('contract: changing item '.$item->id.' ('.$item->product->name.') valid_from to '.$new_date.' for Contract '.$this->number, [$this->id]);
                    }
                }
            }

            // if the enddate is fixed: ignore
            if (! boolval($item->valid_to_fixed)) {
                // set to today if there is an end date less than today
                if (! $this->_date_null($item->valid_to)) {
                    $to = $this->_date_to_carbon($item->valid_to);
                    if ($to->lt($today)) {
                        $new_date = $today->toDateString();
                        $item->valid_to = $new_date;
                        $item_changed = true;
                        Log::info('contract: changing item '.$item->id.' ('.$item->product->name.') valid_to to '.$new_date.' for Contract '.$this->number, [$this->id]);
                    }
                }
            }

            // finally: save the change(s)
            if ($item_changed) {
                // avoid endless loop - dont unnecessarily call daily_conversion again
                /* also the following old concerns are vitiated by disabling the observer:
                    * attention: update youngest valid_from items first (to avoid problems in relation with
                    * ItemObserver::update() which else set valid_to smaller than valid_from in some cases)!
                    * and to avoid “Multipe valid tariffs active” warning
                */
                $item->observer_dailyconversion = false;
                $item->save();
            }
        }
    }

    /**
     * Update the email indices according to the number of allowed emails,
     * which is derived from the current internet item.
     * An email index of 0 means disabled, 1 is the primary email address.
     *
     * @return none
     * @author Ole Ernst
     */
    protected function _update_email_index()
    {
        $cnt = $this->get_email_count();

        // fast path: set all indices to 0, as no email is allowed
        if (! $cnt) {
            foreach ($this->emails as $email) {
                $email->index = 0;
                $email->save();
            }

            return;
        }

        // remove all email indices, which are already in use
        $used = [];
        foreach ($this->emails as $email) {
            $used[] = $email->index;
        }
        $avail = array_diff(range($cnt, 1), $used);

        // try to fit all email indices into available slots
        foreach ($this->emails as $email) {
            if ($email->index > $cnt) {
                $email->index = $avail ? array_pop($avail) : 0;
                $email->save();
            }
        }
    }

    /**
     * The Monthly Scheduling Function
     *
     * Tasks:
     *  1. monthly QOS transition / change
     *  2. monthly VOIP transition / change
     *
     * “next*” values are initialized with 0 on ItemObserver::creating() and
     * possibly overwritten by ItemObserver::updating() (which can also be executed by daily conversion)
     *
     * So the daily conversion also can change these values – but this is only triggered on updating an item.
     * To write long term changes to DB we have to check all items in this monthly conversion.
     *
     * @return none
     * @author Torsten Schmidt, Patrick Reichel
     */
    public function monthly_conversion()
    {
        // with billing module -> done by daily conversion
        if (Module::collections()->has('BillingBase')) {
            return;
        }

        $contract_changed = false;

        // Tariff: monthly Tariff change – "Tarifwechsel"
        if (
            ($this->next_qos_id > 0) &&
            ($this->qos_id != $this->next_qos_id)
        ) {
            Log::info('monthly: contract: change Tariff for '.$this->id.' from '.$this->qos_id.' to '.$this->next_qos_id);
            $this->qos_id = $this->next_qos_id;
            $this->next_qos_id = 0;
            $contract_changed = true;
        }

        // VOIP: monthly VOIP change
        if ($this->next_voip_id > 0) {
            Log::info('monthly: contract: change VOIP-ID for '.$this->id.' from '.$this->voip_id.' to '.$this->next_voip_id);
            $this->voip_id = $this->next_voip_id;
            $this->next_voip_id = 0;
            $contract_changed = true;
        }

        if ($contract_changed) {
            $this->save();
            $this->push_to_modems();
        }
    }

    /**
     * Returns last started actual valid tariff assigned to this contract.
     *
     * @author Patrick Reichel
     *
     * @param $type product type as string (e.g. 'Internet', 'Voip', etc.)
     *
     * @return object 	item
     */
    public function get_valid_tariff($type)
    {
        return $this->_get_valid_tariff_item_and_count($type)['item'];
    }

    /**
     * Returns number of currently active items of given type assigned to this contract.
     *
     * Use this for checks – a value bigger than 1 should be an error and result in special action!
     *
     * @author Patrick Reichel
     *
     * @param $type product type as string (e.g. 'Internet', 'Voip', etc.)
     *
     * @return number of active items for given type and this contract
     */
    public function get_valid_tariff_count($type)
    {
        return $this->_get_valid_tariff_item_and_count($type)['count'];
    }

    /**
     * Return last started actual valid tariff and number of active tariffs of given type for this contract.
     *
     * @author Nino Ryschawy, Patrick Reichel
     *
     * @param $type product type as string (e.g. 'Internet', 'Voip', etc.)
     *
     * @return array containing two values:
     *	'item' => the last startet tariff (item object)
     *	'count' => integer
     */
    protected function _get_valid_tariff_item_and_count($type)
    {
        if (! Module::collections()->has('BillingBase')) {
            return ['item' => null, 'count' => 0];
        }

        $last = $count = 0;
        $tariff = null;			// item

        // Dont make a DB request when items are already loaded
        if ($this->relationLoaded('items')) {
            $tariffs = $this->items->where('valid_from', '<=', date('Y-m-d'));
        } else {
            $tariffs = $this->items()
                ->join('product as p', 'item.product_id', '=', 'p.id')
                ->select('item.*', 'p.*', 'item.id as id')
                ->where('type', '=', $type)->where('valid_from', '<=', date('Y-m-d'))
                ->where(whereLaterOrEqual('valid_to', date('Y-m-d')));

            if (strtolower($type) == 'voip') {
                $tariffs->with('product.salestariff');
            }

            $tariffs = $tariffs->get();
        }

        if ($tariffs->isEmpty()) {
            return ['item' => null, 'count' => 0];
        }

        foreach ($tariffs as $item) {
            if ($item->product->type != $type) {
                continue;
            }

            if (! $item->isValid('Now')) {
                continue;
            }

            $count++;

            $start = $item->get_start_time();
            if ($start > $last) {
                $tariff = $item;
                $last = $start;
            }
        }

        // This is an error! There should only be one active item per type and contract
        if ($count > 1) {
            Log::warning('There are '.$count.' active items of product type '.$type.' assigned to contract '.$this->number.' ['.$this->id.'].');
        }

        return ['item' => $tariff, 'count' => $count];
    }

    /**
     * Wrapper to call updater helper methods depending on product type of each given item
     * The called methods write product related data (qos_id, next_qos_id, voip_id, next_voip_id, purchase_tariff, next_purchase_tariff)
     * from items to contract depending on item's valid_from & valid_to dates.
     * So the data is available if billing is deactivated; also ProvVoipEnvia uses this data directly
     * (instead of extracting from items).
     *
     * This is called by ItemObserver::updating() (also indirectly by daily conversion) for currently
     * updated items and also by monthly conversion for all items on contract
     *
     * @author Patrick Reichel
     *
     * @param $items iterable (array, Collection) containing items
     *
     * @return null
     */
    public function update_product_related_data()
    {
        $valid_tariff = false;

        foreach ($this->items as $item) {
            if (! $item->product) {
                Log::error("Product of item $item->id (ID) of contract ".$item->contract->number.' (number) is missing');

                continue;
            }

            $type = $item->product->type;

            // check which month is affected by the currently investigated item
            if (
                // check if information is current
                // this is the case for currently active items:
                //	- latest possible startday is today
                //	- closest possible endday is today
                // there should only be one of each type
                ($item->valid_from <= date('Y-m-d')) &&
                (
                    $this->_date_null($item->valid_to) ||
                    ($item->valid_to >= date('Y-m-d'))
                )
            ) {

                // check if there is more than one active item for given type ⇒ this is an error
                // this can happen if one fixes thè start date of one and forgets to fix the end date
                // of an other item
                if (! isset($valid_tariff_info[$type])) {
                    $valid_tariff_info[$type] = $this->_get_valid_tariff_item_and_count($type);
                }

                $valid_tariff = true;

                if ($valid_tariff_info[$type]['count'] > 1) {
                    // this should never occur!!
                    if ($valid_tariff_info[$type]['item']->id != $item->id) {
                        Log::warning('Using newer item '.$valid_tariff_info[$type]['item']->id.' instead of '.$item->id.' to update current data on contract '.$this->number.' ['.$this->id.'].');
                    }
                    $this->_update_product_related_current_data($valid_tariff_info[$type]['item']);
                } else {
                    // default case
                    $this->_update_product_related_current_data($item);
                }

                // if no enddate is set: check if future data has to be changed to
                // this can occur in the following scenario:
                //	1) new item “B” created (that replaces the current item “A”) ⇒ future data changed
                //	2) item “B” deleted ⇒ nothing done
                //  3) end date in item “A” reset to null
                // now we have to replace the future data by data from item “A” (again)
                if ($this->_date_null($item->valid_to)) {
                    $this->_update_product_related_future_data($item);
                }
            }
            // check if information is for the future
            // this should be save because there is max. one of each type allowed
            // but if there is more than one: no problem – in worst case we overwrite next_* values
            // multiple times
            elseif ($item->valid_from > date('Y-m-d')) {
                $this->_update_product_related_future_data($item);
            } else {
                // items finished before today don't update contracts!
                continue;
            }
        }

        if (! $valid_tariff) {
            $this->qos_id = 0;
            $this->changes_on_daily_conversion = true;
        }
    }

    /**
     * Check for (and possibly perform) product related changes in contract for the current month
     *
     * @author Patrick Reichel
     *
     * @param $item to be analyzed
     *
     * @return null
     * Sets global var $changes_on_daily_conversion when contract data has changed
     */
    protected function _update_product_related_current_data($item)
    {
        $contract_changed = false;

        if ($item->product->type == 'Voip') {

            // check if there are changes in state for voip_id and purchase_tariff
            if ($this->voip_id != $item->product->voip_sales_tariff_id) {
                $this->voip_id = $item->product->voip_sales_tariff_id;
                $this->changes_on_daily_conversion = true;
                Log::info('contract: changing voip_id to '.$this->voip_id.' for contract '.$this->number, [$this->id]);
            }
            if ($this->purchase_tariff != $item->product->voip_purchase_tariff_id) {
                $this->purchase_tariff = $item->product->voip_purchase_tariff_id;
                $this->changes_on_daily_conversion = true;
                Log::info('contract: changing purchase_tariff to '.$this->purchase_tariff.' for contract '.$this->number, [$this->id]);
            }
        }

        if ($item->product->type == 'Internet') {
            if ($this->qos_id != $item->product->qos_id) {
                $this->qos_id = $item->product->qos_id;
                $this->changes_on_daily_conversion = true;
                Log::info('contract: changing  qos_id to '.$this->qos_id.' for contract '.$this->number, [$this->id]);
            }
        }
    }

    /**
     * Check for (and possibly perform) product related changes in contract for the next month
     *
     * @author Patrick Reichel, Nino Ryschawy
     *
     * @param $item to be analyzed
     *
     * @return null
     * Sets global var $changes_on_daily_conversion when contract data has changed
     */
    protected function _update_product_related_future_data($item)
    {
        $contract_changed = false;

        if ($item->product->type == 'Voip') {

            // check if there are changes in state for voip_id and purchase_tariff
            if ($this->next_voip_id != $item->product->voip_sales_tariff_id) {
                $this->next_voip_id = $item->product->voip_sales_tariff_id;
                $this->changes_on_daily_conversion = true;
                Log::info('contract: changing next_voip_id to '.$this->next_voip_id.' for contract '.$this->number, [$this->id]);
            }
            if ($this->next_purchase_tariff != $item->product->voip_purchase_tariff_id) {
                $this->next_purchase_tariff = $item->product->voip_purchase_tariff_id;
                $this->changes_on_daily_conversion = true;
                Log::info('contract: changing next_purchase_tariff to '.$this->next_purchase_tariff.' for contract '.$this->number, [$this->id]);
            }
        }

        if ($item->product->type == 'Internet') {
            if ($this->next_qos_id != $item->product->qos_id) {
                $this->next_qos_id = $item->product->qos_id;
                $this->changes_on_daily_conversion = true;
                Log::info('contract: changing next_qos_id to '.$this->next_qos_id.' for contract '.$this->number, [$this->id]);
            }
        }
    }

    /**
     * Push all settings from Contract layer to the related child Modems (for $this)
     * This includes: internet_access, qos_id
     *
     * Called in daily_conversion only if sth changed
     *
     * Note: This allows only 1 tariff qos_id for all modems
     *
     * @author: Torsten Schmidt, Nino Ryschawy
     */
    public function push_to_modems()
    {
        $internetAccessChanged = false;

        foreach ($this->modems as $modem) {
            if ($modem->internet_access != $this->internet_access) {
                $internetAccessChanged = true;
            }

            $modem->internet_access = $this->internet_access;
            $modem->qos_id = $this->qos_id;
            $modem->observer_enabled = false;
            $modem->updateRadius(false);
            $modem->make_configfile();
            $modem->save();
            $modem->restart_modem();
        }

        if ($internetAccessChanged) {
            Modem::createDhcpBlockedCpesFile();
        }
    }

    /**
     * Helper to map database numberX fields to description
     *
     * @author Patrick Reichel
     */
    public function get_column_description($col_name)
    {

        // later use global config to get mapping
        $mappings = [
            'number' => 'Contract number',
            'number2' => 'Contract number legacy',
            'number3' => 'Customer number',
            'number4' => 'Customer number legacy',
        ];

        return $mappings[$col_name];
    }

    /**
     * BOOT:
     * - init observer
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new \Modules\ProvBase\Observers\ContractObserver);
    }

    /**
     * Returns start time of item - Note: contract_start field has higher priority than created_at
     *
     * @return int 		time in seconds after 1970
     */
    public function get_start_time()
    {
        $date = $this->contract_start && $this->contract_start != '0000-00-00' ? $this->contract_start : $this->created_at->toDateString();

        return strtotime($date);
    }

    /**
     * Returns start time of item - Note: contract_start field has higher priority than created_at
     *
     * @return int 		time in seconds after 1970
     */
    public function get_end_time()
    {
        return $this->contract_end && $this->contract_end != '0000-00-00' ? strtotime($this->contract_end) : null;
    }

    /**
     * Returns valid sepa mandate for specific timespan
     *
     * @param 	string 		Timespan - LAST (!!) 'year'/'month' or 'now
     * @param 	int 	If Set only Mandates related to specific SepaAccount are considered (related via CostCenter)
     * @return 	object 		Valid Sepa Mandate with latest start date
     *
     * @author Nino Ryschawy
     */
    public function get_valid_mandate($timespan = 'now', $sepaaccount_id = 0)
    {
        $mandate = null;
        $last = null;

        foreach ($this->sepamandates as $m) {
            if (! is_object($m)) {
                continue;
            }

            if ($m->disable || ! $m->isValid($timespan)) {
                continue;
            }

            if ($m->costcenter xor $sepaaccount_id) {
                continue;
            }

            if ($sepaaccount_id && ($m->costcenter->sepaaccount->id != $sepaaccount_id)) {
                continue;
            }

            if ($mandate) {
                Log::warning("SepaMandate: Multiple valid mandates active for Contract $this->number", [$this->id]);
            }

            $start = $m->get_start_time();

            if ($last === null || $start > $last) {
                $mandate = $m;
                $last = $start;
            }
        }

        return $mandate;
    }

    /**
     * Get Contracts next possible cancelation dates - dependent of internet or voip item
     *
     * @author Nino Ryschawy
     *
     * @param string 	Date for that cancelation dates shall be retrieved: e.g. for today (for alert in observer)
     *                  or last day of last month (settlement run)
     * @return array 	case 1: default
     *                  case 2: contract was already canceled (important for settlement run) - 'canceled_to' is set
     *
     * NOTE: if date is last day of last month it's automatically assumed that cancelation dates are requested for/from the settlement run
     */
    public function getCancelationDates($date = '')
    {
        $ret = [
            'cancelation_day' => '',
            'canceled_to' => '',
            'end_of_term' => '',
            'maturity' => '',
            'tariff' => null,           // current valid tariff
        ];

        // check if contract was already canceled for settlement run
        if (! $this->isDirty('contract_end') && $this->contract_end && $this->get_end_time()) {
            $ret['canceled_to'] = $this->contract_end;

            return $ret;
        }

        // e.g. check for current date or for settlement run
        if (! $date) {
            $date = date('Y-m-d');
        } elseif ($date == date('Y-m-d', strtotime('last day of last month'))) {
            // this is important for the filter of the following db query
            $date = date('Y-m-d', strtotime('first day of this month'));
        }

        // get last internet & voip tariff (take last added if multiple valid tariffs)
        // TODO?: get current inet tariff and all tariffs that start after - check if current tariffs pon is reached than take next if exist?
        $tariffs = $this->items()
            ->join('product as p', 'item.product_id', '=', 'p.id')
            ->select('item.*', 'p.type', 'p.bundled_with_voip', 'p.name')
            ->whereIn('type', ['Internet', 'Voip', 'TV'])
            ->where(function ($query) use ($date) {
                $query
                ->where('item.valid_to', '>=', $date)
                ->orWhereNull('item.valid_to')
                ->orWhere('item.valid_to', '=', '');
            })
            ->orderBy('item.valid_from', 'desc')
            ->with('product')
            ->get();

        $inet = $tariffs->where('type', '=', 'Internet')->first();
        $tariff = $inet;

        // use voip tariff if no inet tariff exists or (inet is not bundled with voip and voip was created last)
        if (! $inet || ! $inet->bundled_with_voip) {
            // take last added (voip or inet)
            $voip = $tariffs->where('type', '=', 'Voip')->first();

            if ($voip) {
                $tariff = $inet && ($inet->valid_from >= $voip->valid_from) ? $inet : $voip;
            }
        }

        // Only take TV tariff if no inet and no voip tariff are valid
        if (! $tariff) {
            $tariff = $tariffs->where('type', 'TV')->first();
        }

        if (! $tariff) {
            return $ret;
        }

        // return end_of_term, last cancelation_day, tariff
        $ret['tariff'] = $tariff;

        return array_merge($ret, $tariff->getNextCancelationDate($date));
    }

    /**
     * Return the outstanding amount and the bootstrap class
     *
     * @return array [(Float) amount, (String) bsclass]
     */
    public function getResultingDebt()
    {
        // https://stackoverflow.com/questions/17210787/php-float-calculation-error-when-subtracting
        // return \Modules\OverdueDebts\Entities\Debt::where('contract_id', $this->id)
        //     ->groupBy('contract_id')
        //     ->select('missing_amount')
        //     ->sum('missing_amount');
        $block = false;
        if (config('overduedebts.debtMgmtType') == 'csv') {
            $block = $this->blockInetFromDebts();
        }

        $totalAmount = 0;
        foreach ($this->debts as $debt) {
            $totalAmount += $debt->missing_amount;
        }

        $bsclass = '';
        if ($block) {
            $bsclass = 'danger';
        } elseif ($totalAmount < 0) {
            $bsclass = 'success';
        } elseif ($totalAmount > 0) {
            $bsclass = 'warning';
        }

        return [
            'amount' => $totalAmount,
            'bsclass' => $bsclass,
        ];
    }

    /**
     * Check if the Contract has Debts that exceed the threshholds of the module configuration and therefore the internet has to be blocked
     *
     * @author Nino Ryschawy
     * @return bool     internet should be blocked
     */
    public function blockInetFromDebts()
    {
        if (! Module::collections()->has('OverdueDebts')) {
            return false;
        }

        $parser = new \Modules\OverdueDebts\Entities\DefaultTransactionParser;
        $debts = clone $this->debts;

        // Filter special debts to exclude (special voucher number and customer had less then 4 weeks to pay)
        foreach ($debts as $key => $debt) {
            $arr = $parser->searchNumbers($debt->voucher_nr);

            // In SWIFT bank file specific transactions have be to excluded, but in CSV they shall be considered
            // Date must be smaller than 4 weeks before now/created_at to make sure the customers have enough time to pay
            // Note: Consider created_at date as result could otherwise be marked as red, but internet was not blocked
            if ($arr['exclude'] && (strtotime($debt->date) > strtotime('-4 week', strtotime($debt->created_at)))) {
                $debts->forget($key);
            }
        }

        $posDebtCount = $debts->where('missing_amount', '>', 0)->count();

        $totalAmount = 0;
        foreach ($debts as $debt) {
            $totalAmount += $debt->missing_amount;
        }

        $highestIndicator = $debts->sortByDesc('indicator')->first();
        $highestIndicator = $highestIndicator ? $highestIndicator->indicator : 0;

        $conf = app()->make('OverdueDebtsConfig')->get();

        $block =
            // Amount threshold is exceeded
            ($conf->import_inet_block_amount && ($totalAmount >= $conf->import_inet_block_amount)) ||
            // More than max num of positive debts
            ($conf->import_inet_block_debts && ($posDebtCount >= $conf->import_inet_block_debts)) ||
            // Highest dunning indicator too high
            ($conf->import_inet_block_indicator && ($highestIndicator >= $conf->import_inet_block_indicator));

        return $block;
    }

    /**
     * Check if contract belongs to a group contract
     * A group contract is a contract just added to charge multiple contracts belonging to one administration by the TV fee
     *
     * NOTE: Returns
     *  false if contract is actually a group contract
     *  true if realty of contract belongs to a contract
     *
     * @author Nino Ryschawy
     * @return bool
     */
    public function belongsToGroupContract()
    {
        if (! Module::collections()->has('PropertyManagement')) {
            return false;
        }

        // Is group contract itself
        if ($this->realty_id) {
            return false;
        }

        $realty = $this->apartment_id ? $this->apartment->realty : $this->realty;

        if (! $realty) {
            return false;
        }

        // TODO: Check if contract is valid?
        return $realty->contract_id ? true : false;
    }

    /**
     * Synchronize address of Realty and Contract as contract address is used in e.g. invoice
     *
     * @param \Modules\PropertyManagement\Entities\Realty
     * @param array  of Contract IDs to update multiple Contracts by one DB query
     */
    public function updateAddressFromProperty($realty = null, $ids = [])
    {
        if (! Module::collections()->has('PropertyManagement')) {
            return;
        }

        if (! $realty) {
            $realty = $this->realty;
        }

        if (! $realty) {
            return;
        }

        self::whereIn('id', $ids ?: [$this->id])->update([
            'street' => $realty->street,
            'house_number' => $realty->house_nr,
            'zip' => $realty->zip,
            'city' => $realty->city,
            'district' => $realty->district,
        ]);
    }

    public function isGroupContract()
    {
        if (! \Module::collections()->has('PropertyManagement')) {
            return false;
        }

        return $this->contact_id ? true : false;
    }

    public function groupContractFilterQuery()
    {
        return "id in (SELECT id from contract WHERE IF(contact_id is null, '".trans('view.false')."', '".trans('view.true')."') like ?)";
    }

    /**
     * Get list of Apartments for select field of edit view
     *
     * @return array
     */
    public function getSelectableApartments()
    {
        if (! \Module::collections()->has('PropertyManagement')) {
            return [];
        }

        // All Apartments without Modems
        // TODO: Filter all Apartments with a valid Contract?
        $apartments = DB::table('apartment')->leftJoin('modem', 'modem.apartment_id', 'apartment.id')
            ->join('realty', 'realty.id', 'apartment.realty_id')
            ->whereNull('modem.id')
            ->whereNull('modem.deleted_at')
            ->select('apartment.*', 'realty.street', 'realty.house_nr', 'realty.city')
            ->get();

        $arr[null] = null;
        foreach ($apartments as $apartment) {
            $arr[$apartment->id] = \Modules\PropertyManagement\Entities\Apartment::labelFromData($apartment);
        }

        return $arr;
    }

    /**
     * Compose list of ordered Realties belonging to a group Contract to be displayed on an Invoice
     *
     * @return array
     */
    public function composeRealtyList()
    {
        if ($this->realties->isEmpty()) {
            return [];
        }

        // Initialise array
        foreach ($this->realties as $key => $realty) {
            $realties[$realty->street][] = str_replace(' ', '', strtolower($realty->house_nr));
        }

        // Sort array by street, even/odd housenr and housenr
        foreach ($realties as $street => $housenrs) {
            natsort($housenrs);

            foreach ($housenrs as $housenr) {
                preg_match('/\d*/', $housenr, $nr);

                if (! $nr[0]) {
                    Log::channel('billing')->error(trans('propertymanagement::messages.invoice.invalidRealtyHousenr', ['id' => $realty->id, 'nr' => $realty->house_nr, 'contractnr' => $contract->number]));

                    continue;
                }

                $nr = intval($nr[0]);

                if (isset($list[$street][$nr % 2 ? 'odd' : 'even'][$nr])) {
                    if (is_string($list[$street][$nr % 2 ? 'odd' : 'even'][$nr])) {
                        $list[$street][$nr % 2 ? 'odd' : 'even'][$nr] = [$list[$street][$nr % 2 ? 'odd' : 'even'][$nr], $housenr];
                    } else {
                        $list[$street][$nr % 2 ? 'odd' : 'even'][$nr][] = $housenr;
                    }
                } else {
                    $list[$street][$nr % 2 ? 'odd' : 'even'][$nr] = $housenr;
                }
            }
        }

        // Build groups in case buildings are adjacent (neighbours)
        foreach ($list as $street => $streets) {
            foreach ($streets as $evenOdd => $nrs) {
                $previous = $key = 0;

                foreach ($nrs as $nr => $block) {
                    if ($previous && ($previous + 2 != $nr)) {
                        $key++;
                    }

                    $groupList[$street][$evenOdd][$key][] = $block;

                    $previous = $nr;
                }
            }
        }

        // Take first and last house of each group
        foreach ($groupList as $street => $streets) {
            foreach ($streets as $evenOdd => $groups) {
                foreach ($groups as $group) {
                    if (count($group) == 1) {
                        if (is_string($group[0])) {
                            $objList[] = $street.' '.$group[0];
                        } else {
                            $separator = count($group[0]) == 2 ? ',' : '-';

                            $objList[] = $street.' '.$group[0][0].$separator.end($group[0]);
                        }

                        continue;
                    }

                    $separator = count($group) == 2 ? ',' : '-';

                    $first = is_string($group[0]) ? $group[0] : $group[0][0];
                    $last = is_string(end($group)) ? end($group) : end(end($group));

                    $objList[] = $street.' '.$first.$separator.$last;
                }
            }
        }

        return $objList;
    }
}
