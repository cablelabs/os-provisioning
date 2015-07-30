
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
			<td>{{ Form::label('parent_id', 'Parent Configfile') }}</td>
			<td>{{ Form::select('parent_id', $parents) }}</td>
		</tr>
		<tr>
			<td>{{ Form::label('public', 'Public Use') }}</td>
			<td>{{ Form::select ('public', array('yes' => 'Yes', 'no' => 'No')) }}</td>
			<td>{{ $errors->first('public') }}</td>
		</tr>
		<tr>
			<td>{{ Form::label('text', 'Config File Parameters') }}</td>
			<td>{{ Form::textarea ('text', null, ['size' => '100x30']) }}</td>
			<td><font color="red">{{ $errors->first('text') }}</font></td>
		</tr>

		</table>
