@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('endpoint.index', 'Endpoints') }}

@stop

@section('content_left')

	<h2>Endpoints</h2>

	{{ Form::open(array('route' => array('endpoint.destroy', 0), 'method' => 'delete')) }}

		@foreach ($endpoints as $endpoint)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$endpoint->id.']') }}
						<a href=endpoint/{{$endpoint->id}}/edit>{{$endpoint->hostname}}</a> 
					</td>
				</tr>
				</table>
			
		@endforeach

	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop