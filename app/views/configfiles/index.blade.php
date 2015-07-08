@extends ('layouts.split')

@section('content_top')

	{{ HTML::linkRoute('configfile.index', 'Configfile') }}
	
@stop

@section('content_left')
	
	<h2>Configfile</h2>

	{{ Form::open(array('route' => 'configfile.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}
	

	{{ Form::open(array('route' => array('configfile.destroy', 0), 'method' => 'delete')) }}

		@foreach ($configfiles as $configfile)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$configfile->id.']') }}
						<a href=configfile/{{$configfile->id}}/edit>{{$configfile->name}}</a> 
					</td>
				</tr>
				</table>
			
		@endforeach

	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop