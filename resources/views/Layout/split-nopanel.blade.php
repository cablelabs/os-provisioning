@extends ('Layout.default')

@section ('content')
    <div class="row flex-wrap-reverse" style="flex:1;">
        @if (isset($withHistory))
            <div class="d-flex col-lg-3 col-xl-2">
                <div class="card card-inverse p-l-15 p-r-15 p-t-5 p-b-5 m-t-10" style="flex:1 auto;">
                    @yield ('historyTable')
                </div>
            </div>
        @endif
        <div class="{{ isset($withHistory) ? 'd-flex' : '' }} col-12 col-lg-{{ isset($withHistory) ? 9 : 12 }} col-xl-{{ isset($withHistory) ? 10 : 12 }} m-t-10">
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
                                <li class="nav-item {{ \Route::getCurrentRoute()->action['as'] == $tab['route'] ? 'active' : '' }}" role="tab">
                                    <a href="{{ route($tab['route'], is_array($tab['link']) ? $tab['link'] : [$tab['link']]) }}">
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
    </div>
    @if (isset($withHistory))
        <div class="row">
            <div class="col-12 p-t-5">
                <div class="card card-inverse p-l-15 p-r-15 p-t-5 p-b-5 ">
                    @yield ('historySlider')
                </div>
            </div>
        </div>
    @endif
@stop
