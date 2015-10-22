
<table>

	<tr>
		<td>{{ Form::label('device_id', 'Device') }}</td>
		<td>{{ Form::text ('device_id') }}</td>
		<td>{{ $errors->first('device_id') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('field', 'Field') }}</td>
		<td>{{ Form::text ('field') }}</td>
		<td>{{ $errors->first('field') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('oid', 'OID') }}</td>
		<td>{{ Form::text ('oid') }}</td>
		<td>{{ $errors->first('oid') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('oid_table', 'OID Table Entry') }}</td>
		<td>{{ Form::select ('oid_table', array(0 => 'False', 1 => 'True')) }}</td>
		<td>{{ $errors->first('oid_table') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('type', 'Type') }}</td>
		<td>{{ Form::select ('type', array(
						              'i' => 'INTEGER',
						              'u' => 'UNSIGNED',
						              's' => 'STRING',
						              'x' => 'HEX STRING',
						              'd' => 'DECIMAL STRING',
						              'n' => 'NULLOBJ',
						              'o' => 'OBJID',
						              't' => 'TIMETICKS',
						              'a' => 'IPADDRESS',
						              'b' => 'BITS'
							)) }}</td>




		<td>{{ $errors->first('type') }}</td>
	</tr>
	
	<tr>
		<td>{{ Form::label('type_array', 'Type Array') }}</td>
		<td>{{ Form::text ('type_array') }}</td>
		<td>{{ $errors->first('type_array') }}</td>
	</tr>

	<tr><td></td></tr>

	<tr>
		<td>{{ Form::label('html_type', 'HTML Field Type') }}</td>
		<td>
			{{ Form::select ('html_type', array('input' => 'Input', 'select' => 'Select', 
												'groupbox' => 'Groupbox', 'textarea' => 'Textarea')) }}
		</td>
	</tr>

	<tr>
		<td>{{ Form::label('html_properties', 'HTML Properties') }}</td>
		<td>{{ Form::text ('html_properties') }}</td>
		<td>{{ $errors->first('html_properties') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('phpcode_pre', 'PHP Code Pre') }}</td>
		<td>{{ Form::text ('phpcode_pre') }}</td>
		<td>{{ $errors->first('phpcode_pre') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('phpcode_post', 'PHP Code Post') }}</td>
		<td>{{ Form::text ('phpcode_post') }}</td>
		<td>{{ $errors->first('phpcode_post') }}</td>
	</tr>


	<tr>
		<td>{{ Form::label('description', 'Description') }}</td>
		<td>{{ Form::textarea ('description') }}</td>
		<td>{{ $errors->first('description') }}</td>
	</tr>

</table>
