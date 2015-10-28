<table>
	<tr>
		<td>{{ Form::label('country_code', 'Country code') }}</td>
		<td>{{ Form::select('country_code', $country_codes) }}</td>
	</tr>
	<tr>
		<td>{{ Form::label('prefix_number', 'Prefix number') }}</td>
		<td>{{ Form::text('prefix_number') }}</td>
		<td>{{ $errors->first('prefix_number') }}</td>
	</tr>
	<tr>
		<td>{{ Form::label('number', 'Number') }}</td>
		<td>{{ Form::text('number') }}</td>
		<td>{{ $errors->first('number') }}</td>
	</tr>
	<tr>
		<td>{{ Form::label('mta_id', 'MTA') }}</td>
		<td>{{ Form::select('mta_id', $mtas, $mta_id) }}</td>
	</tr>
	<tr>
		<td>{{ Form::label('port', 'Port') }}</td>
		<td>{{ Form::text('port') }}</td>
		<td>{{ $errors->first('port') }}</td>
	</tr>
	<tr>
		<td>{{ Form::label('username', 'Username') }}</td>
		<td>{{ Form::text('username') }}</td>
		<td>{{ $errors->first('username') }}</td>
	</tr>
	<tr>
		<td>{{ Form::label('password', 'Password') }}</td>
		<td>{{ Form::text('password') }}</td>
		<td>{{ $errors->first('password') }}</td>
	</tr>
	<tr>
		<td>{{ Form::label('active', 'Active?') }}</td>
		<td>{{ Form::select('active', [1=>'Yes', 0=>'No']) }}</td>
	</tr>
</table>
