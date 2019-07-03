{{--

@param $form_fields: the form fields to be displayed as array()
@param $save_button_name: the save button text

--}}

<?php
    $blade_type = 'form';
    $button_title = $save_button_title_key ? trans('messages.'.$save_button_title_key) : '';
    $second_button_title = $second_button_title_key ? trans('messages.'.$second_button_title_key) : '';
    $third_button_title = $third_button_title_key ? trans('messages.'.$third_button_title_key) : '';
?>

{{-- Error Message --}}
<?php $col = \Acme\Html\FormBuilder::get_layout_form_col_md()['label'] ?>

<div id='top_message' class="note note-{{ Session::get('message_color')}} fade in m-b-15" style="display:none;">
    <h5>{{ Session::get('message') }}</h5>
</div>


@include('Generic.above_infos')

@foreach($form_fields as $fields)
    {!! $fields['html'] !!}
@endforeach

@if(Bouncer::can($action, $model_name) || Bouncer::can($action, $view_var))
    <div class="row d-flex justify-content-center">
    @if ($edit_view_save_button)
        @if ($edit_view_second_button)
        <div class='col-4'>
        @endif
        <div class="text-center">
            <button type="submit" class="btn btn-primary m-r-5 m-t-15" style="simple" name="_save" value="1" title="{{ $button_title }}">
                <i class="fa fa-save fa-lg m-r-10" aria-hidden="true"></i>
                {{ \App\Http\Controllers\BaseViewController::translate_view($save_button_name , 'Button') }}
            </button>
        </div>
        @if ($edit_view_second_button)
        </div>
        @endif
    @endif
    @if ($edit_view_second_button)
        @if ($edit_view_save_button)
        <div class='col-4'>
        @endif
        <div class="text-center">
            <button type="submit" class="btn btn-primary m-r-5 m-t-15" style="simple" name="_2nd_action" value="1" title="{{ $second_button_title }}">
                <i class="fa fa-refresh fa-lg m-r-10" aria-hidden="true"></i>
                {{ \App\Http\Controllers\BaseViewController::translate_view($second_button_name , 'Button') }}
            </button>
        </div>
        @if ($edit_view_save_button)
        </div>
        @endif
    @endif
    @if ($edit_view_third_button)
        @if ($edit_view_save_button)
        <div class='col-4'>
        @endif
        <div class="text-center">
            <button type="submit" class="btn btn-primary m-r-5 m-t-15" style="simple" name="_3rd_action" value="1" title="{{ $third_button_title }}">
                <i class="fa fa-refresh fa-lg m-r-10" aria-hidden="true"></i>
                {{ \App\Http\Controllers\BaseViewController::translate_view($third_button_name , 'Button') }}
            </button>
        </div>
        @if ($edit_view_save_button)
        </div>
        @endif
    @endif
    </div>
@endif
{{-- javascript--}}
@include('Generic.form-js')
