{{ Form::open(['route' => ['DeviceType.assign_oids', $view_var->id], 'method' => 'get']) }}

	{{ Form::label('mibfile', 'Choose MIB-File') }}
	{{ Form::select('mibfile_id', $list) }}
	{{ Form::submit('Assign OIDs') }}

{{ Form::close() }}