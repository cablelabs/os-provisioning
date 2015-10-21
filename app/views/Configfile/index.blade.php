@extends ('Layout.split')

@section('content_top')

	{{ HTML::linkRoute('Configfile.index', 'Configfile') }}
	
@stop

@section('content_left')
	
	<h2>Configfile</h2>

	{{ Form::open(array('route' => 'Configfile.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}


	{{ Form::open(array('route' => array('Configfile.destroy', 0), 'method' => 'delete')) }}

		@foreach ($configfiles as $configfile)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$configfile->id.']') }}
						{{ HTML::linkRoute('Configfile.edit', $configfile->name, $configfile->id) }}
					</td>
				</tr>
				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop
