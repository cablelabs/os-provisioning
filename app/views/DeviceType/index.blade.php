@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('DeviceType.index', 'SNMP MIB') }}
	
@stop

@section('content_left')
	
	<h2>SNMP MIB</h2>

	{{ Form::open(array('route' => 'DeviceType.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}


	{{ Form::open(array('route' => array('DeviceType.destroy', 0), 'method' => 'delete')) }}

		@foreach ($devicetypes as $devicetype)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$devicetype->id.']') }}
						{{ HTML::linkRoute('DeviceType.edit', $devicetype->name, $devicetype->id) }}
					</td>
				</tr>
				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop
