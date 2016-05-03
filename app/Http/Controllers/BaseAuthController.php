<?php

namespace App\Http\Controllers;

use App;
use Module;
use Config;
use File;
use View;
use Validator;
use Input;
use Redirect;
use Route;
use BaseModel;
use Auth;
use NoAuthenticateduserError;
use Log;
use GlobalConfig;

use App\Exceptions\AuthExceptions;


class BaseAuthController extends Controller {

	// The authentication array
	// This is manly used/required for caching/speed-up
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
		if (is_null($model_to_check)) {
			$model_to_check = static::get_model_name();
		}

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