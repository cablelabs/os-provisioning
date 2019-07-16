{!! Form::open(['route' => ['NetElementType.settings', $view_var->id], 'method' => 'post']) !!}

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
