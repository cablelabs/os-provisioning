@extends ('Layout.split')

@include ('Modem.header')

@section('content_left')

	<h2>Edit Modem</h2>	

	{{ Form::model($modem, array('route' => array('Modem.update', $modem->id), 'method' => 'put')) }}

		@include('Modem.form', $modem)
	
	{{ Form::submit('Save') }}
	{{ Form::close() }}
	
@stop

@section('content_right')



@stop