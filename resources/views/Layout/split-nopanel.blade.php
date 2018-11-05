@extends ('Layout.default')

@section ('content')

	<div class="card card-inverse col-12 p-b-5 p-t-10">
		@if(isset($tabs))
		<div class="card-header m-b-15">
			<ul class="nav nav-tabs card-header-tabs d-flex">
				@foreach ($tabs as $tab)
					@if ($tab['route'] == Route::getCurrentRoute()->getName())
						<?php
							$class = 'active';
							$blade = \Input::get('blade');
							if (is_null($blade))
							{
								$class = !isset($tab['link'][1]) || $tab['link'][1] == 'blade=0' ? 'active' : '';
							}
							else if (isset($tab['link'][1]))
							{
								$class = 'blade='.$blade == $tab['link'][1] ? 'active' : '';
							}
						?>
						<li class="nav-item {{$class}}" role="tab"> {{ HTML::linkRoute($tab['route'], $tab['name'], $tab['link']) }}</li>
					@elseif ($tab['name'] == "Logging")
						<li class="nav-item order-12 ml-auto" role="tab"><a id="loggingtab" class="" href="#logging" data-toggle="tab"> Logging</a></li>
					@else
						<li role="tab"> {{ HTML::linkRoute($tab['route'], $tab['name'], $tab['link']) }}</li>
					@endif
				@endforeach
			</ul>
		</div>
		@endif
		<div class="row">
			<div class="card card-inverse col-md-{{(!isset($relations) || empty($relations)) ? '12' : $edit_left_md_size}}">
				@yield('content_left')
			</div>
			@yield('content_right')
		</div>
	</div>
@stop
