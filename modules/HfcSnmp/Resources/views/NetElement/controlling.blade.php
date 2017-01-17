@extends ('Layout.default')


@section('content_top')

@stop


@section ('content')

	{{ Form::model($view_var, array('route' => array($form_update, $view_var->id), 'method' => 'put', 'files' => true)) }}

	<?php $i = 0 ?>

		@foreach ($panel_form_fields as $form_fields)

			@section ('Content_'.$i)
				@include ('Generic.form', [$form_fields, 'edit_view_save_button' => !$i])
			@stop

			@include('bootstrap.panel', ['content' => 'Content_'.$i, 'md' => 5])

			<?php $i++ ?>

		@endforeach
	
	{{ Form::close() }}


@stop