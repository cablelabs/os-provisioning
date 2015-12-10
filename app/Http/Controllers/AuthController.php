<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use Redirect;


class AuthController extends BaseController {

	public function showLogin()
	{
		// show the form
		return \View::make('auth/login');
	}

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
			return Redirect::to('login')
				->withErrors($validator) // send back all errors to the login form
				->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
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
				return Redirect::intended('/');
			}
			else {

				// validation not successful, send back to form
				return Redirect::to('login');

			}

		}
	}
}
