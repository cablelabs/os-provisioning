@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('quality.index', 'quality') }} / {{ HTML::linkRoute('quality.edit', 'quality-'.$quality->name, array($quality->id)) }}
	
@stop

@section('content_left')

	<h2>Edit quality</h2>
	
	{{ Form::model($quality, array('route' => array('quality.update', $quality->id), 'method' => 'put')) }}

		@include('qualities.form', $quality)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')

@stop