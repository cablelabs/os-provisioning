<?php

namespace Modules\ProvBase\Entities;

use Modules\ProvBase\Entities\Qos;

class Contract extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'contract';


	// Add your validation rules here
    public static function rules($id = null)
    {
        return array(
			'number' => 'string|unique:contract,number,'.$id.',id,deleted_at,NULL',
            'firstname' => 'required',
            'lastname' => 'required',
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'phone' => 'required',
            'email' => 'email',
            'birthday' => 'required|date',
            'contract_start' => 'required|date',
            'contract_end' => 'dateornull', // |after:now -> implies we can not change stuff in an out-dated contract
            'sepa_iban' => 'iban',
            'sepa_bic' => 'bic',
        );
    }


    // Name of View
    public static function get_view_header()
    {
        return 'Contract';
    }

    // link title in index view
    public function get_view_link_title()
    {
		$old = $this->number2 ? ' - (Old Nr: '.$this->number2.')' : '';
		return $this->number.' - '.$this->firstname.' '.$this->lastname.' - '.$this->city.$old;
    }


    // Relations
    public function modems()
    {
		return $this->hasMany('Modules\ProvBase\Entities\Modem');
    }

	/**
	 * Get relation to external orders.
	 *
	 * @author Patrick Reichel
	 */
	public function external_orders() {

		if ($this->module_is_active('provvoipenvia')) {
			return $this->hasMany('Modules\ProvVoipEnvia\Entities\EnviaOrder')->withTrashed()->where('ordertype', 'NOT LIKE', 'order/create_attachment');
		}

		return null;
	}

    // View Relation.
    public function view_has_many()
    {
		$ret = array(
			'Modem' => $this->modems,
		);

		if ($this->module_is_active('provvoipenvia')) {
			$ret['EnviaOrder'] = $this->external_orders;
		}

		return $ret;
    }


    /*
     * Generate use a new user login password
     * This does not save the involved model
     */
    public function generate_password($length = 10)
    {
        $this->password = \Acme\php\Password::generate_password();
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


	/**
	 * Helper to define possible salutation values.
	 * E.g. Envia-API has a well defined set of valid values – using this method we can handle this.
	 *
	 * @author Patrick Reichel
	 */
	public function get_salutation_options() {

		$defaults = [
			'Herr',
			'Frau',
			'Firma',
			'Behörde',
		];

		if ($this->module_is_active('provvoipenvia')) {

			$options = [
				'Herrn',
				'Frau',
				'Firma',
				'Behörde',
			];
		}
		else {
			$options = $defaults;
		}

		$result = array();
		foreach ($options as $option) {
			$result[$option] = $option;
		}

		return $result;
	}


	/**
	 * Helper to define possible academic degree values.
	 * E.g. Envia-API has a well defined set of valid values – using this method we can handle this.
	 *
	 * @author Patrick Reichel
	 */
	public function get_academic_degree_options() {

		$defaults = [
			'',
			'Dr.',
			'Prof. Dr.',
		];

		if ($this->module_is_active('provvoipenvia')) {

			$options = [
				'',
				'Dr.',
				'Prof. Dr.',
			];
		}
		else {
			$options = $defaults;
		}

		$result = array();
		foreach ($options as $option) {
			$result[$option] = $option;
		}

		return $result;
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
		return !($date->year > 1900);
	}


	/*
	 * The Daily Scheduling Function
	 *
	 * Tasks:
	 *  1. Check if $this contract end date is expired -> disable network_access
	 *  2. Check if $this is a new contract and activate it -> enable network_access
	 *
	 * @return: none
	 * @author: Torsten Schmidt
	 */
	public function daily_conversion()
	{
		$now   = \Carbon\Carbon::now();
		$start = $this->_date_to_carbon($this->contract_start);
		$end   = $this->_date_to_carbon($this->contract_end);


		// Task 1: Check if $this contract end date is expired -> disable network_access
		if ($end->lt($now) && !$this->_date_null($end) && $this->network_access == 1)
		{
			\Log::Info('daily: contract: disable based on ending contract date for '.$this->id);

			$this->network_access = 0;
			$this->save();
		}

		// Task 2: Check if $this is a new contract and activate it -> enable network_access
		// Note: to avoid enabling contracts which are disabled manually, we also check if
		//       maximum time beetween start contract and now() is not older than 1 day.
		// Note: This requires the daily scheduling to run well
		//       Otherwise the contracts must be enabled manually
		// TODO: give them a good testing
		if ($start->lt($now) && !$this->_date_null($start) && $start->diff($now)->days <= 1 && $this->network_access == 0)
		{
			\Log::Info('daily: contract: enable contract based on start contract date for '.$this->id);

			$this->network_access = 1;
			$this->save();
		}
	}


	/*
	 * The Monthly Scheduling Function
	 *
	 * Tasks:
	 *  1. monthly QOS transition / change
	 *  2. monthly VOIP transition / change
	 *
	 * @return: none
	 * @author: Torsten Schmidt
	 */
	public function monthly_conversion()
	{
		// QOS: monthly QOS change – "Tarifwechsel"
		if ($this->next_qos_id > 0)
		{
			\Log::Info('monthly: contract: change QOS for '.$this->id.' from '.$this->qos_id.' to '.$this->next_qos_id);
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


	/*
	 * Push all settings from Contract layer to the related child Modems (for $this)
	 * This includes: network_access, qos_id
	 *
	 * Note: We call this function from Observer context so a change of the explained
	 *       fields will push this changes to the child Modems
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
	// start contract numbers from 10000 - TODO: move to global config
	protected $num = 490000;

	public function created($contract)
	{
		$contract->number = $contract->id - $this->num;
		$contract->save();     // forces to call the updated method of the observer

		$contract->push_to_modems(); // should not be run, because a new added contract can not have modems..
	}

	public function updating($contract)
	{
		$contract->number = $contract->id - $this->num;
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
