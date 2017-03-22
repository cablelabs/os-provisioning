<?php

namespace App\Http\Controllers;

use Log;
use Auth;
use Input;
use Redirect;
use GlobalConfig;

// NOTE: will not work with default Request class (?) from app.php: Illuminate\Support\Facades\Request
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Modules\Ccc\Entities\CccAuthuser;

/**
 * Basic AuthController
 *
 * IMPORTANT: !!! do not use Auth:: directly, instead use $this->auth() !!!
 *            This will take care of the correct authentication API for
 *            Admin and CCC. Working directly with Auth:: will only work
 *            for the first (in this case Admin) layer!
 *
 * @author Patrick Reichel (founder)
 * @author Torsten Schmidt (adaptions to PingPong, middleware, CCC, adapt to L5.2 code like Throttling)
 */
class AuthController extends Controller {

	use AuthenticatesAndRegistersUsers, ThrottlesLogins;

	// URL prefix, Headlines, Login Page after successful login, authentication guarder, bg image
	protected $prefix, $headline1, $headline2, $login_page, $guard, $image;

	// @see usage from Illuminate\Foundation\Auth\AuthenticatesUsers
	protected $username = 'login_name';


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
	}


	/**
	 * A local helper which MUST be used instead of Auth::
	 * @return type Auth object (for actual object guard)
	 * @author Torsten Schmidt
	 */
	private function auth()
	{
		return Auth::guard($this->guard);
	}


	/**
	 * Local Helper for Logging
	 * @param type $text log message
	 * @param type|string $level [debug|info|warning|..]
	 * @return type
	 */
	private function log($text, $level = 'info')
	{
		Log::{$level}('Auth('.$this->guard.'): '.$text);
	}


	/**
	 * Show Login Page
	 *
	 * @return type view
	 */
	public function showLoginForm()
	{
		$g = GlobalConfig::first();
		$head1 = $this->headline1;
		$head2 = $this->headline2;
		$prefix = $this->prefix;
		$image = $this->image;

		// show the form
		return \View::make('auth.login', compact('head1', 'head2', 'prefix', 'image'));
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
		if (!$this->auth()->user())
			return $this->showLoginForm();

		// Valid User: goto default page
		return $this->default_page();
	}


	/**
	 * Perform Login. Check if the requested user is could login
	 *
	 * @todo NAT addresses could be blocked by throttle algorithm
	 * @todo implement a new MVC for IP address access lists
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postLogin(Request $request)
	{
		// Validation
		$this->validateLogin($request);

		// Authentication Throttling
		$throttles = $this->isUsingThrottlesLoginsTrait();
		if ($throttles && $lockedOut = $this->hasTooManyLoginAttempts($request))
		{
			$this->log('Block: '.$request->ip().' has reached maximum numbers of retries!', 'warning');
			return $this->sendLockoutResponse($request);
		}

		// create our user data for the authentication
		$userdata = $request->only('login_name', 'password');

		// attempt to do the login
		if ($this->auth()->attempt($userdata))
		{
			$this->log(Input::get('login_name').' has logged in');

			// update email password hash (salted sha512), if customer logs in successfully
			// this way we don't need to ask customers to set a new password manually
			if(\PPModule::is_active('mail') && $this->prefix == 'customer') {
				foreach(CccAuthuser::where('login_name', '=', $request->login_name)->first()->contract->emails as $email) {
					// password has already been hashed with sha512
					if(substr($email->password,0,3) === '$6$')
						continue;
					$email->psw_update($request->password);
				}
			}

			return $this->default_page(); // login successful
		}

		// Throttling: increase wrong attempts
		if ($throttles && !$lockedOut)
			$this->incrementLoginAttempts($request);

		$this->log(Input::get('login_name').' wrong login attempt', 'debug');

		// Login not successful, send back to form
		return Redirect::to($this->prefix.'/auth/login')
			->withInput(Input::except('password')) // send back the input (not the password) so that we can repopulate the form
			->withErrors(['error_test' => $this->getFailedLoginMessage()]);
	}


	/**
	 * Logout User
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getLogout()
	{
		$this->redirectAfterLogout = $this->prefix.'/auth/login';

		return $this->logout();
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
