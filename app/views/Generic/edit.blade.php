@extends ('Layout.split')

@if (!isset($own_top))
	@section('content_top')

		{{ HTML::linkRoute($model_name.'.index', $view_header) }}: 

		<?php
			/**
			 * Shows the html links of the related objects recursivly
			 */ 
			$s = '';
			$parent = $view_var;
			do
			{
				$parent = $parent->view_belongs_to();
				
				if ($parent)
				{
					$view = explode('\\',get_class($parent))[1];
					$s = HTML::linkRoute($view.'.edit', $parent->get_view_link_title(), $parent->id).' / '.$s;
				}
			}
			while ($parent);

			echo $s;
		?>

		{{ HTML::linkRoute($model_name.'.edit', $view_var->get_view_link_title(), $view_var->id) }}

	@stop
@endif

@section('content_left')

	<?php
		if (!isset($form_update))
			$form_update = $model_name.'.update';
	?>

	{{ Form::model($view_var, array('route' => array($form_update, $view_var->id), 'method' => 'put', 'files' => true)) }}

		@include($form_path, $view_var)

	{{ Form::close() }}

@stop

{{-- We should add a new section for each relation --}}
@section('content_right')

	<?php 

		if ($view_var->view_has_many())
			$view_header_right = '';
	?>

	@foreach($view_var->view_has_many() as $view => $relations)

			<?php
				$key = strtolower($model_name).'_id';

				$model_name = 'Models\\'.$view;
				$model = new $model_name;
				$view_header_right .= ' Assigned '.$model->get_view_header();
			?>

			@include('Generic.relation', [$relations, $view, $key])
			
			<br> </br>
			<hr> <hr>

	
	@endforeach

@stop