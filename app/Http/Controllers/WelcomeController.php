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
        $server_port = \Request::getPort();
        $admin_port = env('HTTPS_ADMIN_PORT', '8080');
        $ccc_port = env('HTTPS_CCC_PORT', '443');

        if ($server_port == $admin_port) {
            return redirect(route('adminLogin'));
        }

        if ($server_port == $ccc_port) {
            return redirect(route('customerLogin'));
        }

        abort(404);
    }
}
