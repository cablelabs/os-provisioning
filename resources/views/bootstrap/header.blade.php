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
      <!-- ================== Pace Loading indicator ================== -->
      <script src="{{asset('components/assets-admin/plugins/pace/pace.min.js')}}"></script>
      <!-- ================== BEGIN BASE CSS STYLE ================== -->
      <!-- JQuery UI & Bootstrap -->
      <link href="{{asset('components/assets-admin/plugins/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet" />
      <link href="{{asset('components/assets-admin/plugins/bootstrap4/css/bootstrap.min.css')}}" rel="stylesheet" />

      <!-- icons -->
      <link href="{{asset('components/assets-admin/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" />

      <!-- Theme -->
      <link href="{{asset('components/assets-admin/css/animate.min.css')}}" rel="stylesheet" />
      <link href="{{asset('components/assets-admin/css/style.css')}}" rel="stylesheet" />
      @if(isset($user))
      <link href="{{asset('components/assets-admin/css/config/'.$user->theme_color)}}" rel="stylesheet" />
        @else
       <link href="{{asset('components/assets-admin/css/config/default_theme_config.css')}}" rel="stylesheet" />
      @endif
      <link href="{{asset('components/assets-admin/css/style-bs4.css')}}" rel="stylesheet" />
      <link href="{{asset('components/assets-admin/css/style-responsive.min.css')}}" rel="stylesheet" />
      <link href="{{asset('components/assets-admin/css/theme/default.css')}}" rel="stylesheet" id="theme" />

      <!-- Plugins -->
      <link href="{{asset('components/assets-admin/plugins/jstree/dist/themes/default/style.min.css')}}" rel="stylesheet" />

      <link href="{{asset('components/assets-admin/plugins/ionRangeSlider/css/ion.rangeSlider.css')}}" rel="stylesheet" />

      <link href="{{asset('components/assets-admin/plugins/switchery/switchery.css')}}" rel="stylesheet" />
      <!-- SITE -->
      <link href="{{ mix('css/app.css') }}" rel="stylesheet" />

      @if (request()->is('customer*'))
      <link href="{{ mix('css/ccc.css') }}" rel="stylesheet" />
      @endif
      <!-- ================== END BASE CSS STYLE ================== -->
