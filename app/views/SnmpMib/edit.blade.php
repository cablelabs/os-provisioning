@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('SnmpMib.index', 'snmpmib') }} / {{ HTML::linkRoute('SnmpMib.edit', 'snmpmib-'.$snmpmib->name, array($snmpmib->id)) }}
	
@stop

@section('content_left')

	<h2>Edit SNMP MIB</h2>
	
	{{ Form::model($snmpmib, array('route' => array('SnmpMib.update', $snmpmib->id), 'method' => 'put')) }}

		@include('SnmpMib.form', $snmpmib)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')

@stop
