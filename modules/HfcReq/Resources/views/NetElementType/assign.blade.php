@extends ('Layout.split')


@section('content_top')

@stop


@section('content_left')

	{{ Form::open(['route' => ['NetElementType.attach', $view_var->id], 'method' => 'post']) }}

		{{ Form::label('oids', 'Choose OIDs') }}
		{{ Form::select('oid_id[]', $oids, null, ['multiple' => 'multiple']) }}
		<br><br><br><br><br><br>
		{{ Form::submit('Attach OIDs') }}

	{{ Form::close() }}

@stop



@section('content_right')	

	@section('content_right_1')

	{{ Form::open(['route' => ['NetElementType.assign_oids', $view_var->id], 'method' => 'get']) }}

		{{ Form::label('mibfile', 'Choose MIB-File') }}
		{{ Form::select('mibfile_id', $mibs) }}
		{{ Form::submit('Attach OIDs') }}

	{{ Form::close() }}

	@stop

	@include ('bootstrap.panel', array ('content' => "content_right_1", 'view_header' => 'Attach all OIDs of a MIB', 'md' => 3))

@stop
