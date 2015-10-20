@extends ('layouts.default')

@include ('layouts.header')

<hr>

@section ('content')

@yield('content_top')

<hr>
<p align="right">
	@yield('content_top_2')
</p>

<hr>

	<table>
		<tr>
			<td width="400" valign="top">@yield('content_left')</td> 
			<td width="50"></td>
			<td width="400" valign="top">@yield('content_right')</td>
		</tr>
	</table>

@stop