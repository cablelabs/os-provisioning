{{--

The generic ip bundle

--}}

<?php $cb->missing_routes=false; ?>
@foreach ($view_var->ippools as $pool)
@if($pool->ip_route_prov_exists())
 {{$pool->net}}/{{$pool->size()}} via {{$view_var->ip}}
@else
<div class="label label-danger">
 {{$pool->net}}/{{$pool->size()}} via {{$view_var->ip}}
</div>
<?php $cb->missing_routes=true; ?>
@endif
@endforeach
