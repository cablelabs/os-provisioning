<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<?php

			// get the base part of current route and add to title
			// helpful with many open tabs
			$_ = explode('.', Route::getCurrentRoute()->getName());
			array_pop($_);
			$_ = implode('.', $_);

			echo "<title>NMS – $_</title>";

		?>
		@include ('bootstrap.header')

	</head>

	<body <?php if(isset($body_onload)) echo "onload=$body_onload()";?> >

		<div id="page-container" class="fade page-sidebar-fixed page-header-fixed in">

			@include ('Layout.header')

			@include ('bootstrap.sidebar')

			<div id="content" class="content p-t-15">

					@if(isset($panel_right))
						<ul class="nav nav-pills hidden-xs hidden-sm pull-right p-b-5">
							@foreach ($panel_right as $menu)

								@if ($menu['route'] == Route::getCurrentRoute()->getName())
									<?php 
										$class = 'active';
										$blade = \Input::get('blade');
										if (is_null($blade))
										{
											$class = !isset($menu['link'][1]) || $menu['link'][1] == 'blade=0' ? 'active' : '';
										}
										else if (isset($menu['link'][1]))
										{
											$class = 'blade='.$blade == $menu['link'][1] ? 'active' : '';
										}
									?>
									<li class="{{$class}}" role="presentation"> {{ HTML::linkRoute($menu['route'], $menu['name'], $menu['link']) }}</li>
								@else
									<li role="presentation"> {{ HTML::linkRoute($menu['route'], $menu['name'], $menu['link']) }}</li>
								@endif
							@endforeach
						</ul>
						<div class="panel panel-default hidden-md hidden-lg">
							<div class="panel-body">
								<ul class="nav nav-pills nav-justified pull-right p-b-5">
									@foreach ($panel_right as $menu)
										@if ($menu['route'] == Route::getCurrentRoute()->getName())
											<li class="active" role="presentation"> {{ HTML::linkRoute($menu['route'], $menu['name'], $menu['link']) }}</li>
										@else
											<li role="presentation"> {{ HTML::linkRoute($menu['route'], $menu['name'], $menu['link']) }}</li>
										@endif
									@endforeach
								</ul>
							</div>
						</div>
					@endif

				@yield ('content')
			</div>

			<!-- begin scroll to top btn -->
			  <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade p-l-5" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
			 <!-- end scroll to top btn -->
			@include ('bootstrap.footer')
			@yield ('java')
		</div>

	</body>

</html>
