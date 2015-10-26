@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('Qos.index', 'quality') }} / {{ HTML::linkRoute('Qos.edit', 'quality-'.$qos->name, array($qos->id)) }}
	
@stop

@section('content_left')

	<h2>Edit Quality</h2>
	
	{{ Form::model($qos, array('route' => array('Qos.update', $qos->id), 'method' => 'put')) }}

		@include('Qos.form', $qos)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')

@stop
