<?php

namespace Modules\ProvVoip\Entities;

class ProvVoip extends \BaseModel {

	// The associated SQL table for this Model
	protected $table = 'provvoip';

	// Don't forget to fill this array
	protected $fillable = ['startid_mta'];

	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
		);
	}

	// Name of View
	public static function view_headline()
	{
		return 'ProvVoip Config';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return "ProvVoip";
	}


}