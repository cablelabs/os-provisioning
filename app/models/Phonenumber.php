<?php

namespace Models;

// Model not found? execute composer dump-autoload in lara root dir
class Phonenumber extends \BaseModel {

	// for soft deleting => move to BaseModel?
	use \Illuminate\Database\Eloquent\SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
			'country_code' => 'required|numeric',
			'prefix_number' => 'required|numeric',
			'number' => 'required|numeric',
			'mta_id' => 'required|exists:mtas,id|min:1',
			'port' => 'required|numeric|min:1',
			'active' => 'required|boolean',
		);
	}

	// Don't forget to fill this array
	protected $fillable = ['mta_id', 'port', 'country_code', 'prefix_number', 'number', 'username', 'password', 'active'];


	/**
	 * link with mtas
	 */
	public function mta()
	{
		return $this->belongsTo('Models\Mta');
	}

}
