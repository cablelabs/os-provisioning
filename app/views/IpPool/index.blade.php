@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('ipPool.index', 'IP-Pools') }}

@stop

@section('content_left')

	<h2>IP-Pools</h2>

	{{ Form::open(array('route' => 'ipPool.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}
	
	{{ Form::open(array('route' => array('ipPool.destroy', 0), 'method' => 'delete')) }}

		@foreach ($ip_pools as $pool)

			<table>
			<tr>
				<td> 
					{{ Form::checkbox('ids['.$pool->id.']') }}
					<a href=ipPool/{{$pool->id}}/edit>{{ $pool->cmts->hostname.'-'.$pool->id }}</a>

				</td>
			</tr>

			</table>
		
		@endforeach

	<br>

	{{ Form::submit('Delete') }}
	{{ Form::close() }}


@stop