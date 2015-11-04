
<ul>
	@foreach ($relations as $relation)
		<li>
			{{ HTML::linkRoute($view.'.edit', $relation->get_view_link_title(), $relation->id) }}
		</li>
	@endforeach
</ul>

{{ Form::open(array('route' => $view.'.create', 'method' => 'GET')) }}
{{ Form::hidden($key, $view_var->id) }}
{{ Form::submit('Create '.$view) }}
{{ Form::close() }}

