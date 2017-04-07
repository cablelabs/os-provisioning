@extends ('Layout.default')

@section ('content')

		@include ('bootstrap.panel', array ('content' => 'content_left', 'md' => isset($index_left_md_size) ? $index_left_md_size : 12))

@stop