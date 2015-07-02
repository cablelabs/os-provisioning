<?php

class Endpoint extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		 'hostname' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'mac', 'public', 'description', 'modem_id'];

	public function modem ()
	{
		return $this->belongsTo('Modem');
	}
}