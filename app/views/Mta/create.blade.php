<!-- Begin of app/views/mtas/create.blade.php -->

@extends('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('mta.index', 'MTAs') }}

@stop

@section('content_left')

	<h2>Create MTA</h2>

	{{ Form::open(array('route' => array('mta.store', 0), 'method' => 'POST')) }}

		@include('mtas.form', array('mta' => null))

	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop
