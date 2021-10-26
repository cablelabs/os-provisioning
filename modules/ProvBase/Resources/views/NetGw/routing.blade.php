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
<b>Provisioning Routing</b><br>
File: <i>/etc/sysconfig/network-scripts/route-...</i><br><br>

<div class="p-l-10">
<pre>{{--do not indent, it will show up in HTML--}}
# {{$view_var->hostname}}
<?php $cb->missing_pools=false; ?>
@foreach ($view_var->ippools as $pool)
@php
	$routeExists = $pool->ip_route_prov_exists($view_var);
@endphp
@if (! $routeExists)
<div class="label label-danger m-l-5">
@endif
 {{$pool->net.' via '.($pool->version == '4' ? $view_var->ip : $view_var->ipv6)}}
@if (! $routeExists)
</div>
@endif
@endforeach
</pre>
</div>
@if ($cb->missing_pools)
	<div class="label label-danger">!!! ROUTES NOT FOUND !!!</div>
@endif

<hr>

<b>NetGw Bundle Interface</b><br><br>

<div class="p-l-10">
<pre>
@include ('provbase::NetGwBlade.bundle_ips')
</pre>
</div>

@if ($cb->missing_routes)
	<div class="label label-danger">!!! NetGw IP OFFLINE !!!</div>
@endif
