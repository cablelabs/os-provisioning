
@DivOpen(12)

	{{ Form::open(array('route' => $view.'.create', 'method' => 'GET')) }}
	{{ Form::hidden($key, $view_var->id) }}

	{{-- Add a hidden form field if create tag is set in $form_fields --}}
	@foreach($form_fields as $field)
		<?php
			if (array_key_exists('create', $field))
				echo Form::hidden($field["name"], $view_var->{$field["name"]});
		?>
	@endforeach

	{{ Form::submit(\App\Http\Controllers\BaseViewController::translate('Create ').\App\Http\Controllers\BaseViewController::translate($view), ['style' => 'simple']) }}
	{{ Form::close() }}

@DivClose()

<br>

@DivOpen(12)

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

@DivClose()
