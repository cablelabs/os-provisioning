@extends ('Layout.default')

@section ('content')

<div class="row col-md-12">

	{{-- We need to include sections dynamically: always content left and if needed content right - more than 1 time possible --}}
	@include ('bootstrap.panel', array ('content' => 'content_left', 'view_header' => $view_header, 'md' => 8))
	@if (isset($view_header_right))
		@include ('bootstrap.panel', array ('content' => 'content_right', 'view_header' => $view_header_right, 'md' => 4))
	@endif


</div>

@stop