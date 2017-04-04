{{--

@param $headline: the link header description in HTML

@param $view_var: the object we are editing
@param $form_update: the update route which should be called when clicking save
@param $form_path: the form view to be displayed inside this blade (mostly Generic.edit)
@param $panel_right: the page hyperlinks returned from prepare_tabs() or prep_right_panels()
@param $relations: the relations array() returned by prep_right_panels() in BaseViewController

--}}
@extends ('Layout.split')

@section('content_top')

	{{ $headline }}


@stop


@section('content_left')

	{{ Form::model($view_var, array('route' => array($form_update, $view_var->id), 'method' => 'put', 'files' => true)) }}

		@include($form_path, $view_var)

	{{ Form::close() }}

@stop


<?php $api = App\Http\Controllers\BaseViewController::get_view_has_many_api_version($relations) ?>

<?php //d($edit_left_md_size, $edit_right_md_size); ?>

@section('content_right')

	@foreach($relations as $view => $relation)

		<?php if (!isset($i)) $i = 0; else $i++; ?>

		<!-- The section content for the new Panel -->
		@section("content_$i")

			<!-- old API: directly load relation view. NOTE: old API new class var is $view -->
			@if ($api == 1)
				@include('Generic.relation', [$relation, 'class' => $view, 'key' => strtolower($view_var->table).'_id'])
			@endif

			<!-- new API: parse data -->
			@if ($api == 2)
				@if (is_array($relation))

					<!-- include pure HTML -->
					@if (isset($relation['html']))
						{{$relation['html']}}
					@endif

					<!-- include a view -->
					@if (isset($relation['view']))
						@if (is_string($relation['view']))
							@include ($relation['view'])
						@endif
						@if (is_array($relation['view']))
							@include ($relation['view']['view'], isset($relation['view']['vars']) ? $relation['view']['vars'] : [])
						@endif
					@endif

					<!-- include a relational class/object/table, like Contract->Modem -->
					@if (isset($relation['class']) && isset($relation['relation']))
						@include('Generic.relation', ['relation' => $relation['relation'],
													  'class' => $relation['class'],
													  'key' => strtolower($view_var->table).'_id',
													  'options' => isset($relation['options']) ? ($relation['options']) : null])
					@endif

				@endif
			@endif

		@stop


		<!-- The Bootstap Panel to include -->
		@include ('bootstrap.panel', array ('content' => "content_$i",
											'view_header' => \App\Http\Controllers\BaseViewController::translate_view('Assigned', 'Header').' '.\App\Http\Controllers\BaseViewController::translate_view($view, 'Header' , 2),
											'md' => isset($edit_right_md_size) ? $edit_right_md_size : 3))


	@endforeach


@stop
