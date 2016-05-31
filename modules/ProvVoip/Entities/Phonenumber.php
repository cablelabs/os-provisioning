<?php

namespace Modules\ProvVoip\Entities;

use Illuminate\Support\Collection;

// Model not found? execute composer dump-autoload in lara root dir
class Phonenumber extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'phonenumber';

	// Add your validation rules here
	public static function rules($id=null)
	{
		// Port unique in the appropriate mta (where mta_id=mta_id and deleted_at=NULL)
		$mta_id = 1;
		if ($id)
			$mta_id = Phonenumber::find($id)->mta->id;

		return array(
			'country_code' => 'required|numeric',
			'prefix_number' => 'required|numeric',
			'number' => 'required|numeric',
			'mta_id' => 'required|exists:mta,id|min:1',
			'port' => 'required|numeric|min:1|unique:phonenumber,port,'.$id.',id,deleted_at,NULL,mta_id,'.$mta_id,
			'active' => 'required|boolean',
			// TODO: check if password is secure and matches needs of external APIs (e.g. Envia)
		);
	}


	// Name of View
	public static function view_headline()
	{
		return 'Phonenumbers';
	}



	// link title in index view
    public function view_index_label()
    {
        $bsclass = 'success';

        if ($this->active == 0)
			$bsclass = 'danger';

        // TODO: use mta states.
        //       Maybe use fast ping to test if online in this funciton?

        return ['index' => [$this->country_code, $this->prefix_number, $this->number, $this->port],
                'index_header' => ['Name', 'MAC', 'Type', 'Phone Port'],
                'bsclass' => $bsclass,
                'header' => 'Port '.$this->port.': '.$this->prefix_number."/".$this->number];
    }

	/**
	 * ALL RELATIONS
	 * link with mtas
	 */
	public function mta()
	{
		return $this->belongsTo('Modules\ProvVoip\Entities\Mta', 'mta_id');
	}

	// belongs to an mta
	public function view_belongs_to ()
	{
		return $this->mta;
	}

    // View Relation.
    public function view_has_many()
    {
		if (\PPModule::is_active('provvoipenvia'))
		{
			$ret['Envia']['Envia Order']['class'] = 'EnviaOrder';
			$ret['Envia']['Envia Order']['relation'] = $this->external_orders;

			$ret['Envia']['PhonenumberManagement']['class'] = 'PhonenumberManagement';

			$relation = $this->phonenumbermanagement;

			// can be created if no one exists, can be deleted if one exists
			if (is_null($relation)) {
				$ret['Envia']['PhonenumberManagement']['relation'] = new Collection();
				$ret['Envia']['PhonenumberManagement']['options']['hide_delete_button'] = 1;
			}
			else {
				$ret['Envia']['PhonenumberManagement']['relation'] = [$relation];
				$ret['Envia']['PhonenumberManagement']['options']['hide_create_button'] = 1;
			}

			// TODO: auth - loading controller from model could be a security issue ?
			$ret['Envia']['Envia API']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
			$ret['Envia']['Envia API']['view']['vars']['extra_data'] = \Modules\ProvVoip\Http\Controllers\PhonenumberController::_get_envia_management_jobs($this);
		}

		return $ret;
    }

	/**
	 * return all mta objects
	 */
	public function mtas()
	{
		$dummies = Mta::withTrashed()->where('is_dummy', True)->get();
		$mtas = Mta::get();
		return array('dummies' => $dummies, 'mtas' => $mtas);
	}

	/**
	 * return a list [id => hostname] of all mtas
	 */
	public function mtas_list()
	{
		$ret = array();
		foreach ($this->mtas()['mtas'] as $mta)
		{
			$ret[$mta->id] = $mta->hostname;
		}

		return $ret;
	}

	/**
	 * return a list [id => hostname] of all mtas
	 */
	public function mtas_list_with_dummies()
	{
		$ret = array();
		foreach ($this->mtas() as $mta_tmp)
		{
			foreach ($mta_tmp as $mta)
			{
				$ret[$mta->id] = $mta->hostname;
			}
		}

		return $ret;
	}

	/**
	 * link to management
	 */
	public function phonenumbermanagement() {
		return $this->hasOne('Modules\ProvVoip\Entities\PhonenumberManagement');
	}

	/**
	 * Get relation to external orders.
	 *
	 * @author Patrick Reichel
	 */
	public function external_orders() {

		if (\PPModule::is_active('provvoipenvia')) {
			return $this->hasMany('Modules\ProvVoipEnvia\Entities\EnviaOrder');
		}

		return null;
	}

	/**
	 * BOOT:
	 * - init phone observer
	 */
	public static function boot()
	{
		parent::boot();

		Phonenumber::observe(new PhonenumberObserver);
	}
}


/**
 * Phonenumber Observer Class
 * Handles changes on Phonenumbers
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 *
 * @author Patrick Reichel
 */
class PhonenumberObserver
{

	/**
	 * For Envia API we create username and login if not given.
	 * Otherwise Envia will do this – so we would have to ask for this data…
	 *
	 * @author Patrick Reichel
	 */
	protected function _create_login_data($phone) {

		$changed = false;

		if (\PPModule::is_active('provvoipenvia') && ($phone->mta->type == 'sip')) {

			if (!boolval($phone->password)) {
				$phone->password = \Acme\php\Password::generate_password(15, 'envia');
				$changed = true;
			}

			// username at Envia defaults to prefixnumber + number – we also do so
			if (!boolval($phone->username)) {
				$phone->username = $phone->prefix_number.$phone->number;
				$changed = true;
			}

		}

		if ($changed) {
			$phone->save();
		}

	}


	public function created($phone)
	{
		$this->_create_login_data($phone);

		$phone->mta->make_configfile();
		$phone->mta->modem->restart_modem();

	}


	public function updated($phone)
	{
		$this->_create_login_data($phone);

		$phone->mta->make_configfile();
		$phone->mta->modem->restart_modem();
	}


	public function deleted($phone)
	{
		$phone->mta->make_configfile();
		$phone->mta->modem->restart_modem();
	}
}
