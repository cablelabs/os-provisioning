
		<table>
		<tr>
			<td>{{ Form::label('name', 'Name') }}</td>
			<td>{{ Form::text ('name') }}</td>
			<td>{{ $errors->first('name') }}</td>
		</tr>

		<tr>
			<td>{{ Form::label('ds_rate_max', 'DS Rate [MBit/s]') }}</td>
			<td>{{ Form::text ('ds_rate_max') }}</td>
		</tr>
		<tr>
			<td>{{ Form::label('us_rate_max', 'US Rate [MBit/s]') }}</td>
			<td>{{ Form::text ('us_rate_max') }}</td>
		</tr>
		</table>
