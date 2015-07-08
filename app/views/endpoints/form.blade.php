
		<table>
		<tr>
			 {{ isset($_GET['modem_id']) ? Form::hidden ('modem_id', $_GET['modem_id']) : '' }}

			<td>{{ Form::label('hostname', 'Hostname') }}</td>
			<td>{{ Form::text ('hostname') }}</td>
			<td>{{ $errors->first('hostname') }}</td>
		</tr>

		<tr>
			<td>{{ Form::label('mac', 'MAC address') }}</td>
			<td>{{ Form::text ('mac') }}</td>
		</tr>

		<tr>
			<td>{{ Form::label('public', 'Public IP') }}</td>
			<td>{{ Form::checkbox('public', 1) }}</td>
		</tr>

		<tr>
			<td>{{ Form::label('description', 'Description') }}</td>
			<td>{{ Form::textarea('description') }}</td>
		</tr>
		</table>