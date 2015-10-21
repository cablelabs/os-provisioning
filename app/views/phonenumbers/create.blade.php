@extends('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('phonenumber.index', 'Phonenumbers') }}

@stop

@section('content_left')

	<h2>Create Phonenumber</h2>

	{{ Form::open(array('route' => array('phonenumber.store', 0), 'method' => 'POST')) }}

		@include('phonenumbers.form', array('phonenumber' => null))

	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop
