@extends('Layout.split')


@section('content_top')

	{{ HTML::linkRoute($route_name, 'Global Config Page') }}

@stop


@section('content_left')

	<table>
	@foreach($links as $mod => $name)
		<tr> 
			<td> {{ HTML::linkRoute($name.'.edit', $name, 1) }} </td>
		</tr>
	@endforeach
	</table>

@stop
