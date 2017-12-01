@extends ('Layout.default')

@section ('content')

	<div class="card card-inverse col-md-{{$edit_left_md_size}}">
		@if(isset($panel_right))
		<div class="card-header">
			<ul class="nav nav-tabs card-header-tabs d-flex">
				@foreach ($panel_right as $tabs)
					@if ($tabs['route'] == Route::getCurrentRoute()->getName())
						<?php
							$class = 'active';
							$blade = \Input::get('blade');
							if (is_null($blade))
							{
								$class = !isset($tabs['link'][1]) || $tabs['link'][1] == 'blade=0' ? 'active' : '';
							}
							else if (isset($tabs['link'][1]))
							{
								$class = 'blade='.$blade == $tabs['link'][1] ? 'active' : '';
							}
						?>
						<li class="nav-item {{$class}}" role="tab"> {{ HTML::linkRoute($tabs['route'], $tabs['name'], $tabs['link']) }}</li>
					@elseif ($tabs['name'] == "Logging")
						<li class="nav-item order-12 ml-auto" role="tab"><a id="loggingtab" class="" href="#logging" data-toggle="tab"> Logging</a></li>
					@else
						<li role="tab"> {{ HTML::linkRoute($tabs['route'], $tabs['name'], $tabs['link']) }}</li>
					@endif
				@endforeach
			</ul>
		</div>
		@endif
		<div class="col-12 card-block">
				@yield('content_left')
		</div>
	</div>
	@yield('content_right')
	@yield('content_right_extra')

@stop
