@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('SnmpMib.index', 'SNMP MIB') }}

@stop

@section('content_left')

	<h2>Create SNMP MIB</h2>	

	{{ Form::open(array('route' => array('SnmpMib.store', 0), 'method' => 'POST')) }}

		@include('SnmpMib.form', array ('snmpmib' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop