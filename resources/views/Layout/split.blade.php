@extends ('Layout.default')

@section ('content')

<div class="row col-md-12">

	@include ('bootstrap.panel', array ('content' => 'content_left', 'view_header' => $view_header,
										'md' => isset($edit_left_md_size) ? $edit_left_md_size : 5))

	@yield('content_right')
	@yield('content_right_extra')

</div>

@stop