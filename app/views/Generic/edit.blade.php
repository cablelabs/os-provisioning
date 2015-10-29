@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute($model_name.'.index', $view_header) }} / {{ HTML::linkRoute($model_name.'.edit', $view_var->get_view_link_title(), $view_var->id) }}

@stop

@section('content_left')

	{{ '<h2>Edit '.$view_header.'</h2>' }}

	{{ Form::model($view_var, array('route' => array($model_name.'.update', $view_var->id), 'method' => 'put', 'files' => true)) }}

		@include($form_path, $view_var)

	{{ Form::close() }}

@stop
