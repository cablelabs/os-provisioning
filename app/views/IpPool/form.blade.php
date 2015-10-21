<table>
<tr>
	<td>{{ Form::label('cmts_id', 'CMTS Hostname') }}</td>
	<td>{{ Form::select('cmts_id', $hostnames) }}	
</tr>

<tr>
	<td>{{ Form::label('type', 'Type') }}</td>
	<td>{{ Form::select ('type', array( 'CM' => 'Cable Modem', 'CPEPriv' => 'CPE Private', 'CPEPub' => 'CPE Public', 'MTA' => 'MTA')) }}</td>
</tr>

<tr>
	<td>{{ Form::label('net', 'Net') }}</td>
	<td>{{ Form::text ('net') }}</td>
	<td>{{ $errors->first('net') }}</td>
</tr>

<tr>
	<td>{{ Form::label('netmask', 'Netmask') }}</td>
	<td>{{ Form::text ('netmask') }}</td>
	<td>{{ $errors->first('netmask') }}</td>
</tr>

<tr>
	<td>{{ Form::label('ip_pool_start', 'First IP') }}</td>
	<td>{{ Form::text ('ip_pool_start') }}</td>
	<td>{{ $errors->first('ip_pool_start') }}</td>
</tr>

<tr>
	<td>{{ Form::label('ip_pool_end', 'Last IP') }}</td>
	<td>{{ Form::text ('ip_pool_end') }}</td>
	<td>{{ $errors->first('ip_pool_end') }}</td>
</tr>

<tr>
	<td>{{ Form::label('router_ip', 'Router IP') }}</td>
	<td>{{ Form::text ('router_ip') }}</td>
	<td>{{ $errors->first('router_ip') }}</td>
</tr>

<tr>
	<td>{{ Form::label('broadcast_ip', 'Broadcast IP') }}</td>
	<td>{{ Form::text ('broadcast_ip') }}</td>
	<td>{{ $errors->first('broadcast_ip') }}</td>
</tr>

<tr>
	<td>{{ Form::label('dns1_ip', 'DNS1 IP') }}</td>
	<td>{{ Form::text ('dns1_ip') }}</td>
	<td>{{ $errors->first('dns1_ip') }}</td>
</tr>

<tr>
	<td>{{ Form::label('dns2_ip', 'DNS2 IP') }}</td>
	<td>{{ Form::text ('dns2_ip') }}</td>
	<td>{{ $errors->first('dns2_ip') }}</td>
</tr>

<tr>
	<td>{{ Form::label('dns3_ip', 'DNS3 IP') }}</td>
	<td>{{ Form::text ('dns3_ip') }}</td>
	<td>{{ $errors->first('dns3_ip') }}</td>
</tr>

<tr>
	<td>{{ Form::label('optional', 'Additional Options') }}</td>
	<td>{{ Form::textarea ('optional') }}</td>
</tr>
</table>

