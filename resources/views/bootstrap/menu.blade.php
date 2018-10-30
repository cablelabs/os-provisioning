{{-- begin Navbar --}}
<nav id="header" class="header navbar navbar-expand navbar-default navbar-fixed-top">
	{{-- only one row Navbar --}}
    <div class="row">
    	{{-- begin mobile sidebar expand / collapse button --}}
        <button type="button" class="navbar-toggle m-l-20" data-click="sidebar-toggled">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a href="javascript:;" class="navbar-brand d-none d-sm-none d-md-block">
            <span><img src="{{asset('images/nmsprime-logo.png')}}" style="width:100%; margin-top:-10px; margin-left:5px" class=""></span>
        </a>
      	{{-- end mobile sidebar expand / collapse button --}}
			<div class="col tab-overflow p-t-5">
				<ul class="nav nav-pills p-t-5">
					<li class="prev-button"><a href="javascript:;" data-click="prev-tab" class="m-t-10"><i class="fa fa-arrow-left"></i></a></li>
					@yield('content_top')
					<li class="next-button"><a href="javascript:;" data-click="next-tab" class="m-t-10"><i class="fa fa-arrow-right"></i></a></li>
				</ul>
			</div>
			<ul class="navbar-nav ml-auto">
				{{-- global search form --}}
				<li class="navbar-nav nav p-t-15">
					<a id="togglesearch" href="javascript:;" class="icon notification waves-effect waves-light m-t-5" data-toggle="navbar-search"><i class="fa fa-search fa-lg" aria-hidden="true"></i></a>
				</li>

				{{-- Help Section --}}
				<li class="nav-item dropdown">
					<a id="navbarDropdown"
						class="nav-link dropdown-toggle"
						href="#"
						role="button"
						data-toggle="dropdown"
						aria-haspopup="true"
						aria-expanded="false">
						<i class="fa fa-question fa-2x" aria-hidden="true"></i>
						<span class="d-none d-sm-none d-md-inline">
						</span>
						<b class="caret"></b>
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown" style="right: 0;left:auto;">
						<a class="dropdown-item" href="https://devel.roetzer-engineering.com/" target="_blank">
							<i class="fa fa-question-circle" aria-hidden="true" style="width: 20px;"></i>Documentation
						</a>
						<a class="dropdown-item" href="https://www.youtube.com/channel/UCpFaWPpJLQQQLpTVeZnq_qA" target="_blank">
							<i class="fa fa-tv" aria-hidden="true" style="width: 20px;"></i>Youtube
						</a>
						<a class="dropdown-item" href="https://nmsprime.com/forum" target="_blank">
							<i class="fa fa-wpforms" aria-hidden="true" style="width: 20px;"></i>Forum
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href={{route('SupportRequest.index')}}>
							<i class="fa fa-envelope-open" aria-hidden="true" style="width: 20px;"></i>Professional Help
						</a>
					</div>
				</li>

				@if (Module::collections()->has('Dashboard'))
					{{-- Modem Statistics (Online/Offline) --}}
					<li  class='m-t-10' style='font-size: 2em; font-weight: bold'>
						<a href="{{ route('CustomerTopo.show_impaired') }}" style="text-decoration: none;">
							@if (is_object($modem_statistics))
								<span data-toggle="tooltip" data-placement="auto" title="{{trans_choice('messages.modem_statistics',1)}}">
									<i class="{{ $modem_statistics->fa }} fa-lg text-{{ $modem_statistics->style }}"></i>
									<span class="badge badge-{{ $modem_statistics->style }}">{!! $modem_statistics->text !!}</span>
								</span>
							@endif
						</a>
					</li>
				@endif
				@if (Module::collections()->has('ProvVoipEnvia'))
					{{-- count of user interaction needing EnviaOrders --}}
					<li  class='m-t-10' style='font-size: 2em; font-weight: bold'>
						<a href="{{route('EnviaOrder.index', ['show_filter' => 'action_needed'])}}" target="_self" style="text-decoration: none;">
							@if ($envia_interactioncount > 0)
								<span data-toggle="tooltip" data-placement="auto" title="{{ $envia_interactioncount }} {{ trans_choice('messages.envia_interaction', $envia_interactioncount )}}">
									<i class="fa fa-times fa-lg text-danger"></i>
									<span class="badge badge-danger">Envia<br>{{ $envia_interactioncount }}</span>
								</span>
							@else
								<span data-toggle="tooltip" data-placement="auto" title="{{ trans('messages.envia_no_interaction')}}">
									<i class="fa fa-check fa-lg text-success"></i>
								</span>
							@endif
						</a>
					</li>
				@endif
				<li class="nav-item dropdown m-r-20">
					<a id="navbarDropdown"
						class="nav-link dropdown-toggle"
						href="#"
						role="button"
						data-toggle="dropdown"
						aria-haspopup="true"
						aria-expanded="false">
						<i class="fa fa-user-circle-o fa-lg d-inline" aria-hidden="true"></i>
						<span class="d-none d-sm-none d-md-inline">
							{{ $user->first_name.' '. $user->last_name }}
						</span>
						<b class="caret"></b>
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown" style="right: 0;left:auto;">
						<a class="dropdown-item" href="{{ route('User.profile', $user->id) }}">
							<i class="fa fa-cog" aria-hidden="true"></i>
							{{ \App\Http\Controllers\BaseViewController::translate_view('UserSettings', 'Menu')}}
						</a>
						@if (Bouncer::can('update', App\User::class))
							<a class="dropdown-item" href="{{route('User.index')}}">
								<i class="fa fa-cogs" aria-hidden="true"></i>
								{{ \App\Http\Controllers\BaseViewController::translate_view('UserGlobSettings', 'Menu')}}
							</a>
						@endif
						@if (Bouncer::can('update', App\Role::class))
							<a class="dropdown-item" href="{{route('Role.index')}}">
								<i class="fa fa-users" aria-hidden="true"></i>
								{{ \App\Http\Controllers\BaseViewController::translate_view('UserRoleSettings', 'Menu')}}
							</a>
						@endif
						<div class="dropdown-divider"></div>
						{!! Form::open(['url' => route('logout.post')]) !!}
							<button class="dropdown-item">
								<i class="fa fa-sign-out" aria-hidden="true"></i>
								{{ \App\Http\Controllers\BaseViewController::translate_view('Logout', 'Menu')}}
							</button>
						{!!Form::close() !!}
					</div>
				</li>
			</ul>
		{{-- end header navigation right --}}
		<div class="search-form bg-white">
			{!! Form::model(null, array('route'=>'Base.fulltextSearch', 'method'=>'GET'), 'simple') !!}
			{!! Form::hidden('mode', 'simple') !!}
			{!! Form::hidden('scope', 'all') !!}
			<button class="search-btn" type="submit">
				<i class="fa fa-search fa-2x" aria-hidden="true"></i>
			</button>
			<input id="globalsearch" type="text" name="query" class="form-control navbar" placeholder="{{\App\Http\Controllers\BaseViewController::translate_view('EnterKeyword', 'Search')}}">
			<a href="#" class="close" data-dismiss="navbar-search"><i class="fa fa-angle-up fa-2x" aria-hidden="true"></i></a>
			{!! Form::close() !!}
		</div>
	</div> {{-- End ROW --}}
</nav>
