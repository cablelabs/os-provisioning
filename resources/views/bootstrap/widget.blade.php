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

    <div class="stats-link">
        <a href="{{ $link_target ?? 'javascript:;' }}">
            {{ $linkText ?? App\Http\Controllers\BaseViewController::translate_view('LinkDetails', 'Dashboard') }}
            @if(isset($link_target))
                <i class="fa fa-arrow-circle-right"></i>
            @endif
        </a>
    </div>
</div>
