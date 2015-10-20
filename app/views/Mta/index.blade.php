@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('Mta.index', 'MTAs') }}

@stop

@section('content_left')

	<h2>MTAs</h2>

	{{ Form::open(array('route' => 'Mta.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}

	{{ Form::open(array('route' => array('Mta.destroy', 0), 'method' => 'delete')) }}

		@foreach ($mtas as $mta)

				<table>
				<tr>
					<td>
						{{ Form::checkbox('ids['.$mta->id.']') }}
						{{ HTML::linkRoute('Mta.edit', $mta->hostname, $mta->id) }}
					</td>
				</tr>
				</table>

		@endforeach

	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop

