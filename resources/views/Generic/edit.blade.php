@extends ('Layout.split')

@if (!isset($own_top))
	@section('content_top')

		{{ $link_header }}

	@stop
@endif


@section('content_left')

	{{ Form::model($view_var, array('route' => array($form_update, $view_var->id), 'method' => 'put', 'files' => true)) }}

		@include($form_path, $view_var)

	{{ Form::close() }}

@stop


@section('content_right')

	<?php

		if ($view_var->view_has_many())
			$view_header_0 = '';
		$i = 0;
	?>

	@foreach($view_var->view_has_many() as $view => $relation)

		<?php
			$i++;

			$model = new $model_name;
			$key   = strtolower($model->table).'_id';
			${"view_header_$i"} = \App\Http\Controllers\BaseViewController::translate("Assigned").' '.\App\Http\Controllers\BaseViewController::translate($view);
		?>

		@section("content_$i")
			@include('Generic.relation', [$relation, $view, $key])
		@stop

		@include ('bootstrap.panel', array ('content' => "content_$i", 'view_header' => ${"view_header_$i"}, 'md' => 3))

	@endforeach

@stop

