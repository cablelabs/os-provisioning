@extends ('Layout.split')

@if (!isset($own_top))
	@section('content_top')

		{{ HTML::linkRoute($route_name.'.index', \App\Http\Controllers\BaseController::translate(trim(str_replace(\App\Http\Controllers\BaseController::translate('Create '), '', $view_header)))) }}: 

		@if(isset($_GET) && $_GET != array())

			<?php
				/**
				 * Shows the html links of the related objects recursivly
				 * TODO: should be moved either to controller or somewhere else
				 */ 
				$s = '';

				$key        = array_keys($_GET)[0];
				$class_name = ucwords(explode ('_id', $key)[0]);
				$class      = BaseModel::_guess_model_name($class_name);

				if (class_exists($class))
				{
					$view_var = new $class;
					$parent   = $view_var->find($_GET[$key]);


					while ($parent)
					{
						if ($parent)
						{
							$view = explode('\\',get_class($parent));
							$s = HTML::linkRoute(end($view).'.edit', \App\Http\Controllers\BaseController::translate($parent->get_view_link_title()), $parent->id).' / '.$s;
						}

						$parent = $parent->view_belongs_to();
					}
					
					echo $s;
				}
			?>

		@endif

		{{ \App\Http\Controllers\BaseController::translate('Create') }}

	@stop
@endif

@section('content_left')

	{{ Form::open(array('route' => array($route_name.'.store', 0), 'method' => 'POST', 'files' => true)) }}

		@include($form_path)

	{{ Form::close() }}

@stop
