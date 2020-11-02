<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{$html_title}}</title>
    @include ('bootstrap.header')
    @yield('head')
</head>
<body {{ isset($body_onload) ? "onload=$body_onload()" : ""}}>

    <div id="page-container" class="d-flex flex-column fade page-sidebar-fixed page-header-fixed in" style="min-height:100%;">
        @include ('Layout.header')
        @include ('bootstrap.sidebar')

        <div id="content" class="d-flex flex-column content p-t-15 p-b-0" style="flex:1;">
            @if(session('GlobalNotification'))
                @foreach (session('GlobalNotification') as $name => $options)
                    <div class="alert alert-{{ $options['level'] }} alert-dismissible fade show" role="alert">
                        <h4 class="text-center alert-heading">{{ trans('messages.' . $options['message']) }} </h4>
                        <p class="mb-0 text-center">
                            {{ trans('messages.' . $options['reason']) }}
                            <a href="{{ route('User.profile', $user->id) }}" class="alert-link">
                                    {{ trans('messages.PasswordClick') }}
                            </a>
                        </p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endforeach
            @endif
            @if(session('DashboardNotification'))
                @foreach (session('DashboardNotification') as $name => $options)
                    <div class="alert alert-{{ $options['level'] }} alert-dismissible fade show" role="alert">
                        <p class="mb-0 text-center">
                            {{$options['message'] }}
                        </p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endforeach
            @endif
            <div class="d-flex flex-column d-print-flex" style="flex: 1;">
                @yield ('content')
            </div>
        </div>
    </div>

    @include ('Generic.userGeopos')

    @include ('bootstrap.footer')
    @yield ('form-javascript')
    @yield ('javascript')
    @yield ('javascript_extra')
    @yield('mycharts')

    {{-- scroll to top btn --}}
    <a href="javascript:;"
        class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade d-flex"
        data-click="scroll-top"
        style="justify-content: space-around;align-items: center">
        <i class="fa fa-angle-up m-0"></i>
    </a>

</body>
</html>
