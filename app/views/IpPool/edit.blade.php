@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('IpPool.index', 'IP-Pool') }} / {{ HTML::linkRoute('IpPool.edit', 'IP-Pool: '.$ippool->cmts->hostname.'-'.$ippool->id, array($ippool->id)) }}

@stop

@section('content_left')

	<h2>Edit IP-Pool</h2>

	{{-- Form model populates the form fields with the array of the model (in this case $ippool) --}}	
	{{ Form::model($ippool, array('route' => array('IpPool.update', $ippool->id), 'method' => 'put')) }}

		@include('IpPool.form', $ippool)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')

@stop
