<?php

namespace App\Http\Controllers\Auth;

use App, Auth, Bouncer, Log;
use App\Exceptions\AuthException;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;


/*
 * Authentication Base Class for Checking active
 */
class BaseAuthController extends Controller {

	/**
	 * Return Model to Check
	 *
	 * @return type string containing the model to access
	 * @author Torsten Schmidt
	 */
	private static function __model_to_check()
	{
		$m = null;
		$m = \NamespaceController::get_model_name();

		// Rewrite model to check with new assigned Model
		switch ($m) {
			case 'App\Base': // global search
				$m = 'App\GlobalConfig';
				break;

			case 'Modules\HfcBase\Entities\TreeErd': // entity relation diagram
			case 'Modules\HfcBase\Entities\TreeTopo': // topography tree card
			case 'Modules\HfcBase\Entities\TreeTopography':
			case 'Modules\HfcSnmp\Entities\Snmp':
				$m = 'Modules\HfcReq\Entities\NetElement';
				break;

			case 'Modules\HfcCustomer\Entities\CustomerTopo': // topography modems
			case 'Modules\ProvMon\Entities\ProvMon': 			// modem analyses page
				$m = 'Modules\ProvBase\Entities\Modem';
				break;

			case 'Modules\ProvBase\Entities\Dashboard':
					$m = 'Modules\ProvBase\Entities\ProvBase';
					break;
		}

		return $m;
	}


	/**
	 * Check if user has permission to continue.
	 * Use this method to protect your methods
	 *
	 * @author Patrick Reichel
	 *
	 * @param $access [view|create|edit|delete]
	 * @param $model_to_check model path and name (in format as is stored in database); use current model if not given
	 *
	 * @throws AuthorizationException which redirects to login page
	 */
	protected static function _check_permissions($access, $modelToCheck=null) {

		$user = Auth::user();
		// if no model is given: use current model
		if (is_null($modelToCheck))
			$modelToCheck = static::__model_to_check();

		// no user logged in
		if (is_null($user)) {
			throw new AuthorizationException();
		}

		if (Bouncer::cannot($access, $modelToCheck))
			throw new AuthException('Access to model '. $modelToCheck .' not allowed for user '. $user->login_name .'.');
	}


	/**
	 * Main Authentication Function
	 *
	 * Check if user has permission to continue.
	 * Use this method to protect your methods
	 *
	 * NOTE: This function will (and should be mainly used) from Middleware context
	 *
	 * @author Torsten Schmidt
	 *
	 * @param $access [view|create|edit|delete]
	 * @param $model_to_check model path and name (in format as is stored in database); use current model if not given
	 */
	public static function auth_check ($access, $model_to_check=null)
	{
		// Handling of thrown AuthException is done in app/Exceptions/Handler.php
		static::_check_permissions($access, $model_to_check);
	}
}
