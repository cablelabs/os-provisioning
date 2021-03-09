<?php

namespace App\Http\Controllers;

class WelcomeController extends Controller
{
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
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index()
    {
        if ($_SERVER['SERVER_PORT'] == config('app.cccPort')) {
            return redirect(route('HomeCcc'));
        }

        if (auth()->user()) {
            return redirect((new Auth\LoginController())->redirectTo());
        }

        if ($_SERVER['SERVER_PORT'] == config('app.adminPort')) {
            return redirect(route('adminLogin'));
        }

        return abort(404);
    }
}
