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
{{--

@param $headline: the link header description in HTML

@param $form_path: the form view to be displayed inside this blade (mostly Generic.edit)
@param $route_name: the base route name of this object class which will be added

--}}

@extends ('Layout.split-nopanel')

@section('content_top')

	{!! $headline !!}
	<li><a onMouseOver="this.style.backgroundColor='#FFFFFF'"><span class="text-info">{{ \App\Http\Controllers\BaseViewController::translate_view('Create', 'Header') }}</span></a></li>

@stop


@section('content_left')

	{{ Form::open(['route' => [$route_name.'.store'], 'method' => 'POST', 'files' => true]) }}

		@include($form_path)

	{{ Form::close() }}

@stop
