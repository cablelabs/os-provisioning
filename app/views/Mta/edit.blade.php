@extends ('Layout.split')

@include ('Mta.header')

@section('content_top')

		{{ HTML::linkRoute('Mta.index', 'MTAs') }}

@stop

@section('content_left')

	<h2>Edit MTA</h2>

	{{ Form::model($mta, array('route' => array('Mta.update', $mta->id), 'method' => 'put')) }}

		@include('Mta.form', $mta)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

