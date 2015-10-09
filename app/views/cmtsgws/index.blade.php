@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('cmts.index', 'CMTS') }}

@stop

@section('content_left')

	<h2>CMTS List</h2>

	{{ Form::open(array('route' => 'cmts.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}
	
	{{ Form::open(array('route' => array('cmts.destroy', 0), 'method' => 'delete')) }}

		@foreach ($CmtsGws as $CmtsGw)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$CmtsGw->id.']') }}
						<a href=cmts/{{$CmtsGw->id}}/edit>{{''.(($CmtsGw->name == '') ? $CmtsGw->hostname : 'cm-'.$CmtsGw->name)}}</a>
					</td>
				</tr>

				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}


@stop