@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('Qos.index', 'quality') }}
	
@stop

@section('content_left')
	
	<h2>quality</h2>

	{{ Form::open(array('route' => 'Qos.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}


	{{ Form::open(array('route' => array('Qos.destroy', 0), 'method' => 'delete')) }}

		@foreach ($qoss as $quality)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$quality->id.']') }}
						{{ HTML::linkRoute('Qos.edit', $quality->name, $quality->id) }}
					</td>
				</tr>
				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop
