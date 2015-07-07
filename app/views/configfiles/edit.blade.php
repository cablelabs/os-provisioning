@extends ('layouts.split')

@section('content_top')

	
@stop

@section('content_left')
	
	{{ Form::model($configfile, array('route' => array('configfile.update', $configfile->id), 'method' => 'put')) }}

		@include('configfiles.form', $configfile)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')


@stop