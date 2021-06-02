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
