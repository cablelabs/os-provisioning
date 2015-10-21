@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('IpPool.index', 'IP-Pool') }} / {{ HTML::linkRoute('IpPool.edit', 'IP-Pool: '.$ip_pool->cmts->hostname.'-'.$ip_pool->id, array($ip_pool->id)) }}

@stop

@section('content_left')

	<h2>Edit IP-Pool</h2>

	{{-- Form model populates the form fields with the array of the model (in this case $ip_pool) --}}	
	{{ Form::model($ip_pool, array('route' => array('IpPool.update', $ip_pool->id), 'method' => 'put')) }}

		@include('IpPool.form', $ip_pool)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')

@stop
