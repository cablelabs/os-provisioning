<?php

namespace App\Http\Controllers;

use App;
use Auth;
use NoAuthenticateduserError;
use Log;

use App\Exceptions\AuthExceptions;

/*
 * Authentication Base Class for Checking active
 */
class BaseAuthController extends Controller {

	// The authentication array
	// This is manly used/required for caching/speed-up
	// NOTE: static class variables seems to live as long as the php process is running
	//       this seems to be a kind of hack(?). Advantage is that we can cache requests
	//       instead of producing many sql requests. Of curse this needs some more testing. (TODO)
	private static $permissions;


	/**
	 * Get permissions array from model.
	 * This will overwrite an existing array.
	 *
	 * @throws NoAuthenticatedUserError if no user is logged in
	 */
	protected static function _get_permissions() {

		if (self::$permissions)
			return self::$permissions;

		// get the currently authenticated user
		$cur_user = Auth::user();

		// no user logged in
		if (is_null($cur_user)) {
			throw new NoAuthenticateduserError("No user logged in");
		}

		Log::debug('get_model_permissions() - parse user auth datas from sql');
		self::$permissions = $cur_user->get_model_permissions();

		// get permissions for each role from model
		return self::$permissions;
	}


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
	 * @throws NoAuthenticatedUserError if no user is logged in
	 * @throws NoModelPermissionError if user is not allowed to acces the model
	 * @throws InvalidPermissionsRequest if permission request is invalid
	 * @throws InsufficientRightsError if user has not the specific right needed to perform an action
	 */
	protected static function _check_permissions($access, $model_to_check=null) {

		// get the currently authenticated user
		$cur_user = Auth::user();

		// if no model is given: use current model
		if (is_null($model_to_check))
			$model_to_check = static::__model_to_check();

		// no user logged in
		if (is_null($cur_user)) {
			throw new AuthExceptions('Login Required');
		}

		// build permissions array for easy access to user rights
		$permissions['model'] = static::_get_permissions();

		// check model rights
		if (!array_key_exists($model_to_check, $permissions['model'])) {
			throw new AuthExceptions('Access to model '.$model_to_check.' not allowed for user '.$cur_user->login_name.'.');
		}
		if (!array_key_exists($access, $permissions['model'][$model_to_check])) {
			throw new AuthExceptions('Something went wrong asking for '.$access.' right in '.$model_to_check.' for user '.$cur_user->login_name.'.');
		}
		if ($permissions['model'][$model_to_check][$access] == 0) {
			throw new AuthExceptions('User '.$cur_user->login_name.' is not allowed to '.$access.' in '.$model_to_check.'.');
		}

		// TODO: check net rights
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
		try {
			static::_check_permissions($access, $model_to_check);
		}
		catch (PermissionDeniedError $ex) {
			return View::make('auth.denied', array('error_msg' => $ex->getMessage()));
		}
	}
}