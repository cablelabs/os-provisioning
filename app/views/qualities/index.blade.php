@extends ('layouts.split')

@section('content_top')

	{{ HTML::linkRoute('quality.index', 'quality') }}
	
@stop

@section('content_left')
	
	<h2>quality</h2>

	{{ Form::open(array('route' => 'quality.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}


	{{ Form::open(array('route' => array('quality.destroy', 0), 'method' => 'delete')) }}

		@foreach ($qualities as $quality)

				<table>
				<tr>
					<td> 
						{{ Form::checkbox('ids['.$quality->id.']') }}
						<a href=quality/{{$quality->id}}/edit>{{$quality->name}}</a> 
					</td>
				</tr>
				</table>
			
		@endforeach

	{{ Form::submit('Delete') }}
	{{ Form::close() }}

@stop