<?php
    $link = '#';
    $noHover = 'noHover';
    if (isset($link_target) && $link_target != '#') {
    	$link = $link_target;
        $noHover = '';
    }
?>

<div class="widget widget-stats bg-{{ $widget_bg_color ?? 'white' }}">
    <div class="stats-icon">
        <i class="fa fa-{{ $widget_icon ?? 'bars' }}"></i>
    </div>

    <div class="stats-info">
        @if (isset($title) && isset($value))
            <h4>{{ $title }}</h4>
            <p>{{ $value }}</p>
        @endif
        @if (isset($content))
            @yield($content)
        @endif
    </div>

    {{-- reference link --}}
    <div class="stats-link {{$noHover}}">
        <a href="{{ $link }}">
            @if($link != '#')
                {!! \App\Http\Controllers\BaseViewController::translate_view('LinkDetails', 'Dashboard') !!} <i class="fa fa-arrow-circle-o-right"></i>
            @else
                &nbsp;
            @endif
        </a>
    </div>
</div>
