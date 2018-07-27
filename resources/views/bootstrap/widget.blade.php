<?php
    $icon = 'bars';
    if (isset($widget_icon)) {
    	$icon = $widget_icon;
    }

    $bg_color = 'white';
    if (isset($widget_bg_color)) {
		$bg_color = $widget_bg_color;
    }

    $link = 'javascript:;';
    if (isset($link_target)) {
    	$link = $link_target;
    }
?>

    <div class="widget widget-stats bg-{{ $bg_color }}">
        {{-- icon --}}
        <div class="stats-icon">
            <i class="fa fa-{{ $icon }}"></i>
        </div>

        {{-- info/data --}}
        <div class="stats-info">
            @yield($content)
        </div>

        {{-- reference link --}}
        <div class="stats-link">
            <a href="{{ $link }}">
                @if($link != 'javascript:;')
                    {!! \App\Http\Controllers\BaseViewController::translate_view('LinkDetails', 'Dashboard') !!} <i class="fa fa-arrow-circle-o-right"></i>
                @else
                    &nbsp;
                @endif
            </a>
        </div>
    </div>
