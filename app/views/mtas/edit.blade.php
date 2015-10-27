@extends ('layouts.split')

@include ('mtas.header')

@section('content_top')

		{{ HTML::linkRoute('mta.index', 'MTAs') }}

@stop

@section('content_left')

	<h2>Edit MTA</h2>

	{{ Form::model($mta, array('route' => array('mta.update', $mta->id), 'method' => 'put')) }}

		@include('mtas.form', $mta)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

	<hr>

	Assigned phonenumbers:
	<ul>
	@if (count($phonenumbers) === 0)
		<li>None</li>
	@else

		@foreach ($phonenumbers as $phonenumber)
			<li>
				{{ HTML::linkRoute('phonenumber.edit', "(".$phonenumber->country_code.") ".$phonenumber->prefix_number."/".$phonenumber->number, $phonenumber->id) }}
				(Port {{ $phonenumber->port }})
			</li>
		@endforeach
	@endif
	</ul>

	{{ Form::open(array('route' => 'phonenumber.create', 'method' => 'GET')) }}
	{{ Form::hidden('mta_id', $mta->id) }}
	{{ Form::submit('Create phonenumber') }}
	{{ Form::close() }}

@stop

