<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use Redirect;
use GlobalConfig;


class AuthController extends BaseController {

	/*
	 * Show Login Page
	 */
	public function showLogin()
	{
		$g = GlobalConfig::first();
		$head1 = $g->headline1;
		$head2 = $g->headline2;

		// show the form
		return \View::make('auth/login', compact('head1', 'head2'));
	}


	private function default_page()
	{
		// If ProvBase is not installed redirect to Config Page
		$bm = new \BaseModel;
		if (!\PPModule::is_active ('ProvBase'))
			return Redirect::to('Config');

		// Redirect to Default Page
		// TODO: Redirect to a global overview page
		return Redirect::to('Contract');
	}


	/*
	 * This is the BASIC Home '/' Route Function
	 */
	public function home()
	{
		// Check Login
		if (!Auth::user())
			return Redirect('auth/login');

		return $this->default_page();
	}


	/*
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
			return Redirect::to('auth/login')
				->withErrors($validator) // send back all errors to the login form
				->withInput(Input::except('password')) // send back the input (not the password) so that we can repopulate the form
				->with('status', $error_text);
		}
		else {

			// create our user data for the authentication
			$userdata = array(
				'login_name' => Input::get('login_name'),
				'password' => Input::get('password'),
				'active' => 1,	// user has to be active
			);

			// attempt to do the login
			if (Auth::attempt($userdata)) {
				return $this->default_page();
			}
			else {

				// validation not successful, send back to form
				return Redirect::to('auth/login')
					->withInput(Input::except('password')) // send back the input (not the password) so that we can repopulate the form
					->with('status', 'No valid Login');

			}

		}
	}


	/*
	 * Logout User
	 */
	public function doLogout()
	{
		Auth::logout();

		return Redirect::to('auth/login');
	}


	/*
	 * This function will be called if user has no access to a certain area
	 * or has no valid login at all.
	 */
	public function denied()
	{
		return \View::make('auth.denied');
	}
}
