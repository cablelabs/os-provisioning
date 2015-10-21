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

		@foreach ($cmts as $gw)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$gw->id.']') }}
						{{ HTML::linkRoute('Cmts.edit', (($gw->name == '') ? $gw->hostname : 'cm-'.$gw->name), $gw->id) }}
					</td>
				</tr>

				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}


@stop
