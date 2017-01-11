@extends ('Layout.default')


@section('content_top')

@stop


@section ('content')

	<!-- TODO: include multiple panels (maybe for every different MibFile?) -->

	@section ('Content_1')

	{{ Form::model($view_var, array('route' => array($form_update, $view_var->id), 'method' => 'put', 'files' => true)) }}

		@include ('Generic.form', $form_fields)

	{{ Form::close() }}

	@stop

	@include('bootstrap.panel', ['content' => 'Content_1', 'md' => 5])

@stop