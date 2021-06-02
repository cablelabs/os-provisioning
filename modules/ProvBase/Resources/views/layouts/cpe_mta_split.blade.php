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
@extends ('Layout.split-nopanel')

@section('content_top')

	@include ('provbase::layouts.top', ['type' => $type])

@stop

@section ('content_left')

<div class="row">

	{{-- We need to include sections dynamically: always content left and if needed content right - more than 1 time possible --}}

	<div class="col-md-7 ui-sortable">
		@include ('bootstrap.panel', array ('content' => 'content_dash', 'view_header' => 'Dashboard / Forecast', 'i' => 1, 'height' => 'auto'))
		@include ('bootstrap.panel', array ('content' => 'content_log', 'view_header' => 'DHCP Log', 'i' => 2, 'height' => 'auto'))
	</div>

	<div class="col-md-5 ui-sortable">
		@include ('bootstrap.panel', array ('content' => 'content_ping', 'view_header' => 'Ping Test', 'i' => 3, 'height' => 'auto'))
		@include ('bootstrap.panel', array ('content' => 'content_lease', 'view_header' => 'DHCP Lease', 'i' => 4, 'height' => 'auto'))
		@if (isset($configfile))
			@include ('bootstrap.panel', array ('content' => 'content_configfile', 'view_header' => 'Configfile', 'i' => 5, 'height' => 'auto'))
		@endif
</div>


</div>

@stop
