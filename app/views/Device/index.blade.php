@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('Device.index', 'SNMP MIB') }}
	
@stop

@section('content_left')
	
	<h2>SNMP MIB</h2>

	{{ Form::open(array('route' => 'Device.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}


	{{ Form::open(array('route' => array('Device.destroy', 0), 'method' => 'delete')) }}

		@foreach ($devices as $device)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$device->id.']') }}
						{{ HTML::linkRoute('Device.edit', $device->name, $device->id) }}
					</td>
				</tr>
				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop
