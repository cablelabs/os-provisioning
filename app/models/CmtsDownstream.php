<?php

namespace Models;

use Log;


class CmtsDownstream extends \BaseModel {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['alias', 'cmts_gws_id', 'index', 'description', 'frequency', 'modulation', 'power'];

}