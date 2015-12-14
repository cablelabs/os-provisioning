<?php

namespace Modules\ProvBase\Entities;

class ProvBase extends \BaseModel {

	// The associated SQL table for this Model
	protected $table = 'provbase';

	// Don't forget to fill this array
	protected $fillable = ['provisioning_server'];

	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'provisioning_server' => 'ip',
		);
	}
	
	// Name of View
	public static function get_view_header()
	{
		return 'Prov Base Config';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return "";
	}	


}