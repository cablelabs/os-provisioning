<?php

class SnmpMib extends \Eloquent {

	protected $table = 'snmpmib';

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['devicetype_id', 'html_type', 'html_frame', 'html_properties', 'field', 'oid', 'oid_table', 'type', 'type_array', 
							'phpcode_pre', 'phpcode_post', 'description'];

}