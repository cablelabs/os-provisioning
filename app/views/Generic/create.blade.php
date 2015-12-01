@extends ('Layout.split')

@if (!isset($own_top))
	@section('content_top')

		{{ HTML::linkRoute($route_name.'.index', $view_header) }}: 

		@if(isset($_GET) && $_GET != array())

			<?php
				/**
				 * Shows the html links of the related objects recursivly
				 */ 
				$s = '';

				$key        = array_keys($_GET)[0];
				$class_name = ucwords(explode ('_id', $key)[0]);
				$class      = current(preg_grep ('|.*?'.$class_name.'|i', BaseModel::get_models()));

				$view_var = new $class;

				$parent   = $view_var->find($_GET[$key]);

				while ($parent)
				{
					if ($parent)
					{
						$view = explode('\\',get_class($parent));
						$s = HTML::linkRoute(end($view).'.edit', $parent->get_view_link_title(), $parent->id).' / '.$s;
					}

					$parent = $parent->view_belongs_to();
				}
				
				echo $s;
			?>

		@endif

		{{ 'Create'}}

	@stop
@endif

@section('content_left')

	{{ Form::open(array('route' => array($route_name.'.store', 0), 'method' => 'POST', 'files' => true)) }}

		@include($form_path)

	{{ Form::close() }}

@stop
