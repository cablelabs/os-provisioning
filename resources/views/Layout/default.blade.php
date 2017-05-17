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

				<ul class="nav nav-pills pull-right p-b-10">
					@if(isset($panel_right))
						@foreach ($panel_right as $menu)
							<li role="presentation"> {{ HTML::linkRoute($menu['route'], $menu['name'], $menu['link']) }}</li>
						@endforeach
					@endif
				</ul>

				@yield ('content')
			</div>

			<!-- begin scroll to top btn -->
			  <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
			 <!-- end scroll to top btn -->
			@include ('bootstrap.footer')
			@yield ('java')
		</div>

	</body>

</html>
