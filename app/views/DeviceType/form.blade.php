
<table>

	<tr>
		<td>{{ Form::label('name', 'Device Name') }}</td>
		<td>{{ Form::text ('name') }}</td>
		<td>{{ $errors->first('name') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('vendor', 'Vendor Name') }}</td>
		<td>{{ Form::text ('vendor') }}</td>
		<td>{{ $errors->first('vendor') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('version', 'Version') }}</td>
		<td>{{ Form::text ('version') }}</td>
		<td>{{ $errors->first('version') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('parent_id', 'Parent Device') }}</td>
		<td>{{ Form::text ('parent_id') }}</td>
		<td>{{ $errors->first('parent_id') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('description', 'Description') }}</td>
		<td>{{ Form::textarea ('description') }}</td>
		<td>{{ $errors->first('description') }}</td>
	</tr>

</table>
