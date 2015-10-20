@extends ('Layout.single')

@section('content_top')

	{{ HTML::linkRoute('configfile.index', 'Configfile') }} / {{ HTML::linkRoute('configfile.edit', 'configfile-'.$configfile->name, array($configfile->id)) }}
	
@stop

@section('content_left')

	<h2>Edit Configfile</h2>
	
	{{ Form::model($configfile, array('route' => array('configfile.update', $configfile->id), 'method' => 'put')) }}

		@include('configfiles.form', $configfile)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')


@stop