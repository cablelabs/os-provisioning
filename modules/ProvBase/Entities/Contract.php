<?php

namespace Modules\ProvBase\Entities;

use Modules\ProvBase\Entities\Qos;
use Modules\BillingBase\Entities\SettlementRun;
use Modules\BillingBase\Entities\Invoice;
use Modules\BillingBase\Entities\NumberRange;

class Contract extends \BaseModel {

	// get functions for some address select options
	use \App\Models\AddressFunctionsTrait;

	// The associated SQL table for this Model
	public $table = 'contract';

	// temporary Variables filled during accounting command execution (Billing)
	public $expires = false;			// flag if contract expires this month - used in accounting command
	public $charge = [];				// total charge for each different Sepa Account with net and tax values


	// Add your validation rules here
	// TODO: dependencies of active modules (billing)
	public static function rules($id = null)
	{
		$rules = array(
			'number' => 'string|unique:contract,number,'.$id.',id,deleted_at,NULL',
			'number2' => 'string|unique:contract,number2,'.$id.',id,deleted_at,NULL',
			'number3' => 'string|unique:contract,number3,'.$id.',id,deleted_at,NULL',
			'number4' => 'string|unique:contract,number4,'.$id.',id,deleted_at,NULL',
			'firstname' => 'required',
			'lastname' => 'required',
			'street' => 'required',
			'zip' => 'required',
			'city' => 'required',
			'phone' => 'required',
			'email' => 'email',
			'birthday' => 'required|date',
			'contract_start' => 'date',
			'contract_end' => 'dateornull', // |after:now -> implies we can not change stuff in an out-dated contract
		);

		if (\PPModule::is_active('billingbase')) {
			$rules['costcenter_id'] = 'required|numeric|min:1';
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
				'index_header' => [$this->table.'.number', $this->table.'.firstname', $this->table.'.lastname', $this->table.'.company', $this->table.'.zip', $this->table.'.city', $this->table.'.district', $this->table.'.street', $this->table.'.house_number', $this->table.'.contract_start', $this->table.'.contract_end'],
				'header' =>  $this->number.' '.$this->firstname.' '.$this->lastname,
				'bsclass' => $bsclass,
				'order_by' => ['0' => 'asc']];

		if (\PPModule::is_active('billingbase'))
		{
			$ret['index_header'][] = 'costcenter.name';
			$ret['eager_loading'] = ['costcenter'];
			$ret['edit'] = ['costcenter.name' => 'get_costcenter_name'];
		}

		return $ret;
	}


	/**
	 * @return String  Bootstrap Color Class
	 */
	public function get_bsclass()
	{
		$bsclass = 'success';

		if (!$this->network_access)
		{
			$bsclass = 'active';

			// '$this->id' to dont check when index table header is determined!
			if ($this->id && $this->check_validity('now'))
				$bsclass = 'warning';
		}

		return $bsclass;
	}

	public function get_costcenter_name()
	{
		return $costcenter = $this->costcenter ? $this->costcenter->name : trans('messages.noCC');
	}

	// View Relation.
	public function view_has_many()
	{
		if (\PPModule::is_active('billingbase'))
		{
			$ret['Edit']['Modem'] 		= $this->modems;
			$ret['Edit']['Item']        = $this->items;
			$ret['Edit']['SepaMandate'] = $this->sepamandates;
		}

		$ret['Technical']['Modem'] = $this->modems;

		if (\PPModule::is_active('billingbase'))
		{
			$ret['Billing']['Item']['class'] 	= 'Item';
			$ret['Billing']['Item']['relation']	= $this->items;
			$ret['Billing']['SepaMandate']['class'] 	= 'SepaMandate';
			$ret['Billing']['SepaMandate']['relation']  = $this->sepamandates;
			$ret['Billing']['Invoice']['class'] 	= 'Invoice';
			$ret['Billing']['Invoice']['relation']  = $this->invoices;
			$ret['Billing']['Invoice']['options']['hide_delete_button'] = 1;
			$ret['Billing']['Invoice']['options']['hide_create_button'] = 1;
		}

		if (\PPModule::is_active('provvoipenvia'))
		{
			$ret['envia TEL']['EnviaContract']['class'] = 'EnviaContract';
			$ret['envia TEL']['EnviaContract']['relation'] = $this->enviacontracts;
			$ret['envia TEL']['EnviaContract']['options']['hide_create_button'] = 1;
			$ret['envia TEL']['EnviaContract']['options']['hide_delete_button'] = 1;

			$ret['envia TEL']['EnviaOrder']['class'] = 'EnviaOrder';
			$ret['envia TEL']['EnviaOrder']['relation'] = $this->_envia_orders;
			$ret['envia TEL']['EnviaOrder']['options']['delete_button_text'] = 'Cancel order at envia TEL';

			// TODO: auth - loading controller from model could be a security issue ?
			$ret['envia TEL']['envia TEL API']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
			$ret['envia TEL']['envia TEL API']['view']['vars']['extra_data'] = \Modules\ProvBase\Http\Controllers\ContractController::_get_envia_management_jobs($this);

			// for better navigation: show modems also in envia TEL blade
			$ret['envia TEL']['Modem']['class'] = 'Modem';
			$ret['envia TEL']['Modem']['relation'] = $this->modems;
		}

		if (\PPModule::is_active('ccc'))
		{
			$ret['Create Connection Infos']['Connection Information']['view']['view'] = 'ccc::prov.conn_info';
		}

		if (\PPModule::is_active('Ticketsystem'))
		{
			$ret['Edit']['Ticket'] = $this->tickets;
			$ret['Ticket']['Ticket'] = $this->tickets;
		}

		if (\PPModule::is_active('mail'))
		{
			$ret['Email']['Email'] = $this->emails;
		}

		return $ret;
	}


	/*
	 * Relations
	 */
	public function modems()
	{
		return $this->hasMany('Modules\ProvBase\Entities\Modem');
	}

	/**
	 * related enviacontracts
	 */
	public function enviacontracts() {
		if (!\PPModule::is_active('provvoipenvia')) {
			throw new \LogicException(__METHOD__.' only callable if module ProvVoipEnvia as active');
		}
		else {
			return $this->hasMany('Modules\ProvVoipEnvia\Entities\EnviaContract');
		}
	}


	/**
	 * Get the purchase tariff
	 */
	public function phonetariff_purchase() {

		return $this->belongsTo('Modules\ProvVoip\Entities\PhoneTariff', 'purchase_tariff');
	}


	/**
	 * Get the next purchase tariff
	 */
	public function phonetariff_purchase_next() {

		return $this->belongsTo('Modules\ProvVoip\Entities\PhoneTariff', 'next_purchase_tariff');
	}


	/**
	 * Get the sale tariff
	 */
	public function phonetariff_sale() {

		return $this->belongsTo('Modules\ProvVoip\Entities\PhoneTariff', 'voip_id');
	}


	/**
	 * Get the next sale tariff
	 */
	public function phonetariff_sale_next() {

		return $this->belongsTo('Modules\ProvVoip\Entities\PhoneTariff', 'next_voip_id');
	}

	/**
	 * Get relation to envia orders.
	 *
	 * @author Patrick Reichel
	 */
	protected function _envia_orders() {

		if (!\PPModule::is_active('provvoipenvia')) {
			throw new \LogicException(__METHOD__.' only callable if module ProvVoipEnvia as active');
		}

		return $this->hasMany('Modules\ProvVoipEnvia\Entities\EnviaOrder')->where('ordertype', 'NOT LIKE', 'order/create_attachment');

	}

	public function items()
	{
		return $this->hasMany('Modules\BillingBase\Entities\Item');
	}

	public function items_sorted_by_valid_from_desc()
	{
		return $this->hasMany('Modules\BillingBase\Entities\Item')->orderBy('valid_from', 'desc');
	}

	public function sepamandates()
	{
		return $this->hasMany('Modules\BillingBase\Entities\SepaMandate');
	}

	public function emails()
	{
		return $this->hasMany('Modules\Mail\Entities\Email');
	}

	public function get_email_count()
	{
		$tariff = $this->_get_valid_tariff_item_and_count('Internet');
		return $tariff['count'] ? $tariff['item']->product->email_count : 0;
	}

	public function costcenter()
	{
		return $this->belongsTo('Modules\BillingBase\Entities\CostCenter', 'costcenter_id');
	}

	public function salesman()
	{
		return $this->belongsTo('Modules\BillingBase\Entities\Salesman');
	}

	public function invoices()
	{
		return $this->hasMany('Modules\BillingBase\Entities\Invoice');
		// $srs  = SettlementRun::where('verified', '=', '0')->get(['id'])->pluck('id')->all();
		// $hide = $srs ? : 0;
		// return $this->hasMany('Modules\BillingBase\Entities\Invoice')->where('contract_id', '=', $this->id)->where('settlementrun_id', '!=', [$hide]);
	}

	public function cccauthuser()
	{
		return $this->hasOne('Modules\Ccc\Entities\CccAuthuser');
	}

	public function tickets()
	{
		return $this->hasMany('Modules\Ticketsystem\Entities\Ticket');
	}


    /**
     * Generate use a new user login password
     * This does not save the involved model
     */
    public function generate_password($length=10)
    {
        $this->password = \Acme\php\Password::generate_password($length);
    }


	/**
	 * Helper to get the contract number.
	 * As there is no hard coded contract number in database we have to use this mapper. The semantic meaning of number…number4 can be defined in global configuration.
	 *
	 * @author Patrick Reichel
	 *
	 * @todo: in this first step the relation is hardcoded within the function. Later on we have to check the mapping against the configuration.
	 * @return current contract number
	 */
	public function contract_number() {

		$contract_number = $this->number;

		return $contract_number;

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
	public function customer_number() {

		if (boolval($this->number3) && (\Str::lower($this->number3 != 'n/a'))) {
			$customer_number = $this->number3;
		}
		else {
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
	public function customer_number_legacy() {

		if (boolval($this->number4) && (\Str::lower($this->number4 != 'n/a'))) {
			$customer_number_lecacy = $this->number4;
		}
		else {
			$customer_number_lecacy = $this->number2;
		}

		return $customer_number_lecacy;

	}

	/**
	 * Helper to get all phonenumbers related to contract.
	 *
	 * @author Patrick Reichel
	 */
	public function related_phonenumbers() {

		// if voip module is not active: there can be no phonenumbers
		if (!\PPModule::is_active('ProvVoip')) {
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
	private function _date_to_carbon ($date)
	{
		// createFromFormat crashes if nothing given
		if (!boolval($date)) {
			return null;
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
	private function _date_null ($date)
	{
		if (!boolval($date))
			return True;

		if (is_string($date)) {
			return (\Str::startswith($date, '0000'));
		}

		// Carbon object
		return !($date->year > 1900);
	}


	/**
	 * The Daily Scheduling Function
	 *
	 * Tasks:
	 *  1. Check if $this contract end date is expired -> disable network_access
	 *  2. Check if $this is a new contract and activate it -> enable network_access
	 *  3. Change QoS id and Voip id if actual valid (billing-) tariff changes
	 *
	 * @TODO try to avoid the use of multiple saves, instead use one save at the end
	 *
	 * @return none
	 * @author Torsten Schmidt, Nino Ryschawy, Patrick Reichel
	 */
	public function daily_conversion()
	{
		\Log::Debug('Starting daily conversion for contract '.$this->number, [$this->id]);

		if (!\PPModule::is_active('Billingbase')) {

			$this->_update_network_access_from_contract();
		}
		else {

			// Task 3: Check and possibly update item's valid_from and valid_to dates
			$this->_update_inet_voip_dates();

			$this->load('items');
			// $this->fresh();

			// Task 4: Check and possibly change product related data (qos_id, voip, purchase_tariff)
			// for this contract depending on the start/end times of its items
			$this->update_product_related_data($this->items);

			// NOTE: Keep this order! - update network access after all adaptions are made
			// Task 1 & 2 included
			$this->_update_network_access_from_items();

			if(\PPModule::is_active('mail'))
				$this->_update_email_index();

			// commented out by par for reference ⇒ if all is running this can savely be removed
			/* $qos_id = ($tariff = $this->get_valid_tariff('Internet')) ? $tariff->product->qos_id : 0; */

			/* if ($this->qos_id != $qos_id) */
			/* { */
			/* 	\Log::Info("daily: contract: changed qos_id (tariff) to $qos_id for Contract ".$this->number, [$this->id]); */
			/* 	$this->qos_id = $qos_id; */
			/* 	$this->save(); */
			/* 	$this->push_to_modems(); */
			/* } */

			/* $voip_id = ($tariff = $this->get_valid_tariff('Voip')) ? $tariff->product->voip_sales_tariff_id : 0; */

			/* if ($this->voip_id != $voip_id) */
			/* { */
			/* 	\Log::Info("daily: contract: changed voip_id (tariff) to $voip_id for Contract ".$this->number, [$this->id]); */
			/* 	$this->voip_id = $voip_id; */
			/* 	$this->save(); */
			/* } */
		}
	}


	/**
	 * This enables/disables network_access according to start and end date of the contract.
	 * Used if billing is disabled.
	 *
	 * @author Torsten Schmidt
	 */
	protected function _update_network_access_from_contract() {

		$now   = \Carbon\Carbon::now();

		// Task 1: Check if $this contract end date is expired -> disable network_access
		if ($this->contract_end) {
			$end  = $this->_date_to_carbon($this->contract_end);
			if ($end->lt($now) && !$this->_date_null($end) && $this->network_access == 1)
			{
				\Log::Info('daily: contract: disable based on ending contract date for '.$this->id);

				$this->network_access = 0;
				$this->save();
			}
		}

		// Task 2: Check if $this is a new contract and activate it -> enable network_access
		// Note: to avoid enabling contracts which are disabled manually, we also check if
		//       maximum time beetween start contract and now() is not older than 1 day.
		// Note: This requires the daily scheduling to run well
		//       Otherwise the contracts must be enabled manually
		// TODO: give them a good testing
		if ($this->contract_start) {
			$start = $this->_date_to_carbon($this->contract_start);
			if ($start->lt($now) && !$this->_date_null($start) && $start->diff($now)->days <= 1 && $this->network_access == 0)
			{
				\Log::Info('daily: contract: enable contract based on start contract date for '.$this->id);

				$this->network_access = 1;
				$this->save();
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
	protected function _update_network_access_from_items() {

		// check if DB update is required
		$contract_changed = False;

		$active_tariff_info_internet = $this->_get_valid_tariff_item_and_count('Internet');
		$active_tariff_info_voip = $this->_get_valid_tariff_item_and_count('Voip');

		$active_count_internet = $active_tariff_info_internet['count'];
		$active_count_voip = $active_tariff_info_voip['count'];

		if (!$this->check_validity('Now'))
		{
			// invalid contract - disable every access
			if ($this->network_access) {
				$this->network_access = 0;
				$contract_changed = True;
				\Log::Info('daily: contract: disabling network_access based on active internet/voip items for contract '.$this->id);
			}

			if ($this->telephony_only) {
				$this->telephony_only = 0;
				$contract_changed = True;
				\Log::info('daily: contract: Unset telephony_only as contract is invalid!', [$this->id]);
			}
		}
		else if (!$active_count_internet)
		{
			// valid contract, but no valid internet tariff
			if ($this->network_access) {
				$this->network_access = 0;
				$contract_changed = True;
				\Log::Info('daily: contract: disabling network_access based on active internet/voip items for contract '.$this->id);
			}

			if ($active_count_voip && !$this->telephony_only) {
				$this->telephony_only = 1;
				$contract_changed = True;
				\Log::Info('daily: contract: switch to telephony_only', [$this->id]);
			}

			else if (!$active_count_voip && $this->telephony_only) {
				$this->telephony_only = 0;
				$contract_changed = True;
				\Log::Info('daily: contract: switch from telephony_only to internet + telephony tariff', [$this->id]);
			}
		}
		else
		{
			// valid contract and valid internet tariff
			if ($this->telephony_only) {
				$this->telephony_only = 0;
				$contract_changed = True;
				\Log::info('daily: contract: unset telephony_only as customer has internet tariff now', [$this->id]);
			}

			if (!$this->network_access) {
				$this->network_access = 1;
				$contract_changed = True;
				\Log::Info('daily: contract: enabling network_access based on active internet/voip items for contract '.$this->id);
			}
		}

		if ($contract_changed)
			$this->save();

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
	 * @author Patrick Reichel
	 *
	 * @return null
	 */
	protected function _update_inet_voip_dates() {

		// items only exist if Billingbase is enabled
		if (!\PPModule::is_active('Billingbase')) {
			return;
		}

		// get tomorrow and today as Carbon objects – so they can directly be compared to the dates at items
		$tomorrow = \Carbon\Carbon::tomorrow();
		$today = \Carbon\Carbon::today();

		// check for each item on contract
		// attention: update youngest valid_from items first (to avoid problems in relation with
		// ItemObserver::update() which else set valid_to smaller than valid_from in some cases)!
		// and to avoid “Multipe valid tariffs active” warning

		foreach ($this->items_sorted_by_valid_from_desc as $item) {

			$type = isset($item->product) ? $item->product->type : '';

			if (!in_array($type, ['Voip', 'Internet']))
				continue;

			// flag to decide if item has to be saved at the end of the loop
			$item_changed = False;

			// if the startdate is fixed: ignore
			if (!boolval($item->valid_from_fixed)) {
				// set to tomorrow if there is a start date but this is less then tomorrow
				if (!$this->_date_null($item->valid_from)) {
					$from = $this->_date_to_carbon($item->valid_from);
					if ($from->lt($tomorrow)) {
						$new_date = $tomorrow->toDateString();
						$item->valid_from = $new_date;
						$item_changed = True;
						\Log::Info("contract: changing item ".$item->id." (".$item->product->name.") valid_from to ".$new_date." for Contract ".$this->number, [$this->id]);
					}
				}
			}

			// if the enddate is fixed: ignore
			if (!boolval($item->valid_to_fixed)) {
				// set to today if there is an end date less than today
				if (!$this->_date_null($item->valid_to)) {
					$to = $this->_date_to_carbon($item->valid_to);
				    if ($to->lt($today)) {
						$new_date = $today->toDateString();
						$item->valid_to = $new_date;
						$item_changed = True;
						\Log::Info("contract: changing item ".$item->id." (".$item->product->name.") valid_to to ".$new_date." for Contract ".$this->number, [$this->id]);
					}
				}
			}

			// finally: save the change(s)
			if ($item_changed) {
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
		if(!$cnt) {
			foreach($this->emails as $email) {
				$email->index = 0;
				$email->save();
			}
			return;
		}

		// remove all email indices, which are already in use
		$used = [];
		foreach($this->emails as $email)
			$used[] = $email->index;
		$avail = array_diff(range($cnt, 1), $used);

		// try to fit all email indices into available slots
		foreach($this->emails as $email)
			if($email->index > $cnt) {
				$email->index = $avail ? array_pop($avail) : 0;
				$email->save();
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
		if (\PPModule::is_active('Billingbase'))
			return;

		$contract_changed = False;

		// Tariff: monthly Tariff change – "Tarifwechsel"
		if (
			($this->next_qos_id > 0)
			&&
			($this->qos_id != $this->next_qos_id)
		) {
			\Log::Info('monthly: contract: change Tariff for '.$this->id.' from '.$this->qos_id.' to '.$this->next_qos_id);
			$this->qos_id = $this->next_qos_id;
			$this->next_qos_id = 0;
			$contract_changed = True;
		}

		// VOIP: monthly VOIP change
		if ($this->next_voip_id > 0)
		{
			\Log::Info('monthly: contract: change VOIP-ID for '.$this->id.' from '.$this->voip_id.' to '.$this->next_voip_id);
			$this->voip_id = $this->next_voip_id;
			$this->next_voip_id = 0;
			$contract_changed = True;
		}

		if ($contract_changed) {
			$this->save();
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
	public function get_valid_tariff($type) {
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
	public function get_valid_tariff_count($type) {
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
		if (!\PPModule::is_active('Billingbase'))
			return ['item' => null, 'count' => 0];

		$last = $count = 0;
		$tariff = null;			// item

		$tariffs = $this->items()
			->join('product as p', 'item.product_id', '=', 'p.id')
			->select('item.*', 'p.*', 'item.id as id')
			->where('type', '=', $type)->where('valid_from', '<=', date('Y-m-d'))
			->get();

		if ($tariffs->isEmpty())
			return ['item' => null, 'count' => 0];

		foreach ($tariffs as $item)
		{
			if (!$item->check_validity('Now'))
				continue;

			$count++;

			$start = $item->get_start_time();
			if ($start > $last)
			{
				$tariff = $item;
				$last   = $start;
			}
		}

		// This is an error! There should only be one active item per type and contract
		if ($count > 1) {
			\Log::Error('There are '.$count.' active items of product type '.$type.' assigned to contract '.$this->number.' ['.$this->id.'].');
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
	public function update_product_related_data($items) {

		// set qos_id to zero - this is necessary because one could accidentially activate network access on modem page and the customer now has the old tariff activated however this tariff is not assigned anymore
		//  (2) better extract voip & inet items directly!? - why iterate over all items? is call of get_valid_tariff() multiple times not very bad ??
		// Better to set qos_id to the one with the lowest data rates ?
		// $qos_id = Qos::orderBy('us_rate_max')->orderBy('ds_rate_max')->first()->id;
		if (!count($items) && boolval($this->qos_id))
		{
			$this->qos_id = 0;
			$this->save();
			return;
		}

		$valid_tariff = false;

		foreach ($items as $item) {

			// a given item can be null – check and ignore
			if (!$item)
				continue;

			$type = isset($item->product) ? $item->product->type : '';
			// process only particular product types
			if (!in_array($type, ['Voip', 'Internet']))
				continue;

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
				$valid_tariff_info = $this->_get_valid_tariff_item_and_count($type);

				$valid_tariff = true;

				if ($valid_tariff_info['count'] > 1) {
					// this should never occur!!
					if ($valid_tariff_info['item']->id != $item->id) {
						\Log::Warning('Using newer item '.$valid_tariff_info['item']->id.' instead of '.$item->id.' to update current data on contract '.$this->number.' ['.$this->id.'].');
					}
					$this->_update_product_related_current_data($valid_tariff_info['item']);

				}
				else {
					// default case
					$this->_update_product_related_current_data($item);
				}
			}
			// check if information is for the future
			// this should be save because there is max. one of each type allowed
			// but if there is more than one: no problem – in worst case we overwrite next_* values
			// multiple times
			elseif ($item->valid_from > date('Y-m-d')) {
				$this->_update_product_related_future_data($item);
			}
			else {
				// items finished before today don't update contracts!
				continue;
			}

		}

		if (!$valid_tariff)
		{
			$this->qos_id = 0;
			$this->save();
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
	 */
	protected function _update_product_related_current_data($item) {

		$contract_changed = False;

		if ($item->product->type == 'Voip') {

			// check if there are changes in state for voip_id and purchase_tariff
			if ($this->voip_id != $item->product->voip_sales_tariff_id) {
				$this->voip_id = $item->product->voip_sales_tariff_id;
				$contract_changed = True;
				\Log::Info("contract: changing voip_id to ".$this->voip_id." for contract ".$this->number, [$this->id]);
			}
			if ($this->purchase_tariff != $item->product->voip_purchase_tariff_id) {
				$this->purchase_tariff = $item->product->voip_purchase_tariff_id;
				$contract_changed = True;
				\Log::Info("contract: changing purchase_tariff to ".$this->purchase_tariff." for contract ".$this->number, [$this->id]);
			}

			if ($contract_changed) {
				$this->save();
			}
		}

		if ($item->product->type == 'Internet') {

			if ($this->qos_id != $item->product->qos_id) {
				$this->qos_id = $item->product->qos_id;
				$contract_changed = True;
				\Log::Info("contract: changing  qos_id to ".$this->qos_id." for contract ".$this->number, [$this->id]);
			}
		}

		if ($contract_changed) {
			$this->save();
		}
	}


	/**
	 * Check for (and possibly perform) product related changes in contract for the next month
	 *
	 * @author Patrick Reichel
	 *
	 * @param $item to be analyzed
	 *
	 * @return null
	 */
	protected function _update_product_related_future_data($item) {

		$contract_changed = False;

		if ($item->product->type == 'Voip') {

			// check if there are changes in state for voip_id and purchase_tariff
			if ($this->next_voip_id != $item->product->voip_sales_tariff_id) {
				$this->next_voip_id = $item->product->voip_sales_tariff_id;
				$contract_changed = True;
				\Log::Info("contract: changing next_voip_id to ".$this->next_voip_id." for contract ".$this->number, [$this->id]);
			}
			if ($this->next_purchase_tariff != $item->product->voip_purchase_tariff_id) {
				$this->next_purchase_tariff = $item->product->voip_purchase_tariff_id;
				$contract_changed = True;
				\Log::Info("contract: changing next_purchase_tariff to ".$this->next_purchase_tariff." for contract ".$this->number, [$this->id]);
			}
		}

		if ($item->product->type == 'Internet') {

			if ($this->next_qos_id != $item->product->qos_id) {
				$this->next_qos_id = $item->product->qos_id;
				$contract_changed = True;
				\Log::Info("contract: changing next_qos_id to ".$this->next_qos_id." for contract ".$this->number, [$this->id]);
			}
		}

		if ($contract_changed) {
			$this->save();
		}
	}


	/*
	 * Push all settings from Contract layer to the related child Modems (for $this)
	 * This includes: network_access, qos_id
	 *
	 * Note: We call this function from Observer context so a change of the explained
	 *       fields will push this changes to the child Modems
	 * Note: This allows only 1 tariff qos_id for all modems
	 *
	 * @return: none
	 * @author: Torsten Schmidt
	 */
	public function push_to_modems()
	{
		$changes = $this->getDirty();

		// TODO: Speed-up: Could this be done with a single eloquent update statement ?
		//       Note: This requires to use the Eloquent Context to run all Observers
		//       an to rebuild and restart the involved modems
		foreach ($this->modems as $modem)
		{
			$modem->network_access = $this->network_access;
			$modem->qos_id = $this->qos_id;
			$modem->save();

			if (isset($changes['telephony_only']) && !$modem->needs_restart()) {
				$modem->restart_modem();
				$modem->make_configfile();
			}
		}
	}


	/**
	 * Helper to map database numberX fields to description
	 *
	 * @author Patrick Reichel
	 */
	public function get_column_description($col_name) {

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

		Contract::observe(new ContractObserver);
	}


	/**
	 * Returns start time of item - Note: contract_start field has higher priority than created_at
	 *
	 * @return integer 		time in seconds after 1970
	 */
	public function get_start_time()
	{
		$date = $this->contract_start && $this->contract_start != '0000-00-00' ? $this->contract_start : $this->created_at->toDateString();
		return strtotime($date);
	}


	/**
	 * Returns start time of item - Note: contract_start field has higher priority than created_at
	 *
	 * @return integer 		time in seconds after 1970
	 */
	public function get_end_time()
	{
		return $this->contract_end && $this->contract_end != '0000-00-00' ? strtotime($this->contract_end) : null;
	}


	/**
	 * Returns valid sepa mandate for specific timespan
	 *
	 * @param 	String 		Timespan - LAST (!!) 'year'/'month' or 'now
	 * @param 	Integer 	If Set only Mandates related to specific SepaAccount are considered (related via CostCenter)
	 * @return 	Object 		Valid Sepa Mandate with latest start date
	 *
	 * @author Nino Ryschawy
	 */
	public function get_valid_mandate($timespan = 'now', $sepaaccount_id = 0)
	{
		$mandate = null;
		$last 	 = 0;

		foreach ($this->sepamandates as $m)
		{
			if (!is_object($m))
				continue;

			if ($m->disable || !$m->check_validity($timespan))
				continue;

			if ($m->costcenter xor $sepaaccount_id)
				continue;

			if ($sepaaccount_id && ($m->costcenter->sepaaccount->id != $sepaaccount_id))
				continue;

			if ($mandate)
				\Log::warning("SepaMandate: Multiple valid mandates active for Contract $this->number", [$this->id]);

			$start = $m->get_start_time();

			if ($start > $last)
			{
				$mandate = $m;
				$last   = $start;
			}

		}

		return $mandate;
	}


	/**
	 * Get Contracts next possible cancelation dates - dependent of tariff type (default: Internet)
	 *
	 * @author Nino Ryschawy
	 *
	 * @param String 	Internet|Voip|TV  - Type of which the contracts next possible cancelation date is dependent
	 * @return Array 	[end of term, next possible cancelation date, tariff]
	 *
	 * NOTE: if cancelation date is empty -> customer has no tariff or has already canceled
	 */
	public function get_next_cancel_date($type = 'Internet')
	{
		if (!in_array($type, ['Internet', 'Voip', 'TV']))
			throw new Exception("No Tariff Type");

		// get last tariff
		$tariff = $this->items()
			->join('product as p', 'item.product_id', '=', 'p.id')
			->where('type', '=', $type)
			->where('valid_from_fixed', '=', 1)
			->orderBy('valid_from', 'desc')
			->first();

		// check voip and tv tariffs also
		if (!$tariff)
		{
			switch ($type) {
				case 'Internet':
					$type = 'Voip'; break;

				case 'Voip':
					$type = 'TV'; break;

				case 'TV':
					// customer has no tariff
					return array(
						'end_of_term' => '',
						'cancelation_day' => '',
						);
			}

			return $this->get_next_cancel_date($type);
		}

		// last tariff already canceled -> contract canceled
		if ($tariff->get_end_time()) {
			return array(
				'end_of_term' => $tariff->valid_to,
				'cancelation_day' => '',
				'tariff' => $tariff,
				);
		}

		return array_merge($tariff->get_next_cancel_date(), ['tariff' => $tariff]);
	}

}


/**
 * Observer Class
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class ContractObserver
{

	public function creating($contract)
	{
		if (!\PPModule::is_active('billingbase'))
		{
			$contract->sepa_iban = strtoupper($contract->sepa_iban);
			$contract->sepa_bic  = strtoupper($contract->sepa_bic);
		}
	}

	public function created($contract)
	{
		$contract->push_to_modems(); 	// should not run, because a new added contract can not have modems..
	}

	public function updating($contract)
	{
		$original_number = $contract->getOriginal('number');
		$original_costcenter_id = $contract->getOriginal('costcenter_id');

		if (!\PPModule::is_active('billingbase'))
		{
			$contract->sepa_iban = strtoupper($contract->sepa_iban);
			$contract->sepa_bic  = strtoupper($contract->sepa_bic);
		}
	}

	public function updated ($contract)
	{
		if (!$contract->observer_enabled)
			return;

		$contract->push_to_modems();

		if ($contract['original'])
		{
			// Note: implement this commented way if there are more checkings for better code structure - but this reduces performance on one of the most used functions of the user!
			// $changed_fields = $contract->getDirty();

			// Note: isset is way faster regarding the performance than array_key_exists, but returns false if value of key is null which is not important here - See upmost comment on: http://php.net/manual/de/function.array-key-exists.php
			// if (isset($changed_fields['number']))
			if ($contract->number != $contract['original']['number'])
			{
				// change customer information - take care - this automatically changes login psw of customer
				if ($customer = $contract->cccauthuser)
					$customer->update();
			}

			// if (isset($changed_fields['contract_start']) || isset($changed_fields['contract_end']))
			if ($contract->contract_start != $contract['original']['contract_start'] || $contract->contract_end != $contract['original']['contract_end'])
			{
				$contract->daily_conversion();

				if (\PPModule::is_active('billingbase') && $contract->contract_end && $contract->contract_end != $contract['original']['contract_end'])
				{
					// Alert if end is lower than tariffs end of term
					$ret = $contract->get_next_cancel_date();

					if ($ret['cancelation_day'] && $contract->contract_end < $ret['end_of_term'])
						\Session::put('alert', trans('messages.contract_early_cancel', ['date' => $ret['end_of_term']]));
				}
			}

		}

	}


	public function saved ($contract)
	{
		if (!$contract->observer_enabled)
			return;

		$contract->push_to_modems();
	}

}


/**
 * Base updater for all data that is related to orders and phonenumbers
 *
 * @author Patrick Reichel
 */
abstract class VoipRelatedDataUpdater {

	// the modules that have to be active to instantiate
	// set to empty array if no modules are needed
	protected $modules_to_be_active = ['OverloadThisByTheNeededModules'];

	// Helper flag; set to true if something related to given contract has to be updated
	protected $has_to_be_updated = True;


	/**
	 * Constructor
	 *
	 * @author Patrick Reichel
	 */
	public function __construct($contract_id) {

		if (!$this->_check_modules()) {
			throw new \RuntimeException('Cannot use class '.__CLASS__.' because at least one of the following modules is not active: '.implode(', ', $this->modules_to_be_active));
		}

		$this->contract = Contract::findOrFail($contract_id);
	}


	/**
	 * Check if all needed modules are active
	 *
	 * @author Patrick Reichel
	 */
	protected function _check_modules() {

		foreach ($this->modules_to_be_active as $module) {
			if (!\PPModule::is_active($module)) {
				return false;
			}
		}

		return true;
	}

}

/**
 * Updater using EnviaOrders as data base.
 *
 * @author Patrick Reichel
 */
class VoipRelatedDataUpdaterByEnvia extends VoipRelatedDataUpdater {

	protected $modules_to_be_active = ['ProvVoipEnvia'];

	/**
	 * Constructor
	 *
	 * @author Patrick Reichel
	 */
	public function __construct($contract_id) {

		parent::__construct($contract_id);

		/* dd(__FILE__, __LINE__, $this->contract); */
	}
}
