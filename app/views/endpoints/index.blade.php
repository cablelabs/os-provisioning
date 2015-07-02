@extends ('layouts.default')

@section('content')

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