<?php

namespace Modules\ProvVoip\Entities;

// Model not found? execute composer dump-autoload in lara root dir
class Phonenumber extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'phonenumber';


	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
			'country_code' => 'required|numeric',
			'prefix_number' => 'required|numeric',
			'number' => 'required|numeric',
			'mta_id' => 'required|exists:mta,id|min:1',
			'port' => 'required|numeric|min:1',
			'active' => 'required|boolean',
		);
	}

	// Don't forget to fill this array
	protected $fillable = ['mta_id', 'port', 'country_code', 'prefix_number', 'number', 'username', 'password', 'active'];


	// Name of View
	public static function get_view_header()
	{
		return 'Phonenumbers';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return "(".$this->country_code.") ".$this->prefix_number."/".$this->number;
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


	// has zero or one management object related
	public function view_has_one() {
		return array(
			'PhonenumberManagement' => $this->phonenumbermanagement,
		);
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

		if ($this->module_is_active('provvoipenvia')) {
			return $this->hasMany('Modules\ProvVoipEnvia\Entities\EnviaOrder');
		}

		return null;
	}
}
