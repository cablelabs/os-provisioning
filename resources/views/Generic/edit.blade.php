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

	@foreach($relations as $view => $relation)

		<?php if (!isset($i)) $i = 0; else $i++; ?>

		<!-- The section content for the new Panel -->
		@section("content_$i")
			@if (is_object($relation))
				<!-- old API: directly load relation view. NOTE: old API new class var was view -->
				@include('Generic.relation', [$relation, 'class' => $view, 'key' => strtolower($view_var->table).'_id'])
			@elseif (is_array($relation))
				<!-- new API: parse data -->
				@if (isset($relation['html']))
					{{$relation['html']}}
				@endif
				@if (isset($relation['view']))
					@include ($relation['view'])
				@endif
				@if (isset($relation['class']) && isset($relation['relation']))
					@include('Generic.relation', ['relation' => $relation['relation'], 'class' => $relation['class'], 'key' => strtolower($view_var->table).'_id'])
				@endif
			@else
				{{$relation}}
			@endif
		@stop


		<!-- The Bootstap Panel to include -->
		@include ('bootstrap.panel', array ('content' => "content_$i",
											'view_header' => \App\Http\Controllers\BaseViewController::translate("Assigned").' '.\App\Http\Controllers\BaseViewController::translate($view),
											'md' => 3))

	@endforeach

@stop

