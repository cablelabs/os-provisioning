@extends ('Layout.split')

@if (!isset($own_top))
	@section('content_top')


		<?php

			$s = HTML::linkRoute($route_name.'.index', str_replace('Edit', '', $view_header));
			if (in_array($route_name, $config_routes))
				$s = HTML::linkRoute('Config.index', 'Global Configurations');

			echo $s.': ';

			/**
			 * Shows the html links of the related objects recursivly
			 */ 
			$s = "";

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
			${"view_header_$i"} = \App\Http\Controllers\BaseController::translate("Assigned").' '.\App\Http\Controllers\BaseController::translate($view);
		?>

		@section("content_$i")
			@include('Generic.relation', [$relation, $view, $key])
		@stop
	
		@include ('bootstrap.panel', array ('content' => "content_$i", 'view_header' => ${"view_header_$i"}, 'md' => 3))

	@endforeach

	@if(isset($products))
		@include('billingbase::item', [$products])
	@endif

@stop

