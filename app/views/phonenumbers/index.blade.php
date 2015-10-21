@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('phonenumber.index', 'Phonenumbers') }}

@stop

@section('content_left')

	<h2>Phonenumbers</h2>

	{{ Form::open(array('route' => 'phonenumber.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}

	{{ Form::open(array('route' => array('phonenumber.destroy', 0), 'method' => 'delete')) }}

		@foreach ($phonenumbers as $phonenumber)

				<table>
				<tr>
					<td>
						{{ Form::checkbox('ids['.$phonenumber->id.']') }}
						{{ HTML::linkRoute('phonenumber.edit', "(".$phonenumber->country_code.") ".$phonenumber->prefix_number."/".$phonenumber->number, $phonenumber->id) }}
					</td>
				</tr>
				</table>

		@endforeach

	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop

