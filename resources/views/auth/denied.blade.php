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
?>
<!doctype html>
<html>

    <head>
        <meta charset="utf-8">
        <title>NMS</title>
        @include ('bootstrap.header')
    </head>

    @include ('bootstrap.header')

    <body class="pace-top">

        <div class="error">
            <div class="error-code m-b-10">{{ trans('auth.accessDenied') }}! <i class="fa fa-warning"></i></div>
            <div class="error-content">
                <div class="error-message">
                    @if (isset($status))
                        {{ $status }}
                    @endif

                    @if (session('status'))
                        {{ session('status') }}
                    @endif

                    @if ( !empty($message))
                        {{ $message }} <a href="{{ route('Auth.logout') }}" class="btn btn-success">Logout</a>
                    @endif
                    <br><br>
                </div>
                <div>
                    <a href="{{ route('Home') }}" class="btn btn-success">{{ trans('view.error.backToHomePage') }}</a>
                </div>
            </div>
        </div>
        {{-- end error --}}

    </body>

    @include ('bootstrap.footer')
</html>


