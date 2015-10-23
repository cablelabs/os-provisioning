<?php

class Device extends \Eloquent {

	// The associated SQL table for this Model
	protected $table = 'device';


	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['devicetype_id', 'name', 'ip', 'community_ro', 'community_rw', 'address1', 'address2', 'address3', 'description'];

}