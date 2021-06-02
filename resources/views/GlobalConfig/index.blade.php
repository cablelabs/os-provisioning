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
@extends ('Layout.split84-nopanel')

@section('content_top')

<li class="active">
	<a href="{{route($route_name)}}">
	{!! \App\Http\Controllers\BaseViewController::__get_view_icon(isset($view_var[0]) ? $view_var[0] : null).$view_header !!}
	</a>
</li>

@stop


@section('content_left')

{{-- Headline: means icon followed by headline --}}
@DivOpen(12)
	<h1 class="page-header">
		{!! \App\Http\Controllers\BaseViewController::__get_view_icon(isset($view_var[0]) ? $view_var[0] : null).$view_header !!}
	</h1>

	<ul class="nav nav-pills d-flex nav-fill" id="SettingsTab">
		@foreach($moduleModels as $count => $model)
			<li class="nav-item">
				<a href="#settings-{{Str::slug($links[$count]['name'],'_')}}" data-toggle="pill"> {{ $links[$count]['name'] }} </a>
			</li>
		@endforeach
	</ul>
@DivClose()

@DivOpen(12)
		<div class="tab-content">
            @php
                $blade_type = 'form';
            @endphp
            @include('Generic.above_infos')

			@foreach($moduleModels as $count => $model)
				<div class="tab-pane fade in" id="settings-{{Str::slug($links[$count]['name'],'_')}}" role="tabpanel">
					{!! Form::model($model, array('route' => array($links[$count]['link'].'.update', '1'), 'method' => 'put', 'files' => true) ) !!}
						@include('Generic.form', ['view_var' => $model, 'form_fields' => $form_fields[$count]])
					{{ Form::close() }}
				</div>
			@endforeach
		</div>
@DivClose()
@stop
