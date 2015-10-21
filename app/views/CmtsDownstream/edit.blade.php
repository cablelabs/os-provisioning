@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('Cmts.index', 'CMTS') }} / {{ HTML::linkRoute('CmtsDownstream.edit', 'CMTS-'.$cmtsdownstream->alias, $cmtsdownstream->id) }}
	
@stop

@section('content_left')

	<h2>Edit CMTS</h2>
	
	{{ Form::model($cmtsdownstream, array('route' => array('CmtsDownstream.update', $cmtsdownstream->id), 'method' => 'put')) }}

		@include('CmtsDownstream.form', $cmtsdownstream)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')

@stop