@if (isset($apiActions) && !is_null($apiActions))

    @foreach($apiActions as $apiAction)

        <a href="{!! $apiAction['url'] !!}" target="_self">{!! $apiAction['linktext'] !!}</a><br>

    @endforeach

@endif
