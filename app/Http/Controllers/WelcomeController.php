<?php namespace App\Http\Controllers;

class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// $this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
        $admin_port = env('HTTPS_ADMIN_PORT', '443');
        $ccc_port   = env('HTTPS_CCC_PORT', '443');

        // if same port, show start page
        if ($admin_port == $ccc_port)
            return $next($request);

		if (env('APP_ENV') == 'testing') {
			// $_SERVER['SERVER_PORT'] does not exist if running phpunit
			$server_port = \Request::getPort();
		}
		else {
			$server_port = $_SERVER['SERVER_PORT'];
		}

        if ($server_port == $admin_port)
            return redirect('admin/login');

        if ($server_port == $ccc_port)
            return redirect('customer');
		if (\App::isLocal())
			return view('welcome')
				->with(compact('head1', 'head2'));

		abort(404);
	}

}
