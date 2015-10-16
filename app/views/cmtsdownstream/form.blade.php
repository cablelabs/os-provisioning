<table>

<tr>
	<td>{{ Form::label('alias', 'Alias') }}</td>
	<td>{{ Form::text ('alias') }}</td> 
	<td>{{ $errors->first('alias') }}</td>
</tr>
<tr>
	<td>{{ Form::label('index', 'Index') }}</td>
	<td>{{ Form::text ('index') }}</td> 
	<td>{{ $errors->first('index') }}</td>
</tr>
<tr>
	<td>{{ Form::label('description', 'Description') }}</td>
	<td>{{ Form::text ('description') }}</td> 
	<td>{{ $errors->first('description') }}</td>
</tr>
<tr>
	<td>{{ Form::label('frequency', 'Frequency') }}</td>
	<td>{{ Form::text ('frequency') }}</td> 
	<td>{{ $errors->first('frequency') }}</td>
</tr>
		<tr>
			<td>{{ Form::label('modulation', 'Modulation') }}</td>
			<td>{{ Form::select ('modulation', array('qam64' => 'qam64', 'qam256' => 'qam256')) }}</td>
			<td>{{ $errors->first('modulation') }}</td>
		</tr>
<tr>
	<td>{{ Form::label('power', 'Power') }}</td>
	<td>{{ Form::text ('power') }}</td> 
	<td>{{ $errors->first('power') }}</td>
</tr>
<tr>
	<td>{{ Form::label('cmts_gws_id', 'CMTS') }}</td>
	<td>{{ Form::text ('cmts_gws_id') }}</td> 
	<td>{{ $errors->first('cmts_gws_id') }}</td>
</tr>

</table>