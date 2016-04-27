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
		return 'Global Config';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return "Global Config";
	}


	/*
	 * Get NMS Version
	 * NOTE: get the actual rpm version of the installed package
	 *       or branch name and short commit reference of GIT repo
	 *
	 * @param: null
	 * @return: string containing version information
	 * @author: Torsten Schmidt
	 */
	public function version ()
	{
		$version = exec("rpm -q lara-base --queryformat '%{version}'");
		if (preg_match('/not installed/', $version))
			$version = 'GIT: '.exec('cd '.app_path().' && git rev-parse --abbrev-ref HEAD').' - '.exec('cd '.app_path().' && git rev-parse --short HEAD');

		return $version;
	}

}