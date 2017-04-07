@extends ('Layout.default')

@section ('content')

<div class="row col-md-12">

	{{-- We need to include sections dynamically: always content left and if needed content right - more than 1 time possible --}}
	@include ('bootstrap.panel', array ('content' => 'content_left', 'view_header' => $view_header, 'md' => isset($index_left_md_size) ? $index_left_md_size : 12))
	@if (isset($view_header_right))
		@include ('bootstrap.panel', array ('content' => 'content_right',
											'view_header' => $view_header_right,
											'md' => isset($index_left_md_size) ? $index_left_md_size : 12))
	@endif


</div>

@stop