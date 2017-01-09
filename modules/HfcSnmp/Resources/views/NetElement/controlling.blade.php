@extends ('Layout.default')

@section ('content')

	@section ('Content_1')

	{{ Form::model($view_var, array('route' => array($form_update, $view_var->id), 'method' => 'put', 'files' => true)) }}

		@include ('Generic.form', $form_fields)

	{{ Form::close() }}

	@stop

	@include('bootstrap.panel', ['content' => 'Content_1', 'md' => 11])

@stop