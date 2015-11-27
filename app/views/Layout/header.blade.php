
	@include ('bootstrap.menu', array(
		'header' => 'Das Monster', 
		'menus' => array (
			'0' => array(

			),
			$view_header_links
		)
	))

	<hr><hr>


	<hr>


	<div class="col-md-6">
		@yield('content_top')
	</div>
	
	<div class="col-md-6">
		<p align="right">
			@yield('content_top_2')
		</p>
	</div>
	<hr>
