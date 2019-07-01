@extends ('Layout.split')


@section('content_top')
    {!! $headline !!}
@stop


@section('content_left')

@stop

@section('content_right')

    @section('content_right_e')

        {!! Form::open(['route' => ['NetElementType.settings', $view_var->id], 'method' => 'post']) !!}

            @php
                $list = Modules\HfcReq\Entities\NetElementType::param_list($view_var->id);
            @endphp


            <div class="col-md-12">
            <div class="form-group row">
            {!! Form::label('param_id', 'Choose Parameter') !!}
            {!! Form::select('param_id[]', $list, null , ['multiple' => 'multiple']) !!}
            <br><br><br>
            </div></div>

            <div class="col-md-12">
            <div class="form-group row">
            {!! Form::label('html_frame', 'HTML Frame ID') !!}
            {!! Form::text('html_frame') !!}
            </div></div>

            <div class="col-md-12">
            <div class="form-group row">
            {!! Form::label('html_id', 'Order ID') !!}
            {!! Form::text('html_id') !!}
            </div></div>


            {!! Form::submit('Set Value(s)') !!}

        {!! Form::close() !!}

    @stop
    @include ('bootstrap.panel', array ('content' => "content_right_e", 'view_header' => 'Set Parameter', 'md' => 7))
@stop
