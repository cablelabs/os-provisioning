{{--

@param $query: the string query to search for
@param $scope: scope means context to search in (all | model name, like contract)

--}}

@extends ('Layout.split84')

@section('content_top')

	<h4>Global Search</h4>

@stop

@section('content_left')


	@DivOpen(12)
		@if (isset($query))
			<h4>Global Search: Matches for <tt>'{{ $query }}'</tt></h4>
		@endif

		<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>Type</th>
					<th>Entry</th>
					<th>Description</th>
				</tr>
			</thead>

			@foreach ($view_var as $object)
				<?php
					// TODO: move away from view!!
					$cur_model_parts = explode('\\', get_class($object));
					$cur_model = array_pop($cur_model_parts);

					if (is_array($object->view_index_label()))
					{
						$link = \HTML::linkRoute($cur_model.'.edit', $object->view_index_label()['header'], $object->id);
						$descr = implode (', ', $object->view_index_label()['index']);
					}
					else
					{
						$link = \HTML::linkRoute($cur_model.'.edit', $object->view_index_label(), $object->id);
						$descr = $object->view_index_label();
					}
				?>

				<tr class={{\App\Http\Controllers\BaseViewController::prep_index_entries_color($object)}}>
					<td>{{Form::checkbox('ids['.$object->id.']', 1, null, null, ['style' => 'simple'])}}</td>
					<td>{{$cur_model}}</td>
					<td>{{$link}}</td>
					<td>{{$descr}}</td>
				</tr>
			@endforeach
		</table>
	@DivClose()

@stop
