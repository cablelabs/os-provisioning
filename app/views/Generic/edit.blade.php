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

	{{ '<h2>Edit '.$view_header.'</h2>' }}

	{{ Form::model($view_var, array('route' => array($model_name.'.update', $view_var->id), 'method' => 'put', 'files' => true)) }}

		@include($form_path, $view_var)

	{{ Form::close() }}

@stop
