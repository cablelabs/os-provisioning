<?php

namespace App\Http\Controllers;
use Route;
use Module;


/**
 * BaseModuleController: is manly used to adapt the MVC naming to pingpong modules namespace
 *
 * TODO: This controller is deprecated and should be removed soon, because namepacing is out-sourced ..
 */
class BaseModuleController extends BaseController {

	// required ?
	public function __construct() {
		parent::__construct();
	}

	/**
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
