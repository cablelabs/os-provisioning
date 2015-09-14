@extends ('Layout.default')

@include ('Layout.header')

<hr>

@section ('content')

@yield('content_top')

<hr>
<p align="right">
	@yield('content_top_2')
</p>

<hr>

<div class="row">

	@include ('bootstrap.panel', array ('content' => 'content_left', 'md' => 8))



	@include ('bootstrap.panel', array ('content' => 'content_right', 'md' => 4))

</div>



@stop