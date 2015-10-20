@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('Cmts.index', 'CMTS') }}

@stop

@section('content_left')

	<h2>CMTS List</h2>

	{{ Form::open(array('route' => 'Cmts.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}
	
	{{ Form::open(array('route' => array('Cmts.destroy', 0), 'method' => 'delete')) }}

		@foreach ($CmtsGws as $CmtsGw)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$CmtsGw->id.']') }}
						{{ HTML::linkRoute('Cmts.edit', (($CmtsGw->name == '') ? $CmtsGw->hostname : 'cm-'.$CmtsGw->name), $CmtsGw->id) }}
					</td>
				</tr>

				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}


@stop
