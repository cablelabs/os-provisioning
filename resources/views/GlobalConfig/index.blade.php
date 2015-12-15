@extends('Layout.split')


@section('content_top')

	{{ HTML::linkRoute($route_name, $view_header) }}

@stop


@section('content_left')

	<table>
	@foreach($links as $name)
		<tr> 
			<td> {{ HTML::linkRoute($name.'.edit', $name, 1) }} </td>
		</tr>
	@endforeach
	</table>

@stop
