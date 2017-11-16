<b>Provisioning Routing</b><br>
File: <i>/etc/sysconfig/network-scripts/route-...</i><br><br>

<div style="padding-left: 20px;">
<pre>{{--do not indent, it will show up in HTML--}}
# {{$view_var->hostname}}
<?php $missing=false; ?>
@foreach ($view_var->ippools as $pool)
@if($pool->ip_route_prov_exists())
 {{$pool->net}}/{{$pool->size()}} via {{$view_var->ip}}
@else
<div class="label label-danger">
 {{$pool->net}}/{{$pool->size()}} via {{$view_var->ip}}
</div>
<?php $missing=true; ?>
@endif
@endforeach
</pre>
</div>
@if ($missing)
	<div class="label label-danger">!!! ROUTES NOT FOUND !!!</div>
@endif

<hr>

<b>CMTS Routing</b><br>
<i>interface bundle 1</i><br><br>

<div style="padding-left: 20px;">
<pre>
@include ('provbase::Cmtsblade.bundle_ips')
</pre>
</div>

@if ($missing)
	<div class="label label-danger">!!! IP OFFLINE !!!</div>
@endif
