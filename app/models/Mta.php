<?php

namespace Models;

class Mta extends \Eloquent {

	// Add your validation rules here
	public static function rules($id=null)
	{
		return array();
            /* 'hostname' => 'required|hostname|unique:mtas,hostname,'.$id */
		/* ); */
	}

	// Don't forget to fill this array
	protected $fillable = ['mac', 'hostname', 'configfile_id', 'type'];

}
