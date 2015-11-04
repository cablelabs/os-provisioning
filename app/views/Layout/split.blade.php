@extends ('Layout.default')

@section ('content')

<div class="row">

	@include ('bootstrap.panel', array ('content' => 'content_left', 'md' => 6))
	@include ('bootstrap.panel', array ('content' => 'content_right', 'md' => 3))

</div>

@stop