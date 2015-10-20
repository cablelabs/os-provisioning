@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('endpoint.index', 'Endpoints') }}

@stop

@section('content_left')

	<h2>Endpoints</h2>

	{{ Form::open(array('route' => 'endpoint.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}

	{{ Form::open(array('route' => array('endpoint.destroy', 0), 'method' => 'delete')) }}

		@foreach ($endpoints as $endpoint)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$endpoint->id.']') }}
						{{ HTML::linkRoute('endpoint.edit', $endpoint->hostname, $endpoint->id) }}
					</td>
				</tr>
				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop