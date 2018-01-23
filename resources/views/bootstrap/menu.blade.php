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
        <a href="javascript:;" class="navbar-brand d-none d-sm-none d-md-block"><span class="navbar-logo"></span> <span>{{$header}}</span></a>
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
				@if (\PPModule::is_active('provvoipenvia'))
					{{-- count of user interaction needing EnviaOrders --}}
					<li  class='m-t-10' style='font-size: 2em; font-weight: bold'>
						<a href="{{route('EnviaOrder.index', ['show_filter' => 'action_needed'])}}" target="_self">
							@if (Modules\ProvVoipEnvia\Entities\EnviaOrder::get_user_interaction_needing_enviaorder_count() > 0)
								<span style='color: #f00; text-decoration:none' title='{{ Modules\ProvVoipEnvia\Entities\EnviaOrder::get_user_interaction_needing_enviaorder_count() }} user interaction needing EnviaOrders'>✘
								<sup>{{ Modules\ProvVoipEnvia\Entities\EnviaOrder::get_user_interaction_needing_enviaorder_count() }}</sup>
							@else
								<span style='color: #080; text-decoration:none' title='No user interaction needing EnviaOrders'>✔
							@endif
						</a>
					</li>
				@endif
				<li class="nav-item dropdown m-r-20">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-user-circle-o fa-lg d-inline" aria-hidden="true"></i>
						<span class="d-none d-sm-none d-md-inline">{{\Auth::user()->first_name.' '.\Auth::user()->last_name}}</span> <b class="caret"></b>
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
						<a class="dropdown-item" href="{{route('Authuser.edit', \Auth::user()->id)}}"> <i class="fa fa-cog" aria-hidden="true"></i>  {{ \App\Http\Controllers\BaseViewController::translate_view('UserSettings', 'Menu')}}</a>
						@if (\Auth::user()->is_admin() === true)
							<a class="dropdown-item" href="{{route('Authuser.index')}}"><i class="fa fa-cogs" aria-hidden="true"></i>  {{ \App\Http\Controllers\BaseViewController::translate_view('UserGlobSettings', 'Menu')}}</a>
							<a class="dropdown-item" href="{{route('Authrole.index')}}"><i class="fa fa-users" aria-hidden="true"></i> {{ \App\Http\Controllers\BaseViewController::translate_view('UserRoleSettings', 'Menu')}}</a>
						@endif
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="{{route('Auth.logout')}}"><i class="fa fa-sign-out" aria-hidden="true"></i>  {{ \App\Http\Controllers\BaseViewController::translate_view('Logout', 'Menu')}}</a>
					</div>
				</li>
			</ul>
		{{-- end header navigation right --}}
		<div class="search-form bg-white">
			{{ Form::model(null, array('route'=>'Base.fulltextSearch', 'method'=>'GET'), 'simple') }}
			{{ Form::hidden('mode', 'simple') }}
			{{ Form::hidden('scope', 'all') }}
			<button class="search-btn" type="submit">
				<i class="fa fa-search fa-2x" aria-hidden="true"></i>
			</button>
			<input id="globalsearch" type="text" name="query" class="form-control navbar" placeholder="<?php echo \App\Http\Controllers\BaseViewController::translate_view('EnterKeyword', 'Search'); ?>">
			<a href="#" class="close" data-dismiss="navbar-search"><i class="fa fa-angle-up fa-2x" aria-hidden="true"></i></a>
			{{ Form::close() }}
		</div>
	</div> {{-- End ROW --}}
</nav>
