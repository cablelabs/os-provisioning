@extends ('Layout.default')

@section ('content')

<div class="col-md-12">
	<div class="card bg-white">
		<div class="card-block">
		{{-- We need to include sections dynamically: always content left and if needed content right - more than 1 time possible --}}
		@yield ('content_left')
		</div>
		@if (isset($view_header_right))
			@include ('bootstrap.panel', array ('content' => 'content_right',
												'view_header' => $view_header_right,
												'md' => isset($index_left_md_size) ? $index_left_md_size : 12))
		@endif
	</div>
</div>

@stop
