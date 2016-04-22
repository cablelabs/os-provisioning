
<?php
	$route = $class;
?>


<!-- Create Button: (With hidden add fields if required) -->
@if (!isset($options['hide_create_button']))
	@DivOpen(12)

		{{ Form::open(array('route' => $route.'.create', 'method' => 'GET')) }}
		{{ Form::hidden($key, $view_var->id) }}

			{{-- Add a hidden form field if create tag is set in $form_fields --}}
			@foreach($form_fields as $field)
				@if (array_key_exists('create', $field))
					{{ Form::hidden($field["name"], $view_var->{$field["name"]}) }}
				@endif
			@endforeach

		{{ Form::submit(\App\Http\Controllers\BaseViewController::translate('Create ').\App\Http\Controllers\BaseViewController::translate($view), ['style' => 'simple']) }}
		{{ Form::close() }}

	@DivClose()
@endif


<!-- The Relation Table and Delete Button -->
@DivOpen(12)

	{{ Form::open(array('route' => array($route.'.destroy', 0), 'method' => 'delete')) }}

		<br>
		<table class="table">
			@foreach ($relation as $rel_elem)
				<tr class="{{isset ($rel_elem->get_view_link_title()['bsclass']) ? $rel_elem->get_view_link_title()['bsclass'] : ''}}">
					<td> {{ Form::checkbox('ids['.$rel_elem->id.']', 1, null, null, ['style' => 'simple']) }} </td>
					<td> {{ HTML::linkRoute($route.'.edit', is_array($rel_elem->get_view_link_title()) ? $rel_elem->get_view_link_title()['header'] : $rel_elem->get_view_link_title(), $rel_elem->id) }} </td>
				</tr>
			@endforeach
		</table>


		<!-- Delete Button -->
		@if (!isset($options['hide_delete_button']) && isset($relation[0]))
			{{ Form::submit('Delete', ['style' => 'simple']) }}
		@endif

	{{ Form::close() }}

@DivClose()
