@extends ('layouts.split')

@include ('phonenumbers.header')

@section('content_top')

		{{ HTML::linkRoute('phonenumber.index', 'Phonenumbers') }}

@stop

@section('content_left')

	<h2>Edit Phonenumber</h2>

	{{ Form::model($phonenumber, array('route' => array('phonenumber.update', $phonenumber->id), 'method' => 'put')) }}

		@include('phonenumbers.form', $phonenumber)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop
