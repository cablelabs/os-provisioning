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
                @include('provvoip::widgets.documentation')
            @DivClose()
        </div>
    </div>

@stop
