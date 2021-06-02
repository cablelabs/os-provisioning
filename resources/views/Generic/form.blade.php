<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
{{--

@param $form_fields: the form fields to be displayed as array()
@param $save_button_name: the save button text

--}}

<?php
    $blade_type = 'form';
    $button_title = $save_button_title_key ? trans('messages.'.$save_button_title_key) : '';
    $second_button_title = $second_button_title_key ? trans('messages.'.$second_button_title_key) : '';
    $second_button_icon = $second_button_icon ?? 'refresh';
    $third_button_title = $third_button_title_key ? trans('messages.'.$third_button_title_key) : '';
    $third_button_icon = $third_button_icon ?? 'refresh';
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
    <div class="row d-flex justify-content-center d-print-none">
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
                <i class="fa fa-{{ $second_button_icon }} fa-lg m-r-10" aria-hidden="true"></i>
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
                <i class="fa fa-{{ $third_button_icon }} fa-lg m-r-10" aria-hidden="true"></i>
                {{ \App\Http\Controllers\BaseViewController::translate_view($third_button_name , 'Button') }}
            </button>
        </div>
        @if ($edit_view_save_button)
        </div>
        @endif
    @endif
    @if ($printButton)
        @if ($edit_view_save_button)
        <div class='col-4'>
        @endif
        <div class="text-center">
            <a href="javascript: window.print();" class="btn btn-primary m-r-5 m-t-15 text-white" title="Print this Page">
                <i class="fa fa-print fa-lg m-r-10" aria-hidden="true"></i>
                {{ trans('view.jQuery_Print') }}
            </a>
        </div>
        @if ($edit_view_save_button)
        </div>
        @endif
    @endif
    </div>
@endif
{{-- javascript--}}
@include('Generic.form-js')
