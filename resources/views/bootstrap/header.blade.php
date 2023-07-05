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
      <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
      <meta content="" name="description" />
      <meta content="" name="author" />
      <meta content="{{ csrf_token() }}" name="csrf-token" />
      <!-- Theme -->
      <script src="{{ asset('js/pace.js') }}"></script>
      @if(isset($user) && $user->theme_color !== 'browser_preferences')
            <link href="{{asset('components/assets-admin/css/config/'.$user->theme_color)}}" rel="stylesheet" />
      @elseif(!isset($user) || (isset($user) && $user->theme_color !== 'browser_preferences'))
            <link href="{{asset('components/assets-admin/css/config/default_theme_config.css')}}" rel="stylesheet" />
      @endif
      <!-- SITE -->
      <link href="{{ mix('css/app.css') }}" rel="stylesheet" />
      @if (request()->is('customer*'))
            <link href="{{ mix('css/ccc.css') }}" rel="stylesheet" />
      @endif
      <!-- ================== END BASE CSS STYLE ================== -->
