@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('DeviceType.index', 'snmpmib') }} / {{ HTML::linkRoute('DeviceType.edit', 'devicetype-'.$devicetype->name, array($devicetype->id)) }}
	
@stop

@section('content_left')

	<h2>Edit SNMP MIB</h2>
	
	{{ Form::model($devicetype, array('route' => array('DeviceType.update', $devicetype->id), 'method' => 'put')) }}

		@include('DeviceType.form', $devicetype)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')

@stop
