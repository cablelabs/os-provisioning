{{---

Relation Blade is used inside a Panel Element to display relational class objects on the right window side

@param $relation: the relation array to be displayed, contains one element of $relations element from edit.blade
@param $key: SQL table key, required for adding new elements with reference to $key table
@param $class: the class of the object to be used. this will be translated to route name
               so take care that class = route and vice versa

--}}

<?php
	$route = $class; // for better reading / understanding
?>



<!-- Create Button: (With hidden add fields if required) -->
@if (!isset($options['hide_create_button']))
	@DivOpen(12)

		<!-- Form Open: must send it's correlating parent via GET, see note in BaseViewController::compute_headline() -->
		{{ Form::open(array('url' => CoreRoute::$admin_prefix.'/'.$route.'/create?'.$key.'='.$view_var->id, 'method' => 'POST')) }}
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
				<tr class="{{isset ($rel_elem->view_index_label()['bsclass']) ? $rel_elem->view_index_label()['bsclass'] : ''}}">
					<td> {{ Form::checkbox('ids['.$rel_elem->id.']', 1, null, null, ['style' => 'simple']) }} </td>
					<td> {{ HTML::linkRoute($route.'.edit', is_array($rel_elem->view_index_label()) ? $rel_elem->view_index_label()['header'] : $rel_elem->view_index_label(), $rel_elem->id) }} </td>
				</tr>
			@endforeach
		</table>


		<!-- Delete Button -->
		@if (!isset($options['hide_delete_button']) && isset($relation[0]))
			{{ Form::submit('Delete', ['!class' => 'btn btn-danger', 'style' => 'simple']) }}
		@endif

	{{ Form::close() }}

@DivClose()