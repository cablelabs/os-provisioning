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
@extends ('Layout.default')

@section ('content')

<div class="col-md-12 p-2">
	<div class="card dark:bg-dark-black-light">
		<div class="card-block">
		{{-- We need to include sections dynamically: always content left and if needed content right - more than 1 time possible --}}
		@yield ('content_left')
		</div>
		@if (isset($view_header_right))
			@include ('bootstrap.panel', array ('content' => 'content_right',
												'view_header' => $view_header_right,
												'md' => isset($index_left_md_size) ? $index_left_md_size : 12))
		@endif
	</div>
</div>

@stop
