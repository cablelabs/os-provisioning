@extends ('Layout.default')

@section ('content')

<div class="row">

	@include ('bootstrap.panel', array ('content' => 'content_left', 'view_header' => $view_header, 'md' => 6))
	@include ('bootstrap.panel', array ('content' => 'content_right', 'view_header' => $view_header_right, 'md' => 3))

</div>

@stop