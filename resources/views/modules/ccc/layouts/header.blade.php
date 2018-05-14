<div id="page-container" class="fade page-sidebar-fixed page-header-fixed in">
	<div id="sidebar" class="sidebar">
</div>


    {{-- begin #header --}}
    <div id="header" class="header navbar navbar-default navbar-fixed-top">
      <!-- begin container-fluid -->
      <div class="container-fluid">
        <!-- begin mobile sidebar expand / collapse button -->
        <div class="navbar-header">
          <a href="javascript:;" class="navbar-brand">{{ trans('messages.ccc') }}</a>
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
              <a href="{{route('HomeCcc')}}">{{ trans('messages.home') }}</a>
          </h5>
        </div>

        <!-- global search form -->
        <ul class="nav navbar-nav navbar-right">

          <li class="dropdown navbar-user">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{asset('components/assets-admin/img/user-11.jpg')}}" alt="" />
              <span class="hidden-xs">{{\Auth::guard('ccc')->user()->first_name.' '.\Auth::guard('ccc')->user()->last_name}}</span> <b class="caret"></b>
            </a>
            <ul class="dropdown-menu animated fadeInLeft">
              <li class="arrow"></li>
              <li><a href="{{route('CustomerPsw')}}">{{ trans('messages.password_change') }}</a></li>
              <li class="divider"></li>
              <li><a href="{{route('CustomerAuth.logout')}}">{{trans('messages.log_out')}}</a></li>
            </ul>
          </li>


        </ul>
        <!-- end header navigation right -->
      </div>
      <!-- end container-fluid -->
    </div>
    {{-- end #header --}}
</div>
