@extends ('Layout.single')

@section('content_top')

	{{ HTML::linkRoute('Configfile.index', 'Configfile') }} / {{ HTML::linkRoute('Configfile.edit', 'configfile-'.$configfile->name, array($configfile->id)) }}
	
@stop

@section('content_left')

	<h2>Edit Configfile</h2>
	
	{{ Form::model($configfile, array('route' => array('Configfile.update', $configfile->id), 'method' => 'put')) }}

		@include('Configfile.form', $configfile)

	{{ Form::submit('Save') }}
	{{ Form::close() }}

@stop

@section('content_right')


@stop
