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

NETGW Config Overview Blade
This is the top level blade for NETGW config

--}}

<div class="panel-group" id="accordion">

@section ('group_1')
	@include ('provbase::NetGwBlade.setup')
@stop

@section ('group_2')
	@include ('provbase::NetGw.routing')
@stop

@section ('group_3')
	@include ('provbase::NetGwBlade.load')
@stop

@include('bootstrap.group', ['header' => 'Setup', 'content' => 'group_1', 'expand' => !\Modules\ProvBase\Entities\ProvBase::prov_ip_online()])
@include('bootstrap.group', ['header' => 'Routing / IPs', 'content' => 'group_2', 'expand' => ($cb->missing_pools || $cb->missing_routes)])
@include('bootstrap.group', ['header' => 'Config', 'content' => 'group_3'])

</div>
