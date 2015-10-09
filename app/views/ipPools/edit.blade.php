@extends ('layouts.split')

@section('content_top')

	{{ HTML::linkRoute('ipPool.index', 'IP-Pool') }} / {{ HTML::linkRoute('ipPool.edit', 'IpPool-'.$ippool->cmts_gw_id, array($ippool->id)) }}

@stop

@section('content_left')

	<h2>Edit IP-Pool</h2>

	{{-- Form model populates the form fields with the array of the model (in this case $ippool) --}}	
	{{ Form::model($ippool, array('route' => array('ipPool.update', $ippool->id), 'method' => 'put')) }}

		@include('ipPools.form', $ippool)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')

@stop