@extends ('provbase::layouts.split')

@section('content_dash')
    <div class="d-flex flex-wrap justify-content-between" style="min-height: 135px;">
    <div class="d-flex justify-content-end align-self-start {{ ($dash && count($dash) == 1) ? 'order-1 order-sm-3' : 'order-3'}}" style="flex: 1">
        @include('Generic.documentation', ['documentation' => $modem->help])
    </div>
    @if ($dash)
        <div class="{{ count($dash) == 1 ? 'col-sm-10 col-xl-11 order-2' : '' }} ">
            @foreach ($dash as $key => $info)
                <div class="alert alert-{{$info['bsclass']}} fade show">
                    <div>
                        {{ $info['text'] }}
                    </div>
                    @if (isset($info['instructions']))
                        <div class="m-t-10 m-b-5">
                            <code class="p-5">{{ $info['instructions'] }}</code>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
    </div>
@stop

@section('content_ping')
    <div class="tab-content">
        <div class="tab-pane fade in" id="ping-test">
            @if ($online)
                <font color="green"><b>Modem is Online</b></font><br>
            @else
                <font color="red">{{trans('messages.modem_offline')}}</font>
            @endif
            {{-- pings are appended dynamically here by javascript --}}
        </div>

        <div class="tab-pane fade in" id="flood-ping">
            <form method="POST">Type:
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <select class="select2 form-control m-b-20" name="floodPing" style="width: 100%;">
                    <option value="1">low load: 500 packets of 56 Byte</option> {{-- needs approximately 5 sec --}}
                    <option value="2">average load: 1000 packets of 736 Byte</option> {{-- needs approximately 10 sec --}}
                    <option value="3">big load: 2500 packets of 56 Byte</option> {{-- needs approximately 30 sec --}}
                    <option value="4">huge load: 2500 packets of 1472 Byte</option> {{-- needs approximately 30 sec --}}
                </select>

                {{-- Form::open(['route' => ['Modem.floodPing', $modem->id]]) --}}
                @if (isset($floodPing))
                    <table class="m-t-20">
                    @foreach ($floodPing as $line)
                        <tr><td><font color="grey">{{$line}}</font></td></tr>
                    @endforeach
                    </table>
                @endif
                <div class="text-center">
                    <button class="btn btn-primary m-t-10" type="submit">Send Ping</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('content_log')
    <div class="tab-content">
        @include('provbase::Modem.logLeaseConfTabs')
    </div>
@stop

@if (Module::collections()->has('HfcCustomer'))
    @section('content_proximity_search')

        {!! Form::open(array('route' => 'CustomerTopo.show_prox', 'method' => 'GET')) !!}
        <div class="row">
        {!! Form::hidden('id', $modem->id) !!}
        {!! Form::number('radius', '1000') !!}
        {!! Form::submit(trans('view.Button_Search'), ['style' => 'simple']) !!}
        {!! Form::label('radius', 'Radius / m', ['class' => 'col-md-2 control-label']) !!}
        </div>
        {!! Form::close() !!}

    @stop
@endif

@section ('javascript')

<script type="text/javascript">

@if ($ip)
    $(document).ready(function() {
        setTimeout(function() {
            var source = new EventSource(" {{ route('Modem.realtimePing', $ip) }}");

            source.onmessage = function(e) {
                // close connection
                if (e.data == 'finished')
                {
                    source.close();
                    return;
                }

                document.getElementById('ping-test').innerHTML += e.data;
            }
        }, 500);
    });
@endif
</script>

@include('Generic.handlePanel')

@stop
