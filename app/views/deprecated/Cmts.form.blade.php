<?php
/*Note: 
	* "{{" equals "php echo" 
	* "{{{" properly escapes text
	* @ replaces <?php and ?> for one line 
	* Form::label('formularName', 'descriptionName')
*/
?>

<script>setTimeout("document.getElementById('success_msg').style.display='none';", 2500);</script>

<table>
<tr>
	<td>{{ Form::label('hostname', 'Hostname') }}</td>
	<td>{{ Form::text ('hostname') }}</td> 
	<td>{{ $errors->first('hostname') }}</td>
</tr>

<tr>
	<td>{{ Form::label('type', 'Type') }}</td>
	<td>{{ Form::text ('type') }}</td>
</tr>

<tr>
	<td>{{ Form::label('ip', 'IP') }}</td>
	<td>{{ Form::text ('ip') }} </td>
	<td>{{ $errors->first('ip') }}</td>

</tr>

<tr>
	<td>{{ Form::label('community_rw', 'SNMP Private Community String') }}</td>
	<td>{{ Form::text ('community_rw') }}</td>
</tr>

<tr>
	<td>{{ Form::label('community_ro', 'SNMP Public Community String') }}</td>
	<td>{{ Form::text ('community_ro') }}</td>
</tr>

<tr>
	<td>{{ Form::label('company', 'Company') }}</td>
	<td>{{ Form::text ('company') }}</td>
</tr>

<tr>
	<td>{{ Form::label('state', 'State') }}</td>
	<td>{{ Form::text ('state') }}</td>
</tr>

<tr>
	<td>{{ Form::label('monitoring', 'Monitoring') }}</td>
	<td>{{ Form::text ('monitoring') }}</td>
</tr>

<tr>
	<td><br></td>
</tr>
<tr>
	<td>{{ Form::submit('Save') }}</td>
	<td id='success_msg'>{{ Session::get('message') }}</td>
</tr>

</table>