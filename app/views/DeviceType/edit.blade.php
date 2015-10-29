@extends('Generic.edit')
@section('content_right')
	
	<h2>Devices</h2>

	@foreach ($view_var->devices as $device)

		<li>{{ HTML::linkRoute('Device.edit', $device->get_view_link_title(), $device->id) }}</li>
	
	@endforeach



	<h2>SNMP MIB</h2>

	@foreach ($view_var->snmpmibs as $snmpmib)

		<li>{{ HTML::linkRoute('SnmpMib.edit', $snmpmib->get_view_link_title(), $snmpmib->id) }}</li>
	
	@endforeach
@stop