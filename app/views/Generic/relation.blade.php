
{{ Form::openDivClass(12) }}

	{{ Form::open(array('route' => $view.'.create', 'method' => 'GET')) }}
	{{ Form::hidden($key, $view_var->id) }}
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
