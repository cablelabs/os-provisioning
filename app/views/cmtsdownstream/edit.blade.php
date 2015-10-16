@extends ('layouts.split')

@section('content_top')

	{{ HTML::linkRoute('cmts.index', 'CMTS') }} / {{ HTML::linkRoute('cmtsdownstream.edit', 'CMTS-'.$cmtsdownstream->alias, $cmtsdownstream->id) }}
	
@stop

@section('content_left')

	<h2>Edit CMTS</h2>
	
	{{ Form::model($cmtsdownstream, array('route' => array('cmtsdownstream.update', $cmtsdownstream->id), 'method' => 'put')) }}

		@include('cmtsdownstream.form', $cmtsdownstream)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')

@stop