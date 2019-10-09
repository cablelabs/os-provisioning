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
