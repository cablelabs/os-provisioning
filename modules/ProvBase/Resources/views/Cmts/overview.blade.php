{{--

CMTS Config Overview Blade
This is the top level blade for CMTS config

--}}

<div class="panel-group" id="accordion">

@section ('group_1')
	@include ('provbase::Cmtsblade.setup')
@stop

@section ('group_2')
	@include ('provbase::Cmts.routing')
@stop

@section ('group_3')
	@include ('provbase::Cmtsblade.load')
@stop

@include('bootstrap.group', ['header' => 'Setup', 'content' => 'group_1', 'expand' => !\Modules\ProvBase\Entities\ProvBase::prov_ip_online()])
@include('bootstrap.group', ['header' => 'Routing', 'content' => 'group_2', 'expand' => ($cb->missing_pools || $cb->missing_routes)])
@include('bootstrap.group', ['header' => 'Config', 'content' => 'group_3'])

</div>
