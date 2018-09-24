<?php

namespace App\Http\Controllers\Auth;

use Log;
use Module;
use Session;
use GlobalConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    private $prefix = 'admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('adminRedirect', ['except' => 'logout']);
    }

    /**
     * Return a instance of the used guard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    /**
     * Change Login Check Field to login_name
     * Laravel Standard: email
     */
    public function username()
    {
        return 'login_name';
    }

    /**
     * Show Login Page
     *
     * @return view
     */
    public function showLoginForm()
    {
        $prefix = $this->prefix;
        $globalConfig = GlobalConfig::first();
        $head1 = $globalConfig->headline1;
        $head2 = $globalConfig->headline2;
        $image = 'main-pic-1.jpg';

        \App::setLocale(\App\Http\Controllers\BaseViewController::get_user_lang());

        return \View::make('auth.login', compact('head1', 'head2', 'prefix', 'image'));
    }

    /**
     * Show Default Page after successful login
     *
     * TODO: Redirect to a global overview page
     *
     * @return type Redirect
     */
    private function redirectTo()
    {
        $user = Auth::user();
        $roles = $user->roles;
        $activeModules = Module::collections();

        if (! count($roles)) {
            return \View::make('auth.denied')->with('message', 'No roles assigned. Please contact your administrator.');
        }

        Log::debug($user->login_name.' logged in successfully!');

        if (! $activeModules->has('Dashboard')) {
            if (($activeModules->has('ProvBase') && ! $user->can('view Contract')) ||
                 (! $activeModules->has('ProvBase'))) {
                if (($activeModules->has('HfcReq') && ! $user->can('view NetElement')) ||
                    (! $activeModules->has('HfcReq'))) {
                    return $this->prefix.'/Config';
                } else {
                    return $this->prefix.'/NetElement';
                }
            } else {
                return $this->prefix.'/Contract';
            }
        } else {
            return $this->prefix.'/';
        }
    }

    /**
     * This function will be called if user has no access to a certain area
     * or has no valid login at all.
     */
    public function denied($message)
    {
        return \View::make('auth.denied')->with('message', $message);
    }
}
