@extends('Generic.edit')

@section('content_right')

	Assigned phonenumbers:
	<ul>
	@if (count($view_var) === 0)
		<li>None</li>
	@else

		@foreach ($view_var->phonenumbers as $phonenumber)
			<li>
				{{ HTML::linkRoute('Phonenumber.edit', $phonenumber->get_view_link_title(), $phonenumber->id) }}
				(Port {{ $phonenumber->port }})
			</li>
		@endforeach
	@endif
	</ul>

	{{ Form::open(array('route' => 'Phonenumber.create', 'method' => 'GET')) }}
	{{ Form::hidden('mta_id', $view_var->id) }}
	{{ Form::submit('Create phonenumber') }}
	{{ Form::close() }}

@stop

