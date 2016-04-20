<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<title>NMS</title>
		@include ('bootstrap.header')
	</head>

	<body <?php if(isset($body_onload)) echo "onload=$body_onload()";?> >

		<div id="page-container" class="fade page-sidebar-fixed page-header-fixed in">

			@include ('Layout.header')

			@include ('bootstrap.sidebar')

			<div id="content" class="content">

				<ol class="breadcrumb pull-right">
					@if(isset($panel_right))
						@foreach ($panel_right as $menu)
							<?php
				              $route = $menu['route'];
				              $name  = $menu['name'];
				              $link  = $menu['link'];
				            ?>
				            <li> {{ HTML::linkRoute($route, $name, $link) }}</li>
						@endforeach
					@endif
				</ol>

				@yield ('content')
			</div>

			@include ('bootstrap.footer')
		</div>

	</body>

</html>
