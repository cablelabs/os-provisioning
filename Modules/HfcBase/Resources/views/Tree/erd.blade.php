@extends ('Layout.single')

@section('content_left')

<head>
<meta http-equiv="refresh" content="60" >
<meta http-equiv="Pragma" content="no-cache">

<link href="{{asset('/modules/Hfcbase/alert.css')}}" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="{{asset('/modules/Hfcbase/alert.js')}}"></script>

<script type="text/javascript">

	/*
	 * Right Click in Schaltplan
	 */
	function getEl (id)
	{
	        target = '_blank';
	        kml    = 0;

	        alert ("Element Number: "+id, "<li><a target="+target+" href=tree.php?tree_sys_operation=tree_op_Change&tree_sys_rec="+id+"&kml="+kml+">Change</a></li>" + 
	                                      "<li><a target="+target+" href=tree.php?tree_sys_operation=tree_op_Delete&tree_sys_rec="+id+"&kml="+kml+">Delete </a></li>" + 
	                                      "<li><a target=_blank href=details.php?id="+id+">Details</a>", {width:150});
	        return false;
	}

</script>

<body>

	@section('content_top')
		{{ HTML::linkRoute('TreeErd.show', $view_header, [$field, $search]) }}
	@stop

	@include ('hfcbase::Tree.search')

	{{ Form::openDivClass(12) }}
		<img usemap="#tree{{$gid}}" src="{{asset("$file.svg")}}"></img>

		<?php 
			echo $usemap;

			if ($is_pos)
			{		
				$url = \Request::root().'/Tree/create?pos='.$search;
				echo "<h4><div align=\"center\"> <a href=$url>Add Device</a></div></h4>";
			}
		?>
	{{ Form::closeDivClass() }}


</body>

@stop