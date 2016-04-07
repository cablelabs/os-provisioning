<?php

namespace App\Http\Controllers;
use Route;
use Module;

class BaseModuleController extends BaseController {

	public function __construct() {
		parent::__construct();
	}


	public function get_mvc_path()
	{
		$a = explode('\\', Route::getCurrentRoute()->getActionName());
		return $a[0].'\\'.$a[1];
	}


	protected function __get_model_name()
	{
		return explode ('Controller', explode ('\\', explode ('@', Route::getCurrentRoute()->getActionName())[0])[4])[0];
	}

	protected function get_model_name()
	{
		// Note: returns namespace of Model
		// quick and dirty :)
		return $this->get_mvc_path().'\\Entities\\'.$this->__get_model_name();
	}


	protected function get_controller_name()
	{
		return explode('@', Route::getCurrentRoute()->getActionName())[0];
	}


	protected function get_view_name()
	{
		return strtolower(explode ('\\', $this->get_model_name())[1]).'::'.$this->__get_model_name();
	}


	protected function get_route_name()
	{
		return explode('\\', $this->get_model_name())[3];
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
