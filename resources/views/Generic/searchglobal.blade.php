@extends ('Layout.split84')

@section('content_top')

	<h4>Global Search</h4>

@stop

@section('content_left')


	{{ Form::openDivClass(12) }}
		<?php
			// searchscope for following form is the current model
			$next_scope = 'all';
		?>
		{{ Form::openDivClass(6) }}
			{{ Form::model(null, array('route'=>'Base.fulltextSearch', 'method'=>'GET')) }}
				@include('Generic.searchform', ['button_text' => 'Search'])
			{{ Form::close() }}
		{{ Form::closeDivClass() }}
	{{ Form::closeDivClass() }}


	{{ Form::openDivClass(12) }}

		@if (isset($query))
			<h4>Global Search: Matches for <tt>'{{ $query }}'</tt></h4>
		@endif

		<table>
			<?php
			$last_model = "";
			?>

			@foreach ($view_var as $object)
					<?php
						// TODO: move away from view!!
						$cur_model_complete = get_class($object);
						$cur_model_parts = explode('\\', $cur_model_complete);
						$cur_model = array_pop($cur_model_parts);
					?>
						@if ($last_model != $cur_model)
							<tr><td colspan="2" style="border-top: solid 1px #aaaaaa">
								<h5>{{ $cur_model }}s:</h5>
							</td></tr>
						@endif
				<tr>
					<td>
						{{ Form::checkbox('ids['.$object->id.']') }}
					</td>
					<td>
					<?php
						$last_model = $cur_model;
					?>

					{{ HTML::linkRoute($cur_model.'.edit', $object->get_view_link_title(), $object->id) }}

					{{-- show current state if object is enviaorder => later on this can be used to filter orders --}}
					@if ($cur_model == "EnviaOrder")
						⇒ <i>{{ $object->orderstatus }}</i>
					@endif

					</td>
				</tr>
			@endforeach
		</table>

	{{ Form::closeDivClass() }}

@stop
