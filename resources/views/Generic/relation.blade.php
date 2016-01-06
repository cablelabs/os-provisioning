<?php
	// check if given relation is a Collection => in this case we assume a x-to-many
	// other checks can easily be OR connected
	// else it seems to be a 1:1
	$is_x_to_many = is_a($relation, 'Illuminate\Database\Eloquent\Collection');

	// helper flag to indicate no related data
	$rel_is_null = is_null($relation);

	// put in array => later we can use this in
	if (!$is_x_to_many) {
		$relation = array($relation);
	}
?>

{{-- Create button --}}
{{-- do not show if a data in a 1:1 is given --}}
@if ($is_x_to_many || $rel_is_null)
	{{ Form::openDivClass(12) }}

		{{ Form::open(array('route' => $view.'.create', 'method' => 'GET')) }}
		{{ Form::hidden($key, $view_var->id) }}

		{{-- Add a hidden form field if create tag is set in $form_fields --}}
		@foreach($form_fields as $field)
			<?php
				if (array_key_exists('create', $field))
					echo Form::hidden($field["name"], $view_var->{$field["name"]});
			?>
		@endforeach

		{{ Form::submit('Create '.$view, ['style' => 'simple']) }}
		{{ Form::close() }}

	{{ Form::closeDivClass() }}
	<br>
@endif

{{-- List of related data --}}
@if (!$rel_is_null)
	{{ Form::openDivClass(12) }}

		{{ Form::open(array('route' => array($view.'.destroy', 0), 'method' => 'delete')) }}
		<table>
			@foreach ($relation as $rel_elem)
				<tr>
					<td>
						{{ Form::checkbox('ids['.$rel_elem->id.']') }}
					</td>
					<td>
						{{ HTML::linkRoute($view.'.edit', $rel_elem->get_view_link_title(), $rel_elem->id) }}
					</td>
				</tr>
			@endforeach
		</table>

		<br>

		{{ Form::submit('Delete', ['style' => 'simple']) }}
		{{ Form::close() }}

	{{ Form::closeDivClass() }}
@endif
