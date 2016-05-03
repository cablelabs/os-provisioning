<?php

namespace App\Http\Controllers;
use Route;
use Module;


// TODO: make all relation functions static, which has the advantage that we can use them in static- and non-static context
class BaseModuleController extends BaseController {

	public function __construct() {
		parent::__construct();
	}


	public static function get_mvc_path()
	{
		$a = explode('\\', Route::getCurrentRoute()->getActionName());
		return $a[0].'\\'.$a[1];
	}


	protected static function __get_model_name()
	{
		return explode ('Controller', explode ('\\', explode ('@', Route::getCurrentRoute()->getActionName())[0])[4])[0];
	}

	protected static function get_model_name()
	{
		// Note: returns namespace of Model
		// quick and dirty :)
		return static::get_mvc_path().'\\Entities\\'.static::__get_model_name();
	}

	protected function get_controller_name()
	{
		return explode('@', Route::getCurrentRoute()->getActionName())[0];
	}

	protected function get_view_name()
	{
		return strtolower(explode ('\\', static::get_model_name())[1]).'::'.static::__get_model_name();
	}


	protected function get_route_name()
	{
		return explode('\\', static::get_model_name())[3];
	}

	/*
	 * Returns Global Config Index Page with links to the configurable Modules
	 *
	 * @author Nino Ryschawy
	 */
	public function glob_conf()
	{
        $tmp = get_parent_class();
        $base_controller = new $tmp;

        $links = BaseController::get_config_modules();
        $view_header = BaseViewController::translate("Global Configurations");
        $route_name = 'Config.index';

    	return \View::make('GlobalConfig.index', $base_controller->compact_prep_view(compact('links', 'view_header', 'route_name')));
	}
}
