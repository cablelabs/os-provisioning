
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

{{ Form::openDivClass(12) }}

	{{ Form::open(array('route' => array($view.'.destroy', 0), 'method' => 'delete')) }}
	<table>
		@foreach ($relations as $relation)
			<tr>
				<td>
					{{ Form::checkbox('ids['.$relation->id.']') }}
				</td>
				<td>
					{{ HTML::linkRoute($view.'.edit', $relation->get_view_link_title(), $relation->id) }}
				</td>
			</tr>
		@endforeach
	</table>

	<br>

	{{ Form::submit('Delete', ['style' => 'simple']) }}
	{{ Form::close() }}

{{ Form::closeDivClass() }}
