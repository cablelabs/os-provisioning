{{-- The generic ip bundle --}}

@php
    $cb->missing_routes = false;
@endphp

@foreach ($view_var->ippools->where('version', '4') as $pool)
@if ($pool->ip_route_online())
 ip address {{$pool->router_ip}} {{$pool->netmask}} {{$pool->is_secondary()}}
@else
<div class="label label-danger m-l-5">
 ip address {{$pool->router_ip}} {{$pool->netmask}} {{$pool->is_secondary()}}
</div>
<?php $cb->missing_routes=true; ?>
@endif
@endforeach
@foreach ($view_var->ippools->where('version', '6') as $pool)
@if ($pool->ip_route_online())
 ipv6 address {{$pool->router_ip.$pool->netmask}}
@else
<div class="label label-danger m-l-5">
 ipv6 address {{$pool->router_ip.$pool->netmask}}
</div>
<?php $cb->missing_routes=true; ?>
@endif
@endforeach
