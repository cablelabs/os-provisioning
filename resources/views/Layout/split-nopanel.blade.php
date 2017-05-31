@extends ('Layout.default')

@section ('content')

<div class="row col-md-12">
	<div class="col-md-{{isset($edit_left_md_size) ? $edit_left_md_size : 5}}">
		<div class="col-md-12 well"><h1 class="page-header">{{$view_var->view_icon().' '.$view_header}}</h1>
			@yield('content_left')
		</div>
	</div>
	@yield('content_right')
	@yield('content_right_extra')

</div>

@stop
