@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('CmtsDownstream.index', 'CMTS') }}

@stop

@section('content_left')

	<h2>CMTS - Downstream List</h2>

	{{ Form::open(array('route' => 'CmtsDownstream.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}
	
	{{ Form::open(array('route' => array('CmtsDownstream.destroy', 0), 'method' => 'delete')) }}

		@foreach ($cmtsdownstreams as $cmtsdownstream)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$cmtsdownstream->id.']') }}
						{{ HTML::linkRoute('CmtsDownstream.edit', $cmtsdownstream->alias, $cmtsdownstream->id) }}
					</td>
				</tr>

				</table>
			
		@endforeach

	<br>
	
	{{ Form::submit('Delete') }}
	{{ Form::close() }}


@stop