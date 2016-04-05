@extends ('Layout.split84')

@section('content_top')

	<h4>Global Search</h4>

@stop

@section('content_left')


	@DivOpen(12)
		<?php
			// searchscope for following form is the current model
			$next_scope = 'all';
		?>
		@DivOpen(6)
			{{ Form::model(null, array('route'=>'Base.fulltextSearch', 'method'=>'GET')) }}
				@include('Generic.searchform', ['button_text' => 'Search'])
			{{ Form::close() }}
		@DivClose()
	@DivClose()


	@DivOpen(12)
		@if (isset($query))
			<h4>Global Search: Matches for <tt>'{{ $query }}'</tt></h4>
		@endif

		<table>
			@foreach ($view_var as $object)
				<tr>
					<td>
						{{ Form::checkbox('ids['.$object->id.']') }}
					</td>
					<td>

						<?php
							// TODO: move away from view!!
							$cur_model_complete = get_class($object);
							$cur_model_parts = explode('\\', $cur_model_complete);
							$cur_model = array_pop($cur_model_parts);
						?>

					{{ HTML::linkRoute($cur_model.'.edit', $object->get_view_link_title(), $object->id) }}

					</td>
				</tr>
			@endforeach
		</table>
	@DivClose()

@stop
