
		<table>
		<tr>
			<td>{{ Form::label('name', 'Name') }}</td>
			<td>{{ Form::text ('name') }}</td>
			<td>{{ $errors->first('name') }}</td>
		</tr>
		<tr>
			<td>{{ Form::label('type', 'Type') }}</td>
			<td>{{ Form::select ('type', array('generic' => 'generic', 'network' => 'network', 'vendor' => 'vendor', 'user' => 'user')) }}</td>
			<td>{{ $errors->first('type') }}</td>
		</tr>
		<tr>
			<td>{{ Form::label('device', 'Device') }}</td>
			<td>{{ Form::select('device', array('cm' => 'CM', 'mta' => 'MTA')) }}</td>
		</tr>
		<tr>
			<td>{{ Form::label('parent', 'Parent Configfile') }}</td>
			<td>{{ Form::select('parent', $parents) }}</td>
		</tr>
		<tr>
			<td>{{ Form::label('text', 'Config File Parameters') }}</td>
			<td>{{ Form::textarea ('text') }}</td>
			<td>{{ $errors->first('text') }}</td>
		</tr>

		</table>
