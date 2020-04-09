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
    <div class="form-group row controlling-container">
        <label for="line-nr" style="margin-top: 10px; max-width: 10em" class="control-label vid-set-item">{{trans('hfcsnmp::view.lineNr')}}</label>
        <input class="form-control vid-set-item" name="line-nr" type="text" value="{{$lineNr}}" id="line-nr">
        <button id="setLine" type="button" class="btn btn-info m-r-10 m-b-5 vid-set-item" onclick="setLine()">{{trans('messages.Set')}}</button>
        <a data-toggle="popover" data-html="true" data-container="body" data-trigger="hover" title="" data-placement="right"
            data-content="{{trans('hfcsnmp::help.lineNr')}}"
            data-original-title="{{trans('hfcsnmp::view.lineNr')}}">
            <i class="fa fa-2x p-t-5 fa-question-circle text-info"></i>
        </a>
    </div>
    <div class="row controlling-container">
        <div id="setLineResponse" class="m-t-10 ajaxResponse"></div>
    </div>
    <div class="form-group row m-r-5" style="justify-content: center;">
        <button id="setLineAuto" type="button" class="btn btn-purple m-r-10 m-b-5" onclick="setLine('auto')">{{trans('hfcsnmp::view.autoSwitchOn')}}</button>
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
            <!-- <img id="video" src="http://{{$hfcBaseConf->video_encoder}}/jpg/1/image.jpg?timestamp=1584552846920"> -->
        @DivClose()
    @DivClose()
@DivClose()

@stop



@section('javascript')
<style type="text/css">
    .rkm-button {
        flex: 1;
        max-width: 6em;
    }

    .controlling-container {
        justify-content: space-between;
        /*flex-wrap: wrap;*/
    }

    #line-nr {
        max-width: 4em;
    }

    #setLineAuto {
        background-color: rgb(114, 124, 182);
    }

    .ajaxResponse {
        width: max-content;
        min-width: 7em;
        text-align: center;
    }
</style>

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

    /**
     * Change cluster/line to show live US picture
     */
    function setLine(auto = null)
    {
        var line = auto ? auto : $('#line-nr').val();

        if (! line) {
            return;
        }

        removeReturnMsg('switchStateResponse');

        $.ajax({
            type: 'post',
            url: '{{ "/admin/NetElement/switchVideoLine" }}',
            data: {
                _token: "{{\Session::get('_token')}}",
                line: line,
            },
            error: function(data, status) {
                $("#setLineResponse").html(data + ': ' + status);
            },
            success: function(data) {
                showReturnMsg('setLineResponse', data);
            }
        });
    }

    function showReturnMsg(domElementId, data)
    {
        console.log(domElementId + ': ' + data);

        $('#' + domElementId).html(data);

        if (! $('#' + domElementId).hasClass('alert')) {
            $('#' + domElementId).addClass('alert');
        }

        if (data == 'OK') {
            $('#' + domElementId).removeClass('alert-danger');
            $('#' + domElementId).addClass('alert-success');
        } else {
            $('#' + domElementId).removeClass('alert-success');
            $('#' + domElementId).addClass('alert-danger');
        }
    }

    function removeReturnMsg(domElementId)
    {
        $('#' + domElementId).html("");
        $('#' + domElementId).removeClass('alert');
    }

    // For reference: well formatted extract of Javascript of CSE-WEB - RCC70 Web-Server frontend
    // as info what answers/return codes of set line mean
    // function handleAjaxAnswer(ans)
    // {
    //     if (! ans)
    //         return;

    //     if (ans.substring(0,5)=="error") {
    //         var val=ans.split('-');

    //         alert("Fehler!\nCSE7 antwortet nicht!\nFehlercode: "+val[1]);
    //         return;
    //     }

    //     var resp=ans.split(':');
    //     var target=resp[0];
    //     var val=resp[1].split('=');

    //     if (target<0 || target>2) {
    //         alert("response error: invalid target ("+target+")");return;
    //     }

    //     switch (val[0]) {
    //         case "0x01"/*setline*/:
    //             if(val[1]==187){
    //                 cse_getLine();
    //             }
    //             break;
    //         case "0x02"/*getline*/:
    //             tmp=val[1];
    //             if(tmp==0){
    //                 alert("Alle Leitungen sind aus!");ledOff();
    //             } else {
    //                 if (tmp>0&&tmp<71) {
    //                     setVal("number",val[1]);cse_getAutoUml();
    //                 }
    //             }
    //             break;
    //         case "0x06"/*gettime*/:tmp=val[1];
    //             if(tmp>1&&tmp<181)
    //                 {setVal("UmlZeit",tmp);
    //                 alert("Umlaufzeit: "+tmp+" Sekunden")
    //             } else {
    //                 alert("Ungültige Umlaufzeit empfangen!\n"+tmp)
    //             }; break;
    //         case "0x07"/*auto_get*/:tmp=val[1];if(tmp>0){ledOn();}else{ledOff();}break;
    //         case "0xfe"/*all-off*/:if(val[1]==187){ledOff();}else{ledOn();}/*cse_getLine()*/;break;
    //         case "0xff"/*auto_on*/:if(val[1]==187){ledOn();}else{ledOff();}break;
    //     }
    // }

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
