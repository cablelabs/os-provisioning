<?php

namespace Models;

use Log;


class CmtsDownstream extends \BaseModel {


	// The associated SQL table for this Model
    protected $table = 'cmtsdownstream';

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['alias', 'cmts_id', 'index', 'description', 'frequency', 'modulation', 'power'];

}