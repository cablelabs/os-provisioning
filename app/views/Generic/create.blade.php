@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute($model_name.'.index', $view_header) }}

@stop

@section('content_left')

	{{ '<h2>Create '.$view_header.'</h2>' }}
	
	{{ Form::open(array('route' => array($model_name.'.store', 0), 'method' => 'POST')) }}

		@include($form_path, array ('cmts' => null))
	
	{{ Form::close() }}

@stop