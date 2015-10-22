@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('DeviceType.index', 'SNMP MIB') }}

@stop

@section('content_left')

	<h2>Create SNMP MIB</h2>	

	{{ Form::open(array('route' => array('DeviceType.store', 0), 'method' => 'POST')) }}

		@include('DeviceType.form', array ('devicetype' => null))
	
	{{ Form::submit('Create') }}
	{{ Form::close() }}

@stop