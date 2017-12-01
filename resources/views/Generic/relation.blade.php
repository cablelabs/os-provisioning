{{--

Relation Blade is used inside a Panel Element to display relational class objects on the right window side

@param $relation: the relation array to be displayed, contains one element of $relations element from edit.blade
@param $key: SQL table key, required for adding new elements with reference to $key table
@param $class: the class of the object to be used. this will be translated to route name
			   so take care that class = route and vice versa

--}}

<?php
	$route = $class; // for better reading / understanding
	$blade_type = 'relations';
?>


{{-- Error Message --}}
@if (Session::get('delete_message') && Session::get('delete_message')['class'] == $class)
	@DivOpen(12)
			<h5 style='color:{{ Session::get('delete_message')['color'] }}' id='delete_msg'>{{ Session::get('delete_message')['message'] }}</h5>
	@DivClose()
@endif


@include('Generic.above_infos')

{{-- Create Button: (With hidden add fields if required) --}}
@DivOpen(12)
	@if (!isset($options['hide_create_button']))
		<div class="row">
		{{-- Form Open: must send it's correlating parent via GET, see note in BaseViewController::compute_headline() --}}
		{{ Form::open(array('url' => BaseRoute::$admin_prefix.'/'.$route.'/create?'.$key.'='.$view_var->id, 'method' => 'POST')) }}
		{{ Form::hidden($key, $view_var->id) }}

			{{-- Add a hidden form field if create tag is set in $form_fields --}}
			@foreach($form_fields as $field)
				@if (array_key_exists('create', $field))
					{{ Form::hidden($field["name"], $view_var->{$field["name"]}) }}
				@endif
			@endforeach

		<?php
			// check if default create button text shall be overwritten
			if (!isset($options['create_button_text'])) {
				$create_button_text = \App\Http\Controllers\BaseViewController::translate_view('Create '.$view, 'Button');
			}
			else {
				$create_button_text = trans($options['create_button_text']);
			}
		?>
		<div class="col align-self-start">
				<button class="btn btn-outline-primary float-right m-b-10" style="simple" data-toggle="tooltip" data-delay='{"show":"250"}' data-placement="top"
				title="{{ $create_button_text }}">
					<i class="fa fa-plus fa-2x" aria-hidden="true"></i>
				</button>
		</div>
		{{ Form::close() }}
	@endif
	{{-- Delete Button --}}
	@if (!isset($options['hide_delete_button']) && isset($relation[0]))

		<?php
			// check if default delete button text shall be overwritten
			if (!isset($options['delete_button_text'])) {
				$delete_button_text = \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button');
			}
			else {
				$delete_button_text = trans($options['delete_button_text']);
			}
		?>
		    <div class="col align-self-end">
				<button class="btn btn-outline-danger m-b-10 float-right" style="simple" data-toggle="tooltip" data-delay='{"show":"250"}' data-placement="top"
				title="{{ $delete_button_text }}" form="RelationForm">
						<i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>
				</button>
			</div>
	@endif
	</div>
@DivClose()





{{-- The Relation Table and Delete Button --}}
@DivOpen(12)

	@if (isset($options['many_to_many']))
		{{ Form::open(array('route' => array($route_name.'.detach', $view_var->id, $options['many_to_many']), 'method' => 'post', 'id' => 'RelationForm')) }}
	@else
		{{ Form::open(array('route' => array($route.'.destroy', 0), 'method' => 'delete', 'id' => 'RelationForm')) }}
	@endif

		<br>
		<table class="table">
			@foreach ($relation as $rel_elem)
				<tr class="{{isset ($rel_elem->view_index_label()['bsclass']) ? $rel_elem->view_index_label()['bsclass'] : ''}}">
					<td> {{ Form::checkbox('ids['.$rel_elem->id.']', 1, null, null, ['style' => 'simple']) }} </td>
					<td> {{$rel_elem->view_icon()}}	{{ HTML::linkRoute($route.'.'.$method, is_array($rel_elem->view_index_label()) ? $rel_elem->view_index_label()['header'] : $rel_elem->view_index_label(), $rel_elem->id) }} </td>
				</tr>
			@endforeach
		</table>

	{{ Form::close() }}

@DivClose()
