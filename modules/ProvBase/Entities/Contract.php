<?php

namespace Modules\ProvBase\Entities;

use Modules\ProvBase\Entities\Qos;

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
		return array(
			'number' => 'integer|unique:contract,number,'.$id.',id,deleted_at,NULL',
			'number2' => 'string|unique:contract,number2,'.$id.',id,deleted_at,NULL',
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
			'sepa_iban' => 'iban',
			'sepa_bic' => 'bic',
			);
	}


	// Name of View
	public static function view_headline()
	{
		return 'Contract';
	}

	// link title in index view
	public function view_index_label()
	{
		$bsclass = 'success';

		if ($this->network_access == 0)
			$bsclass = 'danger';

		return ['index' => [$this->number, $this->firstname, $this->lastname, $this->zip, $this->city, $this->street],
				'index_header' => ['Contract Number', 'Firstname', 'Lastname', 'Postcode', 'City', 'Street'],
				'bsclass' => $bsclass,
				'header' => $this->number.' '.$this->firstname.' '.$this->lastname];

		// deprecated ?
		$old = $this->number2 ? ' - (Old Nr: '.$this->number2.')' : '';
		return $this->number.' - '.$this->firstname.' '.$this->lastname.' - '.$this->city.$old;
	}

	// View Relation.
	public function view_has_many()
	{
		if (\PPModule::is_active('billingbase'))
		{
			$ret['Base']['Modem'] = $this->modems;
			$ret['Base']['Item']        = $this->items;
			$ret['Base']['SepaMandate'] = $this->sepamandates;
		}

		$ret['Technical']['Modem'] = $this->modems;

		if (\PPModule::is_active('billingbase'))
		{
			$ret['Billing']['Item']        = $this->items;
			$ret['Billing']['SepaMandate'] = $this->sepamandates;
		}

		if (\PPModule::is_active('provvoipenvia'))
		{
			$ret['Envia']['EnviaOrder']['class'] = 'EnviaOrder';
			$ret['Envia']['EnviaOrder']['relation'] = $this->_envia_orders;

			// TODO: auth - loading controller from model could be a security issue ?
			$ret['Envia']['Envia API']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
			$ret['Envia']['Envia API']['view']['vars']['extra_data'] = \Modules\ProvBase\Http\Controllers\ContractController::_get_envia_management_jobs($this);
		}

		if (\PPModule::is_active('ccc'))
		{
			$ret['Create Connection Infos']['Connection Information']['view']['view'] = 'ccc::prov.conn_info';
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
	 * Get the purchase tariff
	 */
	public function phonetariff_purchase() {

		if ($this->voip_enabled) {
			return $this->belongsTo('Modules\ProvVoip\Entities\PhoneTariff', 'purchase_tariff');
		}
		else {
			return null;
		}
	}


	/**
	 * Get the next purchase tariff
	 */
	public function phonetariff_purchase_next() {

		if ($this->voip_enabled) {
			return $this->belongsTo('Modules\ProvVoip\Entities\PhoneTariff', 'next_purchase_tariff');
		}
		else {
			return null;
		}
	}


	/**
	 * Get the sale tariff
	 */
	public function phonetariff_sale() {

		if ($this->voip_enabled) {
			return $this->belongsTo('Modules\ProvVoip\Entities\PhoneTariff', 'voip_id');
		}
		else {
			return null;
		}
	}


	/**
	 * Get the next sale tariff
	 */
	public function phonetariff_sale_next() {

		if ($this->voip_enabled) {
			return $this->belongsTo('Modules\ProvVoip\Entities\PhoneTariff', 'next_voip_id');
		}
		else {
			return null;
		}
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
		if (\PPModule::is_active('billingbase'))
			return $this->hasMany('Modules\BillingBase\Entities\Item');
		return null;
	}

	public function sepamandates()
	{
		if (\PPModule::is_active('billingbase'))
			return $this->hasMany('Modules\BillingBase\Entities\SepaMandate');
		return null;
	}

	public function costcenter()
	{
		if (\PPModule::is_active('billingbase'))
			return $this->belongsTo('Modules\BillingBase\Entities\CostCenter', 'costcenter_id');
		return null;
	}

	public function salesman()
	{
		if (\PPModule::is_active('billingbase'))
			return $this->belongsTo('Modules\BillingBase\Entities\Salesman');
		return null;
	}

	public function cccauthuser()
	{
		if (\PPModule::is_active('ccc'))
			return $this->hasOne('Modules\Ccc\Entities\CccAuthuser');
		return null;
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
	 * Helper to get the customer number.
	 * As there is no hard coded customer number in database we have to use this mapper. The semantic meaning of number…number4 can be defined in global configuration.
	 *
	 * @author Patrick Reichel
	 *
	 * @todo: in this first step the relation is hardcoded within the function. Later on we have to check the mapping against the configuration.
	 * @return current customer number
	 */
	public function customer_number() {

		if (boolval($this->number3)) {
			$customer_number = $this->number3;
		}
		else {
			$customer_number = $this->number;
		}

		return $customer_number;

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
	 */
	private function _date_null ($date)
	{
		if (!$date)
			return True;

		return !($date->year > 1900);
	}


	/*
	 * The Daily Scheduling Function
	 *
	 * Tasks:
	 *  1. Check if $this contract end date is expired -> disable network_access
	 *  2. Check if $this is a new contract and activate it -> enable network_access
	 *  3. Change QoS id and Voip id if actual valid (billing-) tariff changes
	 *
	 * TODO: try to avoid the use of multiple saves, instead use one save at the end
	 *
	 * @return: none
	 * @author: Torsten Schmidt, Nino Ryschawy, Patrick Reichel
	 */
	public function daily_conversion()
	{
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


		// Task 3: Change qos and voip id when tariff changes
		if (!\PPModule::is_active('Billingbase'))
			return;

		$qos_id = ($tariff = $this->get_valid_tariff('Internet')) ? $tariff->product->qos_id : 0;

		if ($this->qos_id != $qos_id)
		{
			\Log::Info("daily: contract: changed qos_id (tariff) to $qos_id for Contract ".$this->number, [$this->id]);
			$this->qos_id = $qos_id;
			$this->save();
			$this->push_to_modems();
		}

		$voip_id = ($tariff = $this->get_valid_tariff('Voip')) ? $tariff->product->voip_sales_tariff_id : 0;

		if ($this->voip_id != $voip_id)
		{
			\Log::Info("daily: contract: changed voip_id (tariff) to $voip_id for Contract ".$this->number, [$this->id]);
			$this->voip_id = $voip_id;
			$this->save();
		}

		$today = \Carbon\Carbon::today();
		$yesterday = \Carbon\Carbon::yesterday();
		foreach ($this->items as $item) {

			$item_changed = False;

			// if the startdate is fixed: ignore
			if (!$item->valid_from_fixed) {
				// set to today if there is a start date but this is less then today
				if ($item->valid_from) {
					$from = $this->_date_to_carbon($item->valid_from);
					if (!$this->_date_null($from) && $from->lt($today)) {
						$item->valid_from = $today;
						$item_changed = True;
					}
				}
			}

			// if the enddate is fixed: ignore
			if (!$item->valid_to_fixed) {
				// set to yesterdey if there is an end date less than yesterday
				if ($item->valid_to) {
					$to = $this->_date_to_carbon($item->valid_to);
				    if (!$this->_date_null($to) && $to->lt($yesterday)) {
						$item->valid_to = $yesterday;
						$item_changed = True;
					}
				}
			}

			// finally: save the change
			if ($item_changed) {
				$item->save();
			}
		}

	}


	/*
	 * The Monthly Scheduling Function
	 *
	 * Tasks:
	 *  1. monthly QOS transition / change
	 *  2. monthly VOIP transition / change
	 *
	 * TODO: try to avoid the use of multiple saves, instead use one save at the end
	 *
	 * @return: none
	 * @author: Torsten Schmidt
	 */
	public function monthly_conversion()
	{
		// with billing module -> daily conversion
		if (\PPModule::is_active('Billingbase'))
			return;

		// Tariff: monthly Tariff change – "Tarifwechsel"
		if ($this->next_qos_id > 0)
		{
			\Log::Info('monthly: contract: change Tariff for '.$this->id.' from '.$this->qos_id.' to '.$this->next_qos_id);
			$this->qos_id = $this->next_qos_id;
			$this->next_qos_id = 0;

			$this->save();
		}

		// VOIP: monthly VOIP change
		if ($this->next_voip_id > 0)
		{
			\Log::Info('monthly: contract: change VOIP-ID for '.$this->id.' from '.$this->voip_id.' to '.$this->next_voip_id);
			$this->voip_id = $this->next_voip_id;
			$this->next_voip_id = 0;

			$this->save();
		}
	}


	/**
	 * Returns (qos/voip id of the) last created actual valid tariff assigned to this contract
	 *
	 * @param Enum 	$type 	product type (e.g. 'Internet', 'Voip', 'TV')
	 * @return object 	item
	 * @author Nino Ryschawy
	 */
	public function get_valid_tariff($type)
	{
		if (!\PPModule::is_active('Billingbase'))
			return null;

		$prod_ids = \Modules\BillingBase\Entities\Product::get_product_ids($type);
		if (!$prod_ids)
			return null;

		$last 	= 0;
		$tariff = null;			// item
// dd($prod_ids, $this->items);
		foreach ($this->items as $item)
		{
			if (in_array($item->product->id, $prod_ids) && $item->check_validity('now'))
			{
				if ($tariff)
					\Log::warning("Multiple valid $type tariffs active for Contract ".$this->number, [$this->id]);

				$start = $item->get_start_time();
				if ($start > $last)
				{
					$tariff = $item;
					$last   = $start;
				}
			}
		}

		return $tariff;
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
		// TODO: Speed-up: Could this be done with a single eloquent update statement ?
		//       Note: This requires to use the Eloquent Context to run all Observers
		//       an to rebuild and restart the involved modems
		foreach ($this->modems as $modem)
		{
			$modem->network_access = $this->network_access;
			$modem->qos_id = $this->qos_id;
			$modem->save();
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
	 * @param String 	Timespan - LAST (!!) 'year'/'month' or 'now
	 * @return Object 	Sepa Mandate
	 *
	 * @author Nino Ryschawy
	 */
	public function get_valid_mandate($timespan = 'now')
	{
		$mandate = null;
		$last 	 = 0;

		foreach ($this->sepamandates as $m)
		{
			if (!is_object($m) || !$m->check_validity($timespan))
				continue;

			if ($mandate)
				\Log::warning("Multiple valid Sepa Mandates active for Contract ".$this->number, [$this->id]);

			$start = $m->get_start_time();

			if ($start > $last)
			{
				$mandate = $m;
				$last   = $start;
			}

		}

		return $mandate;
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

	// TODO: move to global config
	// start contract numbers from 10000 - TODO: move to global config
	protected $num = 490000;

	public function creating($contract)
	{
		$contract->number = $contract->id - $this->num;

		// Note: this is only needed when Billing Module is not active - TODO: proof with future static function
		$contract->sepa_iban = strtoupper($contract->sepa_iban);
		$contract->sepa_bic  = strtoupper($contract->sepa_bic);
	}


	public function created($contract)
	{
		$contract->save();     			// forces to call the updated method of the observer
		$contract->push_to_modems(); 	// should not run, because a new added contract can not have modems..
	}

	public function updating($contract)
	{
		$contract->number = $contract->id - $this->num;

		$contract->sepa_iban = strtoupper($contract->sepa_iban);
		$contract->sepa_bic  = strtoupper($contract->sepa_bic);
	}

	public function updated ($contract)
	{
		$contract->push_to_modems();
	}

	public function saved ($contract)
	{
		$contract->push_to_modems();
	}
}


/**
 * Base updater class for all data that is related to orders and phonenumbers
 *
 * @author Patrick Reichel
 */
class VoipRelatedDataUpdater {

	/**
	 * Constructor
	 *
	 * @author Patrick Reichel
	 */
	public function __construct($contract_id) {

		$this->contract = Contract::findOrFail($contract_id);
	}

}

/**
 * Class to update data related to orders and phonenumbers using EnviaOrders as data base.
 *
 * @author Patrick Reichel
 */
class VoipRelatedDataUpdaterByEnvia extends VoipRelatedDataUpdater {

	/**
	 * Constructor
	 *
	 * @author Patrick Reichel
	 */
	public function __construct($contract_id) {

		parent::__construct($contract_id);

		dd(__FILE__, __LINE__, $this->contract);
	}
}
