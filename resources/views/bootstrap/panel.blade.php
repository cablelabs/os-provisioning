<?php
    $style = 'height:100%';
    if (isset($height))
        $style = ($height == 'auto') ? '' : "height:$height%";

    $overflow_y = isset($overflow) ? $overflow : 'auto';

    $display = isset($options['display']) ? 'display: '.$options['display'] : '';

    $dataSortId = isset($tab) ? $tab['name'].'-'.$view : ($i ?? 1);
    $attrExt = isset($handlePanelPosBy) && $handlePanelPosBy == 'nmsprime' ? '' : 'able';
?>

{{-- begin col-dyn --}}
@if(isset($md))
<div class="col-{{ $md }}">
@endif
    <div class="panel panel-inverse card-2" data-sort{{$attrExt}}-id="{{ $dataSortId }}">
        @include ('bootstrap.panel-header', ['view_header' => $view_header])
        <div class="panel-body fader" style="overflow-y:{{ $overflow_y }}; {{ $style }}; {{ $display }}">
            @yield($content)
        </div>
    </div>
@if(isset($md))
</div>
@endif
