<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use Redirect;
use GlobalConfig;

/**
 * Basic AuthController
 *
 * TODO: this controller should not clone from BaseController!
 *       This could be a security hazard!
 *       Better: clone from general Controller API
 *
 * @author Patrick Reichel (founder)
 * @author Torsten Schmidt (adaptions to PingPong, middleware, CCC)
 */
class AuthController extends BaseController {

	// URL prefix, Headlines, Login Page after successful login, authentication guarder, bg image
	protected $prefix, $headline1, $headline2, $login_page, $guard, $image;


	// Constructor
	public function __construct()
	{
		$g = GlobalConfig::first();
		$this->headline1 = $g->headline1;
		$this->headline2 = $g->headline2;
		$this->prefix = \BaseRoute::$admin_prefix; // url prefix
		$this->login_page = null; // means jump to normal admin page
		$this->image = 'main-pic-1.png';

		// @see: L5 documentation for authentication and "Accessing Specific Guard Instances"
		// @see: config/auth.php
		$this->guard = 'admin';

		return parent::__construct();
	}


	/**
	 * Show Login Page
	 *
	 * @return type view
	 */
	public function showLogin()
	{
		$g = GlobalConfig::first();
		$head1 = $this->headline1;
		$head2 = $this->headline2;
		$prefix = $this->prefix;
		$image = $this->image;

		// show the form
		return \View::make('auth/login', compact('head1', 'head2', 'prefix', 'image'));
	}


	/**
	 * Show Default Page after successful login
	 *
	 * TODO: use $this to return a global defined default page, see below
	 *
	 * @return type Redirect
	 */
	private function default_page()
	{
		if(!is_null($this->login_page))
			return Redirect::to($this->prefix.'/'.$this->login_page);

		// TODO: return to dashboard, but via $login_page variable !
		// If ProvBase is not installed redirect to Config Page
		$bm = new \BaseModel;
		if (!\PPModule::is_active ('ProvBase'))
			return Redirect::to($this->prefix.'/Config');

		// Redirect to Default Page
		// TODO: Redirect to a global overview page
		return Redirect::to($this->prefix.'/Contract');
	}


	/**
	 * This is the BASIC Home '/' Route Function
	 */
	public function home()
	{
		// Check Login
		if (!Auth::user())
			return $this->showLogin();

		// Valid User: goto default page
		return $this->default_page();
	}


	/**
	 * Perform Login. Check if valid User is logged in
	 */
	public function doLogin()
	{
		// validate the info, create rules for the inputs
		$rules = array(
			'login_name'    => 'required|string',
			'password' => 'required|string|min:3' // password can only be alphanumeric and has to be greater than 3 characters
		);

		// run the validation rules on the inputs from the form
		$validator = \Validator::make(Input::all(), $rules);

		// if the validator fails, redirect back to the form
		if ($validator->fails()) {
			$error_text = $validator->errors()->first('login_name').'<br>'.$validator->errors()->first('password');
			return Redirect::to($this->prefix.'/auth/login')
				->withErrors($validator) // send back all errors to the login form
				->withInput(Input::except('password')) // send back the input (not the password) so that we can repopulate the form
				->with('status', $error_text);
		}

		// create our user data for the authentication
		$userdata = array(
			'login_name' => Input::get('login_name'),
			'password' => Input::get('password'),
			'active' => 1,	// user has to be active
		);

		// attempt to do the login
		if (Auth::guard($this->guard)->attempt($userdata))
			return $this->default_page();

		// validation not successful, send back to form
		return Redirect::to($this->prefix.'/auth/login')
			->withInput(Input::except('password')) // send back the input (not the password) so that we can repopulate the form
			->with('status', 'No valid Login');
	}


	/**
	 * Logout User
	 */
	public function doLogout()
	{
		Auth::logout();

		return Redirect::to($this->prefix.'/auth/login');
	}


	/**
	 * This function will be called if user has no access to a certain area
	 * or has no valid login at all.
	 */
	public function denied()
	{
		return \View::make('auth.denied');
	}
}
