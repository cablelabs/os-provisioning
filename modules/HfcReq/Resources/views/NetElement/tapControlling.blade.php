@extends ('Layout.split-nopanel')

@section('content_top')

    {!! $headline !!}

@stop

@section ('content_left')

<div class="row">
<div class="col-md-4">
    @section ('tap controlling')
        <button id="stateA" type="button" class="btn btn-success btn-lg m-r-10 m-b-5 rkm-button" onclick="sendSwitchCmd('A')">0 dB</button>
        <button id="stateB" type="button" class="btn btn-warning btn-lg m-r-10 m-b-5 rkm-button" onclick="sendSwitchCmd('B')">-6 dB</button>
        <button id="stateC" type="button" class="btn btn-danger btn-lg m-r-10 m-b-5 rkm-button" onclick="sendSwitchCmd('C')">-40 dB</button>
        <div id="response" class="m-t-10"></div>
    @stop

    @include ('bootstrap.panel', [
        'content' => 'tap controlling',
        'view_header' => trans('hfcsnmp::view.tapControlling'),
        'options' => $relation['panelOptions'] ?? null,
        'handlePanelPosBy' => 'nmsprime',
        'md' => 12,
        ])

    @section ('video controlling')
    <div class="form-group row m-r-5">
        <label for="line-nr" style="margin-top: 10px;" class="col-md-5 control-label">{{trans('hfcsnmp::view.lineNr')}}</label>
        <div class="col-md-3 m-r-5">
            <input class="form-control" name="line-nr" type="text" value="{{$lineNr}}" id="line-nr">
        </div>
        <button id="setLine" type="button" class="btn btn-info m-r-10 m-b-5" onclick="setLine()">Setzen</button>
        <div style="margin-left: auto">
            <a data-toggle="popover" data-html="true" data-container="body" data-trigger="hover" title="" data-placement="right"
                data-content="{{trans('hfcsnmp::help.lineNr')}}"
                data-original-title="{{trans('hfcsnmp::view.lineNr')}}">
                <i class="fa fa-2x p-t-5 fa-question-circle text-info"></i>
            </a>
        </div>
    </div>
    @stop

    @include ('bootstrap.panel', [
        'content' => 'video controlling',
        'view_header' => trans('hfcsnmp::view.videoControlling'),
        'options' => $relation['panelOptions'] ?? null,
        'handlePanelPosBy' => 'nmsprime',
        'md' => 12,
        ])

@DivClose()

    <div class="col-md-5 m-l-20">
        @DivOpen(1)
        @DivClose()
        @DivOpen(6)
            <img id="video" src="http://{{$hfcBaseConf->video_encoder}}/mjpg/1/video.mjpg?camera=1" alt="http://193.168.231.77:2019/mjpg/1/video.mjpg?camera=1">
        @DivClose()
    @DivClose()
@DivClose()

@stop


@section('javascript')
<script>
    /**
     * Notes: State is saved on server DB and updated via server php code
     */
    function sendSwitchCmd(state) {
        // var address = '{{$view_var->address1}}';
        // var current_state = '{{$view_var->state}}';
        // console.log("set " + address + " from " + current_state + " to " + state);
        // var type = "RKS";

        $("#response").html("");

        $.ajax({
            // async: false,
            // crossDomain: true,
            // url: 'http://{{$hfcBaseConf->rkm_server}}' + '/index.php',
            type: 'post',
            url: '{{ "/admin/NetElement/switchTapState" }}',
            data: {
                _token: "{{\Session::get('_token')}}",
                id: '{{ $view_var->id }}',
                state: state,
                // direct ajax doesnt work as CORS header is missing
                // switch for curl, switchRks for direct GET, switchExt for direct POST
                // action: "switchExt",
                // address: address,
                // tap: getTapNumber(address),
                // user: 'user',
                // pass: 'password',
            },
            error: function(data, status) {$("#response").html(data + ":" + status);},
            success: function(data) {
                console.log("Response: " + data);

                if (! $("#response").hasClass('alert')) {
                    $("#response").addClass('alert');
                }

                $("#response").html(data);

                if (data == 'OK') {
                    setActiveState(state);

                    $("#response").removeClass('alert-danger');
                    $("#response").addClass('alert-success');
                } else {
                    $("#response").removeClass('alert-success');
                    $("#response").addClass('alert-danger');
                }

                // var valid = data.search("RKS");     //answer ok
                // var error = data.search("Error");   //error
            }
        });
    }

    function setActiveState(state)
    {
        $('.rkm-button').css('border-color', '#FFFFFF');
        $('#state'+state).css('border-color', '#000000');

        $('.rkm-button').removeClass('activated');
        $('#state'+state).addClass('activated');
    }

    function getTapNumber(address)
    {
        var tap = 0;

        if (address.search('~') !== -1) {  //TAP?
            var array = address.split('~');

            address = array[0];
            tap = array[1];
        }

        return tap;
    }

    var setImgSize = function () {
        $("#video").width($(window).width() / 2.2);
        $("#video").height($(window).height() * 0.6);
    }

    window.onresize = setImgSize;
    window.onload = setImgSize;

    setActiveState("{{$view_var->state}}");

</script>
@stop
