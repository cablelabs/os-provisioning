@extends('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('mta.index', 'Mtas') }}

@stop

@section('content_left')

	<h2>Create MTAs</h2>

	{{ Form::open(array('route' => array('mtas.store', 0), 'method' => 'POST')) }}

		@include('mtas.form', array ('mtas' => null))

	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop
