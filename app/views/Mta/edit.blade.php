@extends ('Layout.split')

@include ('mtas.header')

@section('content_top')

		{{ HTML::linkRoute('mta.index', 'MTAs') }}

@stop

@section('content_left')

	<h2>Edit MTA</h2>

	{{ Form::model($mta, array('route' => array('mta.update', $mta->id), 'method' => 'put')) }}

		@include('mtas.form', $mta)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

