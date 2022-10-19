<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Http\Controllers\Auth;

use App;
use App\BaseModel;
use Carbon\Carbon;
use App\GlobalConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Nwidart\Modules\Facades\Module;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Silber\Bouncer\BouncerFacade as Bouncer;
use App\Http\Controllers\GlobalConfigController;
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
        $intended = null;
        $prefix = $this->prefix;
        $globalConfig = GlobalConfig::first();
        $head1 = $globalConfig->headline1;
        $head2 = $globalConfig->headline2;
        $loginPage = 'admin';
        $logo = asset('images/nmsprime-logo-white.png');

        $bgImageRoute = asset('images/main-pic-1.jpg');
        if ($globalConfig->login_img && is_file(storage_path(GlobalConfigController::BG_IMAGES_PATH_REL.$globalConfig->login_img))) {
            $bgImageRoute = '/storage/base/bg-images/'.$globalConfig->login_img;
        }

        if (session()->has('url.intended') && $pos = strpos($url = session('url.intended'), 'admin')) {
            $intended = substr($url, $pos + 6); // pos + admin/
        }

        return \View::make('auth.login', compact('head1', 'head2', 'prefix', 'bgImageRoute', 'loginPage', 'logo', 'intended'));
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        if (! $this->guard()->attempt($this->credentials($request), $request->filled('remember'))) {
            return false;
        }

        $user = Auth::user();

        if (! $user->active) {
            Log::info("User {$user->login_name} denied: User is inactive");

            return false;
        }

        if (! count($user->roles)) {
            Log::info("User {$user->login_name} denied: User has no roles assigned");

            return false;
        }

        return true;
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

        self::setDashboardNotifications();

        // do not try to update user on ha slave machines
        if (\Module::collections()->has('ProvHA')) {
            if ('master' != config('provha.hostinfo.ownState')) {
                return;
            }
        }

        $user->update(['last_login_at' => Carbon::now()]);
    }

    /**
     * Set global notification messages in session on login
     */
    private static function setDashboardNotifications()
    {
        $alerts = [];
        $conf = GlobalConfig::first();

        if ($conf->alert1) {
            $alerts['alert1'] = [
                'message' => $conf->alert1,
                'level' => 'info',
                'reason'=>'', ];
        }

        if ($conf->alert2) {
            $alerts['alert2'] = [
                'message' => $conf->alert2,
                'level' => 'warning',
                'reason' => '', ];
        }

        if ($conf->alert3) {
            $alerts['alert3'] = [
                'message' => $conf->alert3,
                'level' => 'danger',
                'reason' => '', ];
        }

        \Session::flash('DashboardNotification', $alerts);
    }

    /**
     * Show Default Page after successful login
     *
     * @return type Redirect
     */
    public function redirectTo()
    {
        $user = Auth::user();
        $activeModules = Module::collections();

        Log::debug($user->login_name.' logged in successfully!');

        if (
            $activeModules->has('CoreMon') &&
            $netelement = \Modules\HfcReq\Entities\NetElement::where('netelementtype_id', 1)->orderBy('id')->first()
        ) {
            return route('CoreMon.net.overview', [$netelement]);
        }

        if ($activeModules->has('Ticketsystem') && $this->isMobileDevice()) {
            return route('TicketReceiver.index');
        }

        if ($user->initial_dashboard) {
            return route($user->initial_dashboard);
        }

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
     * Detect Mobile Device with regular expression from http://detectmobilebrowsers.com/
     *
     * @return bool
     */
    protected function isMobileDevice(): bool
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        return preg_match(isMobileRegEx(1), $useragent) || preg_match(isMobileRegEx(2), substr($useragent, 0, 4));
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
