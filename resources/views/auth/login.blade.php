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

    <script>setTimeout("document.getElementById('error').style.display='none';", 3000);</script>
</head>

<body class="pace-top">

    {{-- Background Image --}}
    <div class="login-cover">
        <div class="login-cover-image">
            <img id="login-img" class="mh-100 w-100" data-id="login-cover-image" src="{{ $bgImageRoute }}">
        </div>
        <div class="login-cover-bg"></div>
        @if ($loginPage == 'customer')
            <div id="nmsprime-brand" class="brand">
                <img src="{{asset('images/nmsprime-logo-poweredby.png')}}" class="img-fluid">
            </div>
        @endif
    </div>

    {{-- begin login --}}
    <div class="login login-v2 animated fadeInDown">
        <div class="login-content">
            @if ($logo)
                <div class="brand">
                    <img src="{{$logo}}" class="img-fluid">
                </div>
            @endif

            @if ($head1 || $head2)
                <div class="row">
                    <div class="col-9 d-flex flex-column justify-content-center align-items-center">
                        <div style="font-size: 18px">{{ $head1 }}</div>
                        <div style="font-size: 14px">{{ $head2 }}</div>
                    </div>
                    <div class="icon col-3" style="font-size: 60px">
                        <i class="fa fa-sign-in" style="font-color:#b7b7b7;"></i>
                    </div>
                </div>
            @endif

            <div class="m-t-20">
                {{ Form::open(array('url' => $prefix.'/login')) }}
                @if (isset($intended) && $intended)
                    <div class="note note-warning">
                        <div class="mb-2">
                            {{ trans('view.redirectNote') }}:
                        </div>
                        <div class="badge font-weight-normal" style="font-family: monospace;">{{ $intended }}</div>
                    </div>
                @endif
                {{-- Username --}}
                <div class="form-group m-b-20">
                    {{ Form::text('login_name', Request::old('login_name'), array('autofocus'=>'autofocus', 'class' => "form-control input-lg", 'placeholder' => \App\Http\Controllers\BaseViewController::translate_label($loginPage == 'admin' ? 'Username' : 'Customer number'), 'style' => 'simple')) }}
                </div>

                {{-- Password --}}
                <div class="form-group m-b-20">
                    {{ Form::password('password', array('autofocus'=>'autofocus', 'class' => "form-control input-lg", 'placeholder' => \App\Http\Controllers\BaseViewController::translate_label('Password'), 'style' => 'simple')) }}
                </div>

                {{-- Remember Checkbox --}}
                <div class="form-group m-b-20 d-flex align-items-center">
                    <input align="left" class="mt-0 mb-2" name="remember" type="checkbox" value="1">
                    <label for="remember" class="control-label px-2">
                        {{ \App\Http\Controllers\BaseViewController::translate_label('Remember Me') . '!' }}
                    </label>
                </div>

                {{-- Error Message --}}
                <div class="m-t-20">
                    <p align="center"><font id="error" color="yellow">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </font></p>
                </div>
                <br>
                {{-- Login Button --}}
                <div class="login-buttons">
                    <button type="submit" class="btn btn-success btn-block btn-lg">{{ \App\Http\Controllers\BaseViewController::translate_label('Sign me in') }}</button>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
    {{-- end login --}}

    @include ('bootstrap.footer')

</body>
</html>
