@extends ('layouts.default')

@section('content')

	{{ Form::model($modem, array('route' => array('modem.update', $modem->id), 'method' => 'put')) }}

		<a href=edit>Modem-{{ $modem->hostname }}</a>

		<h2>Edit Modem</h2>
		<table>
		<tr>
			<td>{{ Form::label('hostname', 'Hostname') }}</td>
			<td>{{ Form::text ('hostname') }}</td>
			<td>{{ $errors->first('hostname') }}</td>
		</tr>

		<tr>
			<td>{{ Form::label('mac', 'MAC address') }}</td>
			<td>{{ Form::text ('mac') }}</td>
		</tr>

		<tr>
			<td>{{ Form::label('network_access', 'Network Access') }}</td>
			<td>{{ Form::checkbox('network_access', 1) }}</td>
		</tr>

		<tr>
			<td>{{ Form::label('serial_num', 'Serial Number') }}</td>
			<td>{{ Form::text('serial_num') }}</td>
		</tr>

		<tr>
			<td>{{ Form::label('inventar_num', 'Inventar Number') }}</td>
			<td>{{ Form::text('inventar_num') }}</td>
		</tr>

		<tr>
			<td>{{ Form::label('description', 'Description') }}</td>
			<td>{{ Form::textarea('description') }}</td>
		</tr>
		</table>

	{{ Form::submit('Save') }}
	{{ Form::close() }}
	
@stop