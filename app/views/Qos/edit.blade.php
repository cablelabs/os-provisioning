@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('Qos.index', 'quality') }} / {{ HTML::linkRoute('Qos.edit', 'quality-'.$quality->name, array($quality->id)) }}
	
@stop

@section('content_left')

	<h2>Edit quality</h2>
	
	{{ Form::model($quality, array('route' => array('Qos.update', $quality->id), 'method' => 'put')) }}

		@include('Qos.form', $quality)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')

@stop
