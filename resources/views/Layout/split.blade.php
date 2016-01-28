@extends ('Layout.default')

@section ('content')

<div class="row col-md-12">

	@include ('bootstrap.panel', array ('content' => 'content_left', 'view_header' => $view_header, 'md' => 6))

	@yield('content_right')

</div>

@stop