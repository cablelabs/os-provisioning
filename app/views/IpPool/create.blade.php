@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('IpPool.index', 'IP-Pools') }}

@stop

@section('content_left')

	<h2>Create IP-Pool</h2>
	
	{{-- open generates html for form
		 with route to store method and parameter to hand over (in this case we hand over a zero to the store method)

	--}}

	{{ Form::open(array('route' => array('IpPool.store', 0), 'method' => 'POST')) }}

		@include('ipPools.form', array ('ipPool' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop