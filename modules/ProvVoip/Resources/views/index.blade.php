@extends ('Layout.default')

@section('content')

    <div class="col-md-12">

        <h1 class="page-header">{{ $title }}</h1>

        {{--Quickstart--}}

        <div class="row">
            @DivOpen(3)
                @include('provvoip::widgets.quickstart')
            @DivClose()

            @DivOpen(5)
                @include('Generic.widgets.moduleDocu', [ 'urls' => [
                        'documentation' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/VoIP',
                        'youtube' => 'https://youtu.be/SxTsflcNeUQ',
                        'forum' => 'https://devel.roetzer-engineering.com/confluence/display/nmsprimeforum/VoIP',
                    ]])
            @DivClose()
        </div>
    </div>

@stop
