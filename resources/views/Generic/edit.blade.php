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
		// TODO: move to Controller context

		// API: check which API (array) is used
		if (\Acme\php\ArrayHelper::array_depth($view_var->view_has_many()) < 2)
		{
			// old API
			$relations = $view_var->view_has_many();
		}
		else
		{
			// new API
			// TODO: validate Input blade
			$blade = 0;
			if(Input::get('blade') != '')
				$blade = Input::get('blade');

			// get actual blade to $b
			$a = $view_var->view_has_many();
			$b = current($a);
			for ($i = 0; $i < $blade; $i++)
				$b = next($a);

			$relations = $b;
		}


		$i = 0;
	?>

	@foreach($relations as $view => $relation)

		<?php
			$i++;

			$model = new $model_name;
			$key   = strtolower($model->table).'_id';
			${"view_header_$i"} = \App\Http\Controllers\BaseViewController::translate("Assigned").' '.\App\Http\Controllers\BaseViewController::translate($view);
		?>

		@section("content_$i")
			@if (is_object($relation))
				<!-- old API: directly load relation view -->
				@include('Generic.relation', [$relation, $view, $key])
			@elseif (is_array($relation))
				<!-- new API: parse data -->
				@if (isset($relation['html']))
					{{$relation['html'];}}
				@endif
				@if (isset($relation['view']))
					@include ($relation['view'])
				@endif
			@else
				{{$relation}}
			@endif
		@stop


		@include ('bootstrap.panel', array ('content' => "content_$i", 'view_header' => ${"view_header_$i"}, 'md' => 3))

	@endforeach

@stop

