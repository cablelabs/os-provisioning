@extends ('Layout.split-nopanel')

@section('content_top')

    @include ('provbase::layouts.top')

@stop


@section ('content_left')

<div class="row">

    {{-- We need to include sections dynamically: always content left and if needed content right - more than 1 time possible --}}

    <div class="col-md-7 ui-sortable">
        @include ('bootstrap.panel', array ('content' => 'content_dash', 'view_header' => 'Dashboard', 'i' => 1))
        @if (isset($realtime))
            @include ('bootstrap.panel', array ('content' => 'content_realtime', 'view_header' => \App\Http\Controllers\BaseViewController::translate_label('Real Time Values'), 'i' => 2))
        @endif
        @if (isset($hostId))
            @include ('bootstrap.panel', array ('content' => 'content_cacti', 'view_header' => 'Monitoring', 'i' => 3))
        @endif
    </div>

    <div class="col-md-5 ui-sortable">

        @include ('bootstrap.panel', array ('content' => 'content_ping', 'view_header' =>
            '<ul class="nav nav-pills" id="ping-tab">
                <li role="presentation"><a href="#ping-test" data-toggle="pill">Default Ping</a></li>
                <li role="presentation"><a href="#flood-ping" data-toggle="pill">Flood-Ping</a></li>
            </ul>', 'i' => 4))
        @php
            $panelHeader = '<ul class="nav nav-pills" id="loglease">';
            foreach ($pills as $pill) {
                if ($$pill) {
                    $panelHeader .= "<li role=\"presentation\"><a href=\"#$pill\" data-toggle=\"pill\">".ucfirst($pill).'</a></li>';
                }
            }
            $panelHeader .= '</ul>';
        @endphp

        @include ('bootstrap.panel', array ('content' => 'content_log', 'view_header' => $panelHeader, 'i' => 5))

        @if (\Module::collections()->has('HfcCustomer'))
            @include ('bootstrap.panel', array ('content' => 'content_proximity_search', 'view_header' => trans('messages.proximity'), 'i' => 6))
        @endif
    </div>

    @if (Module::collections()->has('ProvMon') && ! $modem->isPPP())
        <div class="col-md-12 ui-sortable">
            @include ('bootstrap.panel', array ('content' => 'spectrum-analysis', 'view_header' => trans('messages.spectrum'),  'i' => '7'))
        </div>
    @endif

</div>

@stop
