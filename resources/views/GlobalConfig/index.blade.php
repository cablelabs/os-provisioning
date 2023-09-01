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

	<div id="tabs" v-on:wheel.stop="transformScroll" class="flex px-2 pt-0 overflow-x-auto scrollbar scrollbar-h-1 scrollbar-thumb-gray-500 scrollbar-track-gray-100 border-b border-gray-300 shadow-md dark:bg-slate-900 bg-slate-300 dark:border-slate-900 d-print-none whitespace-nowrap">
		<ul class="flex items-center w-full px-2 text-black dark:text-slate-100">
			@foreach($moduleModels as $slug => $model)
				<li v-on:click="setActiveTab('{{ $links[$slug]['name'] }}')"
					:class="tabStates['{{ $links[$slug]['name'] }}'] ? '!border-cyan-500 hover:border-cyan-500' : 'hover:border-white border-transparent'"
					class="pb-1 pt-2 !px-3 border-b-2 hover:bg-slate-200 dark:hover:bg-slate-800 {{ $loop->first ? 'border-cyan-500' : 'border-transparent hover:border-white'}}"
					role="tab">
					<a class="p-0 no-underline" href="#{{$slug}}" data-toggle="tab">
						<span class="text-gray-800 dark:text-slate-100">
							{{ $links[$slug]['local'] }}
						</span>
					</a>
				</li>
			@endforeach
		</ul>
	</div>
@DivClose()

@DivOpen(12)
		<div class="tab-content">
            @php
                $blade_type = 'form';
            @endphp
            @include('Generic.above_infos')

			@foreach($moduleModels as $slug => $model)
				<div :class="{'active': tabStates['{{ $links[$slug]['name'] }}']}"
					class="tab-pane pt-2 {{ $loop->first ? 'active' : ''}}"
					v-show="tabStates['{{ $links[$slug]['name'] }}']"
					role="tabpanel">
					{!! Form::model($model, array('route' => array($links[$slug]['link'].'.update', '1'), 'method' => 'put', 'files' => true) ) !!}
						@include('Generic.form', ['view_var' => $model, 'form_fields' => $form_fields[$slug]])
					{{ Form::close() }}
				</div>
			@endforeach
		</div>
@DivClose()
@stop
