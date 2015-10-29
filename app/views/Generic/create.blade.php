@extends ('Layout.split')

@if (!isset($own_top))
	@section('content_top')

		{{ HTML::linkRoute($model_name.'.index', $view_header) }}: 

		<?php
			/**
			 * Shows the html links of the related objects recursivly
			 */ 
			$s = '';

			$key      = array_keys($_GET)[0];
			$class    = 'Models\\'.ucwords(explode ('_id', $key)[0]);
			$view_var = new $class;

			$parent   = $view_var->find($_GET[$key]);

			while ($parent)
			{
				if ($parent)
				{
					$view = explode('\\',get_class($parent))[1];
					$s = HTML::linkRoute($view.'.edit', $parent->get_view_link_title(), $parent->id).' / '.$s;
				}

				$parent = $parent->view_belongs_to();
			}
			
			echo $s.' Create';
		?>

	@stop
@endif

@section('content_left')

	{{ '<h2>Create '.$model_name.'</h2>' }}

	{{ Form::open(array('route' => array($model_name.'.store', 0), 'method' => 'POST', 'files' => true)) }}

		@include($form_path, array ('cmts' => null))

	{{ Form::close() }}

@stop
