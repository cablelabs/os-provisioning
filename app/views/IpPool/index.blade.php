@extends ('Layout.split')

@section('content_top')

		{{ HTML::linkRoute('IpPool.index', 'IP-Pools') }}

@stop

@section('content_left')

	<h2>IP-Pools</h2>

	{{ Form::open(array('route' => 'IpPool.create', 'method' => 'GET')) }}
	{{ Form::submit('Create') }}
	{{ Form::close() }}
	
	{{ Form::open(array('route' => array('IpPool.destroy', 0), 'method' => 'delete')) }}

		@foreach ($ip_pools as $pool)

			<table>
			<tr>
				<td> 
					{{ Form::checkbox('ids['.$pool->id.']') }}
					{{ HTML::linkRoute('IpPool.edit', $pool->cmts->hostname, $pool->id) }}

				</td>
			</tr>

			</table>
		
		@endforeach

	<br>

	{{ Form::submit('Delete') }}
	{{ Form::close() }}


@stop
