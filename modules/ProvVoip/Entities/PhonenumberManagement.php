<?php

namespace Modules\ProvVoip\Entities;

use Illuminate\Support\Collection;

// Model not found? execute composer dump-autoload in lara root dir
class PhonenumberManagement extends \BaseModel {

	// get functions for some address select options
	use \App\Models\AddressFunctionsTrait;

    // The associated SQL table for this Model
    public $table = 'phonenumbermanagement';


	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
			'phonenumber_id' => 'required|exists:phonenumber,id|min:1',
			'trcclass' => 'required|exists:trcclass,id',
			'carrier_in' => 'required|exists:carriercode,id',
			'carrier_out' => 'required|exists:carriercode,id',
			'ekp_in' => 'required|exists:ekpcode,id',
			'activation_date' => 'date',
			'deactivation_date' => 'date',
		);
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
				];


	// Name of View
	public static function view_headline()
	{
		return 'Phonenumber Management';
	}

	// link title in index view
	public function view_index_label()
	{
        $bsclass = 'success';

        return ['index' => [$this->id],
                'index_header' => ['ID'],
                'bsclass' => $bsclass,
                'header' => 'PhonenumberManagement'];
	}

	/**
	 * ALL RELATIONS
	 * link with phonenumbers
	 */
	public function phonenumber()
	{
		return $this->belongsTo('Modules\ProvVoip\Entities\Phonenumber');
	}

	// belongs to an phonenumber
	public function view_belongs_to ()
	{
		return $this->phonenumber;
	}

	/**
	 * return a list [id => number] of all phonenumbers
	 */
	public function phonenumber_list()
	{
		$ret = array();
		foreach ($this->phonenumber()['phonenumbers'] as $phonenumber)
		{
			$ret[$phonenumber->id] = $phonenumber->prefix_number.'/'.$phonemumber->number;
		}

		return $ret;
	}

	/**
	 * return a list [id => number] of all phonenumber
	 */
	public function phonenumber_list_with_dummies()
	{
		$ret = array();
		foreach ($this->phonenumber() as $phonenumber_tmp)
		{
			foreach ($phonenumber_tmp as $phonenumber)
			{
				$ret[$phonenumber->id] = $phonenumber->prefix_number.'/'.$phonemumber->number;
			}
		}

		return $ret;
	}

	/**
	 * Get relation to trc classes.
	 *
	 * @author Patrick Reichel
	 */
	public function trc_class() {

		if (\PPModule::is_active('provvoipenvia')) {
			return $this->hasOne('Modules\ProvVoipEnvia\Entities\TRCClass', 'trcclass');
		}

		return null;
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

		return $this->phonenumber->hasMany('Modules\ProvVoipEnvia\Entities\EnviaOrder')->withTrashed()->where('ordertype', 'NOT LIKE', 'order/create_attachment');
	}


	/**
	 * Get relation to phonebookentry.
	 *
	 * @author Patrick Reichel
	 */
	public function phonebookentry() {

		return $this->hasOne('Modules\ProvVoip\Entities\PhonebookEntry', 'phonenumbermanagement_id');
	}


	// has zero or one phonebookentry object related
	public function view_has_one() {
		return array(
			'PhonebookEntry' => $this->phonebookentry,
		);
	}


	// View Relation.
	public function view_has_many() {

		if (\PPModule::is_active('provvoipenvia')) {
			$ret['Envia']['EnviaOrder']['class'] = 'EnviaOrder';
			$ret['Envia']['EnviaOrder']['relation'] = $this->_envia_orders;

			$ret['Envia']['PhonebookEntry']['class'] = 'PhonebookEntry';

			$relation = $this->phonebookentry;

			// can be created if no one exists, can be deleted if one exists
			if (is_null($relation)) {
				$ret['Envia']['PhonebookEntry']['relation'] = new Collection();
				$ret['Envia']['PhonebookEntry']['options']['hide_delete_button'] = 1;
			}
			else {
				$ret['Envia']['PhonebookEntry']['relation'] = [$relation];
				$ret['Envia']['PhonebookEntry']['options']['hide_create_button'] = 1;
			}

			// TODO: auth - loading controller from model could be a security issue ?
			$ret['Envia']['Envia API']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
			$ret['Envia']['Envia API']['view']['vars']['extra_data'] = \Modules\ProvVoip\Http\Controllers\PhonenumberManagementController::_get_envia_management_jobs($this);
		}
		else {
			$ret = array();
		}

		return $ret;
	}

	/**
	 * BOOT:
	 * - init phone observer
	 */
	public static function boot()
	{
		parent::boot();

		PhonenumberManagement::observe(new PhonenumberManagementObserver);
	}
}


/**
 * PhonenumberManagement observer class
 * Handles changes on Phonenumbers
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 *
 * @author Patrick Reichel
 */
class PhonenumberManagementObserver
{

	public function created($phonenumbermanagement)
	{
		$phonenumbermanagement->phonenumber->set_active_state();
	}

	public function updated($phonenumbermanagement)
	{
		$phonenumbermanagement->phonenumber->set_active_state();
	}

	public function deleted($phonenumbermanagement)
	{
		$phonenumbermanagement->phonenumber->set_active_state();
	}
}
