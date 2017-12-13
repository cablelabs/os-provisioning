{{--

The generic ip bundle

--}}

<?php $cb->missing_routes=false; ?>
@foreach ($view_var->ippools as $pool)
@if($pool->ip_route_online())
 ip address {{$pool->router_ip}} {{$pool->netmask}} {{$pool->is_secondary()}}
@else
<div class="label label-danger">
 ip address {{$pool->router_ip}} {{$pool->netmask}} {{$pool->is_secondary()}}
</div>
<?php $cb->missing_routes=true; ?>
@endif
@endforeach
