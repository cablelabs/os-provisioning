@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('modem.index', 'Modems') }}

@stop

@section('content_left')

	<h2>Modem List</h2>

	{{ Form::open(array('route' => 'modem.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}
	
	<br>

	{{ Form::open(array('route' => array('modem.destroy', 0), 'method' => 'delete')) }}

		@foreach ($modems as $modem)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$modem->id.']') }}
						<a href=modem/{{$modem->id}}/edit>{{''.(($modem->name == '') ? $modem->hostname : 'cm-'.$modem->name)}}</a> 
					</td>
				</tr>

				</table>
			
		@endforeach

	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop