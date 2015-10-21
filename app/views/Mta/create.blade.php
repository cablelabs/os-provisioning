<!-- Begin of app/views/mtas/create.blade.php -->

@extends('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('Mta.index', 'MTAs') }}

@stop

@section('content_left')

	<h2>Create MTA</h2>

	{{ Form::open(array('route' => array('Mta.store', 0), 'method' => 'POST')) }}

		@include('Mta.form', array('mta' => null))

	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop
