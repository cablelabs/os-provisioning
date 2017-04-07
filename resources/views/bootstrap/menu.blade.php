    <!-- begin #header -->
    <div id="header" class="header navbar navbar-default navbar-fixed-top">
      <!-- begin container-fluid -->
      <div class="container-fluid">
        <!-- begin mobile sidebar expand / collapse button -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed navbar-toggle-left" data-click="sidebar-minify">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
          <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="javascript:;" class="navbar-brand"><span class="navbar-logo hidden-xs"></span> <span class="hidden-xs">{{$header}}</span></a>
        </div>
        <!-- end mobile sidebar expand / collapse button -->

        <div class="col-md-3 p-t-15">
              <h5>
                @yield('content_top')
              </h5>
        </div>


        <ul class="nav navbar-nav navbar-right">

			{{-- TODO: discuss the following draft (position, coding) --}}
			<?php
				if (\PPModule::is_active('provvoipenvia')) {

					echo "<!-- count of user interaction needing EnviaOrders -->";

					echo "<li style='font-size: 2em; font-weight: bold'>";

					$user_interaction_needing_enviaorder_count = Modules\ProvVoipEnvia\Entities\EnviaOrder::get_user_interaction_needing_enviaorder_count();
					echo '<a href="/lara/admin/EnviaOrder?show_filter=action_needed" target="_self">';

					if ($user_interaction_needing_enviaorder_count > 0) {
						echo "<span style='color: #f00; text-decoration:none' title='".$user_interaction_needing_enviaorder_count." user interaction needing EnviaOrders'>✘<sup>";
						echo $user_interaction_needing_enviaorder_count.'</sup>';
					}
					else {
						echo "<span style='color: #080; text-decoration:none' title='No user interaction needing EnviaOrders'>✔";
					}
					echo "</a>";

					echo "</li>";
				}
			?>

        <!-- global search form -->
          <li>
            <a id="togglesearch" href="javascript:;" class="icon notification waves-effect waves-light" data-toggle="navbar-search"><i class="fa fa-search fa-lg" aria-hidden="true"></i></a>
          </li>
          <li class="dropdown navbar-user">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-user-circle-o fa-lg" aria-hidden="true"></i>
              <span class="hidden-xs"><img src="{{asset('components/assets-admin/img/user-11.jpg')}}" alt ="" />  {{\Auth::user()->first_name.' '.\Auth::user()->last_name}}</span>
              <b class="caret"></b>
            </a>
            <ul class="dropdown-menu animated fadeInLeft">
              <li class="arrow"></li>
              <li><a href="{{route('Authuser.edit', \Auth::user()->id)}}"> <i class="fa fa-cog" aria-hidden="true"></i>  {{ \App\Http\Controllers\BaseViewController::translate_view('UserSettings', 'Menu')}}</a></li>
              <li><a href="{{route('Authuser.index')}}"><i class="fa fa-cogs" aria-hidden="true"></i>  {{ \App\Http\Controllers\BaseViewController::translate_view('UserGlobSettings', 'Menu')}}</a></li>
              <li class="divider"></li>
              <li><a href="{{route('Auth.logout')}}"><i class="fa fa-sign-out" aria-hidden="true"></i>  {{ \App\Http\Controllers\BaseViewController::translate_view('Logout', 'Menu')}}</a></li>
            </ul>
          </li>
        </ul>
        <!-- end header navigation right -->
        <div class="search-form bg-white">
              {{ Form::model(null, array('route'=>'Base.fulltextSearch', 'method'=>'GET'), 'simple') }}
              {{ Form::hidden('mode', 'simple') }}
              {{ Form::hidden('scope', 'all') }}
            <button class="search-btn" type="submit"><i class="fa fa-search fa-2x" aria-hidden="true"></i></button>
            <input id="globalsearch" type="text" name="query" class="form-control navbar" placeholder="<?php echo \App\Http\Controllers\BaseViewController::translate_view('EnterKeyword', 'Search'); ?>">
            <a href="#" class="close" data-dismiss="navbar-search"><i class="fa fa-angle-up fa-2x" aria-hidden="true"></i></a>
            {{ Form::close() }}
        </div>
      </div>
      <!-- end container-fluid -->
    </div>
    <!-- end #header -->