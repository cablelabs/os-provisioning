@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('SnmpMib.index', 'SNMP MIB') }}
	
@stop

@section('content_left')
	
	<h2>SNMP MIB</h2>

	{{ Form::open(array('route' => 'SnmpMib.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}


	{{ Form::open(array('route' => array('SnmpMib.destroy', 0), 'method' => 'delete')) }}

		@foreach ($snmpmibs as $snmpmib)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$snmpmib->id.']') }}
						{{ HTML::linkRoute('SnmpMib.edit', 'Device '.$snmpmib->device_id.' - '.$snmpmib->field, $snmpmib->id) }}
					</td>
				</tr>
				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop
