@extends ('Layout.split')

@if (!isset($own_top))
	@section('content_top')

		{{ HTML::linkRoute($route_name.'.index', $view_header) }}: 

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
					// Need to be tested !
					$tmp = explode('\\',get_class($parent));
					$view = end($tmp);
					$s = HTML::linkRoute($view.'.edit', $parent->get_view_link_title(), $parent->id).' / '.$s;
				}
			}
			while ($parent);

			echo $s;
		?>

		{{ HTML::linkRoute($route_name.'.edit', $view_var->get_view_link_title(), $view_var->id) }}

	@stop
@endif

@section('content_left')

	<?php
		if (!isset($form_update))
			$form_update = $route_name.'.update';
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
				$model = new $model_name;
				$key   = strtolower($model->table).'_id';
				$view_header_right .= ' Assigned '.$model->get_view_header();
			?>

			@include('Generic.relation', [$relations, $view, $key])
			
			<br> </br>
			<hr> <hr>

	
	@endforeach

@stop
