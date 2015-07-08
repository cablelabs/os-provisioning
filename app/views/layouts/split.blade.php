@extends ('layouts.default')

@include ('layouts.header')

<hr>

@section ('content')

@yield('content_top')
<hr>

	<table>
		<tr>
			<td width="400">@yield('content_left')</td> 
			<td width="50"></td>
			<td width="400" valign="top">@yield('content_right')</td>
		</tr>
	</table>

@stop