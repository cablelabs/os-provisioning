@extends ('Layout.default')

@php
    // actually this should be named contentMid now as there are 4 sections from left to right
    // where you can place content (leftLeft, left, right, rightRight)
    $leftMdSizeXl = $leftMdSizeLg = 12;
    $flex = '';

    if (! empty($__env->yieldContent('contentLeftLeft'))) {
        $leftMdSizeXl -= $mdSizes['leftLeftXl'];
        $leftMdSizeLg -= $mdSizes['leftLeftLg'];
        $flex = 'flex:1;';
    }

    if (! empty($__env->yieldContent('contentRightRight'))) {
        $leftMdSizeXl -= $mdSizes['rightRightXl'];
        $leftMdSizeLg -= $mdSizes['rightRightLg'];
    }
@endphp

@section ('content')
    <div class="row flex-wrap-reverse" style="{{ $flex }}">

        @yield('contentLeftLeft')

        <div class="d-flex col-12 col-lg-{{ $leftMdSizeLg }} col-xl-{{ $leftMdSizeXl }} m-t-10">
            <div class="card card-inverse p-b-5 p-t-10" style="display:flex;flex: 1;">
                @if(isset($tabs))
                <div class="card-header m-b-15 d-print-none" style="display:flex;">
                    <ul id="tabs" class="nav nav-tabs card-header-tabs d-flex" style="width:100%;">
                        @foreach ($tabs as $key => $tab)
                            @php
                                $firstKey = $key == 0 ? $tab['name'] : '';
                            @endphp
                            <!-- Logging tab -->
                            @if ($tab['name'] == "Logging")
                                <li class="nav-item order-12 ml-auto" role="tab" style="float: right">
                                    <a id="loggingtab" class="" href="#logging" data-toggle="tab">
                                        <i class="fa fa-lg fa-{{ $tab['icon'] ?? 'history' }}"></i> Logging
                                    </a>
                                </li>
                            <!-- Link to separate view -->
                            @elseif (isset($tab['route']))
                                <li class="nav-item" role="tab">
                                    <a href="{{ route($tab['route'], is_array($tab['link']) ? $tab['link'] : [$tab['link']]) }}" class="{{\Route::getCurrentRoute()->action['as'] == $tab['route'] ? 'active' : ''}}">
                                        @if (isset($tab['icon']))
                                            <i class="fa fa-lg fa-{{ $tab['icon'] }}"></i>
                                        @endif
                                        {{ \Lang::has('view.tab.'.$tab['name']) ? trans('view.tab.'.$tab['name']) : $tab['name'] }}
                                    </a>
                                </li>
                            <!-- Other tabs -->
                            @else
                                {{-- probably the <a> tag must be set to active according to docu --}}
                                <li class="nav-item {{$firstKey == $tab['name'] ? 'show' : ''}}" role="tab">
                                    <a id="{{$tab['name'].'tab'}}" class="{{$firstKey == $tab['name'] ? 'active' : ''}}" href="#{{$tab['name']}}" data-toggle="tab">
                                        @if (isset($tab['icon']))
                                            <i class="fa fa-lg fa-{{$tab['icon']}}"></i>
                                        @endif
                                        {{ \Lang::has('view.tab.'.$tab['name']) ? trans('view.tab.'.$tab['name']) : $tab['name'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="d-flex flex-wrap" style="display:flex;flex: 1;">
                    <div class="card card-inverse col-lg-{{(!isset($relations) || empty($relations)) ? '12' : $edit_left_md_size}}" style="{{ isset($withHistory) ? 'display:flex;flex: 1;' : '' }}">
                        @yield('content_left')
                    </div>
                    @yield('content_right')
                </div>
            </div>
        </div>

        @yield('contentRightRight')

    </div>

    @yield('contentBottom')

@stop
