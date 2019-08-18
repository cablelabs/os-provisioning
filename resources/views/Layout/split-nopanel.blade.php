@extends ('Layout.default')

@section ('content')
    <div class="card card-inverse col-12 p-b-5 p-t-10">
        @if(isset($tabs))
        <div class="card-header m-b-15">
            <ul class="nav nav-tabs card-header-tabs d-flex" id='tabs'>
                @foreach ($tabs as $key => $tab)
                    @php $firstKey = $key == 0 ? $tab['name'] : '';
                    @endphp
                    @if ($tab['name'] == "Logging")
                        <li class="nav-item order-12 ml-auto" role="tab" style="float: right">
                            <a id="loggingtab" class="" href="#logging" data-toggle="tab"> Logging</a>
                        </li>
                    @elseif(isset($tab['route']))
                        <li class="nav-item" role="tab"> {{ HTML::linkRoute($tab['route'], $tab['name'], $tab['link']) }}</li>
                    @else
                        {{-- probably the <a> tag must be set to active according to docu --}}
                        <li class="nav-item {{$firstKey == $tab['name'] ? 'show' : ''}}" role="tab">
                            <a id="{{$tab['name'].'tab'}}" class="{{$firstKey == $tab['name'] ? 'active' : ''}}" href="#{{$tab['name']}}" data-toggle="tab">{{$tab['name']}}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row">
            <div class="card card-inverse col-lg-{{(!isset($relations) || empty($relations)) ? '12' : $edit_left_md_size}}">
                @yield('content_left')
            </div>
            @yield('content_right')
        </div>
    </div>
@stop
