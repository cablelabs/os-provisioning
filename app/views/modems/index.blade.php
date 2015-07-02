@extends ('layouts.default')

@section('content')

	{{ Form::open(array('route' => array('modem.destroy', 0), 'method' => 'delete')) }}

		@foreach ($modems as $modem)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$modem->id.']') }}
						<a href=modem/{{$modem->id}}/edit>{{$modem->hostname}}</a> 
					</td>
				</tr>
				</table>
			
		@endforeach

	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop