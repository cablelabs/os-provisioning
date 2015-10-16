@extends ('layouts.split')

@section('content_top')

		{{ HTML::linkRoute('cmtsdownstream.index', 'CMTS') }}

@stop

@section('content_left')

	<h2>CMTS - Downstream List</h2>

	{{ Form::open(array('route' => 'cmtsdownstream.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}
	
	{{ Form::open(array('route' => array('cmtsdownstream.destroy', 0), 'method' => 'delete')) }}

		@foreach ($cmtsdownstreams as $cmtsdownstream)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$cmtsdownstream->id.']') }}
						<a href=cmtsdownstream/{{$cmtsdownstream->id}}/edit>{{$cmtsdownstream->alias}} </a>
					</td>
				</tr>

				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}


@stop