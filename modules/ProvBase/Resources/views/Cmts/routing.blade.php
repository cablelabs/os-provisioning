<b>Required Provisioning Routes</b><br>
File: <i>/etc/sysconfig/network-scripts/route-...</i><br><br>

<div style="padding-left: 20px;">
<div class="note note-info">
# {{$view_var->hostname}}<br>

<?php $missing=false; ?>
@foreach ($view_var->ippools as $pool)
	@if($pool->ip_route_prov_exists())
		{{$pool->net}}/{{$pool->size()}} via {{$view_var->ip}} <br>
	@else
		<div class="label label-danger">
			{{$pool->net}}/{{$pool->size()}} via {{$view_var->ip}} <br>
		</div>
		<?php $missing=true; ?>
	@endif
@endforeach
</div>
</div>

@if ($missing)
	<div class="label label-danger">!!! ROUTES NOT FOUND !!!</div>
@endif
