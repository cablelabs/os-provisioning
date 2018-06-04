@extends ('Layout.split-nopanel')

@section('head')
	<meta http-equiv="Pragma" content="no-cache">
	<link href="{{asset('/modules/hfcbase/alert.css')}}" rel="stylesheet" type="text/css" media="screen" />
@stop


@section('content_top')
	<li>{{ HTML::linkRoute('TreeErd.show', $view_header, [$field, $search]) }}</li>
@stop

@section('content_left')

	@DivOpen(12)
		<img usemap="#tree{{$gid}}" src="{{asset("$file.svg")}}"></img>

		{{ $usemap }}

		@if($is_pos)
			<h4>
				<div align="center">
					<a href="{{ \BaseRoute::get_base_url().'/NetElement/create?pos='.$search }}">Add Device</a>
				</div>
			</h4>
		@endif
	@DivClose()

@stop

@section('javascript')
	<script type="text/javascript" src="{{asset('/modules/hfcbase/alert.js')}}"></script>

	<script type="text/javascript">
		{{-- Right Click in Schaltplan --}}
		function getEl (id)
		{
				url    = '{{ \BaseRoute::get_base_url() }}'
				kml    = 0;

				alert ("Element Number: "+id, "<li><a href="+url+"/NetElement/"+id+"/edit>Change</a></li>" +
											"<li><a href="+url+"/NetElement/"+id+"/delete>Delete </a></li>" +
											"<li><a href=details.php?id="+id+">Details</a>", {width:150});
				return false;
		}
	</script>
@stop
