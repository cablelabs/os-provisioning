<b>Provisioning Routing</b><br>
File: <i>/etc/sysconfig/network-scripts/route-...</i><br><br>

<div class="p-l-10">
<pre>{{--do not indent, it will show up in HTML--}}
# {{$view_var->hostname}}
<?php $cb->missing_pools=false; ?>
@foreach ($view_var->ippools as $pool)
@php
	$routeExists = $pool->ip_route_prov_exists();
@endphp
@if (! $routeExists)
<div class="label label-danger m-l-5">
@endif
 {{$pool->net.$pool->maskToCidr().' via '.($pool->version == '4' ? $view_var->ip : $view_var->ipv6)}}
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
