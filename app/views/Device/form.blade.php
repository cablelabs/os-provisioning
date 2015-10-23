
<table>

	<tr>
		<td>{{ Form::label('devicetype_id', 'Device Type') }}</td>
		<td>{{ Form::select ('devicetype_id') }}</td>
		<td>{{ $errors->first('devicetype_id') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('name', 'Name') }}</td>
		<td>{{ Form::text ('name') }}</td>
		<td>{{ $errors->first('name') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('ip', 'IP address') }}</td>
		<td>{{ Form::text ('ip') }}</td>
		<td>{{ $errors->first('ip') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('community_ro', 'SNMP community (RO)') }}</td>
		<td>{{ Form::text ('community_ro') }}</td>
		<td>{{ $errors->first('community_ro') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('community_rw', 'SNMP community (RW)') }}</td>
		<td>{{ Form::text ('community_rw') }}</td>
		<td>{{ $errors->first('community_rw') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('address1', 'Address Line 1') }}</td>
		<td>{{ Form::text ('address1') }}</td>
		<td>{{ $errors->first('address1') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('address2', 'Address Line 2') }}</td>
		<td>{{ Form::text ('address2') }}</td>
		<td>{{ $errors->first('address2') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('address3', 'Address Line 3') }}</td>
		<td>{{ Form::text ('address3') }}</td>
		<td>{{ $errors->first('address3') }}</td>
	</tr>

	<tr>
		<td>{{ Form::label('description', 'Description') }}</td>
		<td>{{ Form::textarea ('description') }}</td>
		<td>{{ $errors->first('description') }}</td>
	</tr>

{{ Form::label('success', 'success', ['id' => 'success']) }}


<script type="text/javascript">

	document.getElementById("success").innerHTML =  "Speierceuirhferfc";
	Thread.sleep(500);
	document.getElementById("success").innerHTML =  "";
    
</script>




</table>
