@extends('Layout.split')


@section('content_top')

	<li class="nav-tabs">{{ HTML::linkRoute( $route_name , $view_header ) }}</li>

@stop


@section('content_left')

		<table class="table">
		@foreach($links as $name => $link)
			<tr>
				<td> {{ HTML::linkRoute($link.'.edit', \App\Http\Controllers\BaseViewController::translate_label($name), 1) }} </td>
			</tr>
		@endforeach
@stop