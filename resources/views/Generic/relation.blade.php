{{--
Relation Blade is used inside a Panel Element to display relational class objects on the right window side

@param array $relation: the relation array to be displayed, contains one element of $relations element from edit.blade
@param string $key: SQL table key, required for adding new elements with reference to $key table
@param string $class $view: the class of the object to be used.
--}}

{{-- Error Message --}}
@if (Session::get('delete_message') && Session::get('delete_message')['class'] == $class)
	<div id='delete_msg' class="note note-{{ Session::get('delete_message')['color'] }} fade in m-b-15">
		<strong><h5>{{ Session::get('delete_message')['message'] }}	</h5></strong>
	</div>
@endif

@DivOpen(12)
	<div class="row">
		{{-- Create Button: (With hidden add fields if required) --}}
		@if (!isset($options['hide_create_button']))
			{{-- must send it's correlating parent via GET, see note in BaseViewController::compute_headline() --}}
			{{ Form::open(array('method' => 'POST')) }}
			{{ Form::hidden($key, $view_var->id) }}

			{{-- Add a hidden form field if create tag is set in $form_fields --}}
			@foreach($form_fields as $field)
				@if (array_key_exists('create', $field))
					{{ Form::hidden($field["name"], $view_var->{$field["name"]}) }}
				@endif
			@endforeach

			<div class="col align-self-start">
				<a 	class="btn btn-outline-primary m-b-10"
					data-toggle="tooltip"
					data-delay='{"show":"250"}'
					data-placement="top"
					href="{{'/'.BaseRoute::$admin_prefix.'/'.$view.'/create?'.$key.'='.$view_var->id}}"
					onclick="form.submit();"
					style="simple"
					title="{{ !isset($options['create_button_text']) ? \App\Http\Controllers\BaseViewController::translate_view('Create '.$view, 'Button') : trans($options['create_button_text']) }}">
						<i class="fa fa-plus fa-2x" aria-hidden="true"></i>
				</a>
			</div>

			{{ Form::close() }}
		@endif

		{{-- Delete Button --}}
		@if (!isset($options['hide_delete_button']) && isset($relation[0]))
			<div class="col align-self-end">
				<button class="btn btn-outline-danger m-b-10 float-right"
						data-toggle="tooltip"
						data-delay='{"show":"250"}'
						data-placement="top"
						form="{{$class}}"
						style="simple"
						title="{{ !isset($options['delete_button_text']) ? \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button') : trans($options['delete_button_text']) }}">
							<i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>
				</button>
			</div>
		@endif
	</div>
@DivClose()

{{-- The Relation Table --}}
@DivOpen(12)
	@if (isset($options['many_to_many']))
		{{ Form::open(array('route' => array($route_name.'.detach', $view_var->id, $options['many_to_many']), 'method' => 'post', 'id' => $class)) }}
	@else
		{{ Form::open(array('route' => array($view.'.destroy', 0), 'method' => 'delete', 'id' => $class)) }}
	@endif

	<table class="table">
		@foreach ($relation as $rel_elem)
			<tr class="{{isset ($rel_elem->view_index_label()['bsclass']) ? $rel_elem->view_index_label()['bsclass'] : ''}}">
				<td> {{ Form::checkbox('ids['.$rel_elem->id.']', 1, null, null, ['style' => 'simple']) }} </td>
				<td> {{ $rel_elem->view_icon()}} {{ HTML::linkRoute($view.'.'.$method, is_array($rel_elem->view_index_label()) ? $rel_elem->view_index_label()['header'] : $rel_elem->view_index_label(), $rel_elem->id) }} </td>
			</tr>
		@endforeach
	</table>

	{{ Form::close() }}
@DivClose()
