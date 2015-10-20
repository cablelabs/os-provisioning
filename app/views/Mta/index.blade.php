@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('mta.index', 'MTAs') }}

@stop

@section('content_left')

	<h2>MTAs</h2>

	{{ Form::open(array('route' => 'mta.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}

	{{ Form::open(array('route' => array('mta.destroy', 0), 'method' => 'delete')) }}

		@foreach ($mtas as $mta)

				<table>
				<tr>
					<td>
						{{ Form::checkbox('ids['.$mta->id.']') }}
						{{ HTML::linkRoute('mta.edit', $mta->hostname, $mta->id) }}
					</td>
				</tr>
				</table>

		@endforeach

	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop

