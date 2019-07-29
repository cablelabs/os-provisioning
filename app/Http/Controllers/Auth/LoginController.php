<?php

namespace App\Http\Controllers\Auth;

use App;
use Log;
use Module;
use Bouncer;
use GlobalConfig;
use App\BaseModel;
use Carbon\Carbon;
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

        return \View::make('auth.login', compact('head1', 'head2', 'prefix', 'image'));
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\User  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Set (cached) session key with model namespaces for authorization functionaity
        BaseModel::get_models();

        $request->session()->put('GlobalNotification', []);
        App::setLocale(\App\Http\Controllers\BaseViewController::get_user_lang());

        if ($user->isPasswordExpired() || $user->isFirstLogin()) {
            $request->session()->flash('GlobalNotification', [
                'shouldChangePassword' => [
                    'message' => 'shouldChangePassword',
                    'level' => 'danger',
                    'reason' => $user->isPasswordExpired() ? 'PasswordExpired' : 'newUser',
                ],
            ]);
        }

        $user->update(['last_login_at' => Carbon::now()]);
    }

    /**
     * Show Default Page after successful login
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

        if ($activeModules->has('Dashboard')) {
            return route('Dashboard.index');
        }

        if ($activeModules->has('ProvBase') && Bouncer::can('view', \Modules\ProvBase\Entities\Contract::class)) {
            return route('Contract.index');
        }

        if (Bouncer::can('view', \Modules\HfcReq\Entities\NetElement::class)) {
            return route('NetElement.index');
        }

        return route('GlobalConfig.index');
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
