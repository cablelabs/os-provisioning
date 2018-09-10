        {{--Help Section--}}
    <div class="align-self-end m-r-20 dropdown btn-group">
        <button type="button" class="btn btn-outline m-b-10 float-right dropdown-toggle" style="simple" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-toggle="tooltip" data-delay='{"show":"250"}' data-placement="top"
            title="{{ \App\Http\Controllers\BaseViewController::translate_view('Delete', 'Button' ) }}" form="IndexForm" name="_delete">
            <i class="fa fa-question fa-2x" aria-hidden="true"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
            <?php
                if (isset($documentation))
                    $help = config('documentation.'.strtolower($documentation));
                else
                    $help = $view_help;
            ?>

            @if ($help['doc'])
            <a class="dropdown-item" href={{$help['doc']}} target="_blank">Documentation</a>
            @endif
            @if ($help['url'])
            <a class="dropdown-item" href={{$help['url']}} target="_blank">URL</a>
            @endif
            @if ($help['youtube'])
            <a class="dropdown-item" href={{$help['youtube']}} target="_blank">Youtube</a>
            @endif
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="mailto:support@roetzer-engineering.com">Request Professional Help</a>
        </div>
    </div>
