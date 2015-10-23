@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('Device.index', 'Device') }} / {{ HTML::linkRoute('Device.edit', 'device-'.$device->name, array($device->id)) }}
	
@stop

@section('content_left')

	<h2>Edit SNMP MIB</h2>
	
	{{ Form::model($device, array('route' => array('Device.update', $device->id), 'method' => 'put')) }}

		@include('Device.form', $device)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')

@stop
