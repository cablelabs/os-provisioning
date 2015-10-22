<?php

class DeviceType extends \Eloquent {

	// The associated SQL table for this Model
	protected $table = 'devicetype';


	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['name', 'vendor', 'version', 'description', 'parent_id'];

}