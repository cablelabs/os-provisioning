    <!-- begin #header -->
    <div id="header" class="header navbar navbar-default navbar-fixed-top">
      <!-- begin container-fluid -->
      <div class="container-fluid">
        <!-- begin mobile sidebar expand / collapse button -->
        <div class="navbar-header">
          <a href="javascript:;" class="navbar-brand"><span class="navbar-logo"></span> {{$header}}</a>
          <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>




        </div>
        <!-- end mobile sidebar expand / collapse button -->
    
        <div class="col-md-5">

              <br>
              <h5>
                @yield('content_top')
              </h5>
        </div>

        <ul class="nav navbar-nav navbar-right">
        <li>
            <?php
              // searchscope for following form is 'all' => search within all models
              $next_scope = 'all';
              $preselect_field = $preselect_value = null; // drop pre-selection
            ?>
                 
            {{ Form::model(null, array('route'=>'Base.fulltextSearch', 'method'=>'GET')) }}
              @include('Generic.searchform')
            {{ Form::close() }}

            <!--   <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button> -->
        </li>

          <li class="dropdown">
            <a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle f-s-14">
              <i class="fa fa-bell-o"></i>
              <span class="label">0</span>
            </a>
<!--
            <ul class="dropdown-menu media-list pull-right animated fadeInDown">
              <li class="dropdown-header">Notifications (5)</li>
              <li class="media">
                <a href="javascript:;">
                  <div class="pull-left media-object bg-red"><i class="fa fa-bug"></i></div>
                  <div class="media-body">
                    <h6 class="media-heading">Server Error Reports</h6>
                    <div class="text-muted">3 minutes ago</div>
                  </div>
                </a>
              </li>
              <li class="media">
                <a href="javascript:;">
                  <div class="pull-left"><img src="assets/img/user-1.jpg" class="media-object" alt="" /></div>
                  <div class="media-body">
                    <h6 class="media-heading">John Smith</h6>
                    <p>Quisque pulvinar tellus sit amet sem scelerisque tincidunt.</p>
                    <div class="text-muted">25 minutes ago</div>
                  </div>
                </a>
              </li>
              <li class="media">
                <a href="javascript:;">
                  <div class="pull-left"><img src="assets/img/user-2.jpg" class="media-object" alt="" /></div>
                  <div class="media-body">
                    <h6 class="media-heading">Olivia</h6>
                    <p>Quisque pulvinar tellus sit amet sem scelerisque tincidunt.</p>
                    <div class="text-muted">35 minutes ago</div>
                  </div>
                </a>
              </li>
              <li class="media">
                <a href="javascript:;">
                  <div class="pull-left media-object bg-green"><i class="fa fa-plus"></i></div>
                  <div class="media-body">
                    <h6 class="media-heading"> New User Registered</h6>
                    <div class="text-muted">1 hour ago</div>
                  </div>
                </a>
              </li>
              <li class="media">
                <a href="javascript:;">
                  <div class="pull-left media-object bg-blue"><i class="fa fa-envelope"></i></div>
                  <div class="media-body">
                    <h6 class="media-heading"> New Email From John</h6>
                    <div class="text-muted">2 hour ago</div>
                  </div>
                </a>
              </li>
              <li class="dropdown-footer text-center">
                <a href="javascript:;">View more</a>
              </li>
            </ul>
          </li>
-->
          <li class="dropdown navbar-user">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{asset('components/assets-admin/img/user-11.jpg')}}" alt="" /> 
              <span class="hidden-xs">{{\Auth::user()->first_name.' '.\Auth::user()->last_name}}</span> <b class="caret"></b>
            </a>
            <ul class="dropdown-menu animated fadeInLeft">
              <li class="arrow"></li>
              <li><a href="{{route('Authuser.edit', \Auth::user()->id)}}">User Settings</a></li>
              <li><a href="{{route('Authuser.index')}}">User Global Settings</a></li>
              <!-- <li><a href="javascript:;"><span class="badge badge-danger pull-right">2</span> Inbox</a></li> -->
              <li class="divider"></li>
              <li><a href="{{route('Auth.logout')}}">Log Out</a></li>
            </ul>
          </li>

          <li class="dropdown navbar-user">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs">Main Menu</span> <b class="caret"></b>
            </a>
            <ul class="dropdown-menu animated fadeInLeft">
              <li class="arrow"></li>

              @foreach ($menus as $menu)
                @foreach ($menu as $a)
                  @foreach ($a as $name => $link)
                    <li><a href="{{route($link)}}">{{ $name }}</a></li>
                  @endforeach
                @endforeach
              @endforeach
            </ul>
          </li>

        </ul>
        <!-- end header navigation right -->
      </div>
      <!-- end container-fluid -->
    </div>
    <!-- end #header -->

