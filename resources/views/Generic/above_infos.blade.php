<?php $tmp_msg_above_keys = [
    'tmp_success_above_form',
    'tmp_info_above_form',
    'tmp_warning_above_form',
    'tmp_error_above_form',

    'tmp_success_above_index_list',
    'tmp_info_above_index_list',
    'tmp_warning_above_index_list',
    'tmp_error_above_index_list',

    'tmp_success_above_relations',
    'tmp_info_above_relations',
    'tmp_warning_above_relations',
    'tmp_error_above_relations',
    ];

$tmp_msg_above_shown = False;

$tmp_msg_above = [];
foreach ($tmp_msg_above_keys as $tmp_msg_above_key) {
    if ((Session::has($tmp_msg_above_key)) && (Str::endswith($tmp_msg_above_key, $blade_type))) {
        $tmp_msg_above[$tmp_msg_above_key] = Session::get($tmp_msg_above_key);
    }
}
?>

@foreach ($tmp_msg_above as $tmp_msg_above_key => $tmp_msg_above_msg)
    @DivOpen(12)
    <?php

        // for better handling: transform strings to array (containing one element)
        if (is_string($tmp_msg_above_msg)) {
            $tmp_msg_above_msg = [$tmp_msg_above_msg];
        };
        $tmp_msg_above_msg = array_unique($tmp_msg_above_msg);

        // set color depending on message type
        $tmp_type = explode('_', $tmp_msg_above_key)[1];
        if ($tmp_type == 'info') {
            $tmp_color = '#aaaaff';
        }
        elseif ($tmp_type == 'success') {
            $tmp_color = '#aaffaa';
        }
        elseif ($tmp_type == 'warning') {
            $tmp_color = '#ffcc44';
        }
        elseif ($tmp_type == 'error') {
            $tmp_color = '#ffaaaa';
        }
        else {
            $tmp_color = '#aaaaaa';
        }
        $tmp_style = "font-weight: bold; padding-top: 0px; padding-left: 10px; margin-bottom: 5px; border-left: solid 2px $tmp_color";
        $tmp_msg_above_shown = True;
    ?>
    @foreach ($tmp_msg_above_msg as $tmp_msg)
        <div style="{!! $tmp_style !!}">
            {!! $tmp_msg !!}
        </div>
    @endforeach
    <?php
        // as this shall not be shown on later screens: remove from session
        // we could use Session::flash for this behavior – but this supports no arrays…
        Session::forget($tmp_msg_above_key);
    ?>
    @DivClose()
@endforeach

@if ($tmp_msg_above)
    @DivOpen(12)
        &nbsp;
    @DivClose()
@endif
