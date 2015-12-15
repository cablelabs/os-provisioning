<?php

class GlobalConfig extends BaseModel {

	// The associated SQL table for this Model
	protected $table = 'global_config';

	// Don't forget to fill this array
	protected $fillable = ['name', 'street', 'city', 'phone', 'mail', 'log_level', 'headline1', 'headline2'];

	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'mail' => 'email',
		);
	}
	
	// Name of View
	public static function get_view_header()
	{
		return 'Configuration';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return "Global Configuration";
	}	


}