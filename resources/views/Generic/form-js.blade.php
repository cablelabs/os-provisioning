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

Java Edit Form Blade
NOTE: - java include section is in default blade at bottom of text
      - used to add java script stuff to form/edit blade

@param $form_fields: the form fields to be displayed as array()

--}}

@section ('form-javascript')

    <script type="text/javascript">
        if ($('#top_message').is(".note-primary, .note-success, .note-warning, .note-danger, .note-info")){
            $('#top_message').show();
            setTimeout(function() { $('#top_message').fadeOut(); }, 6000);
        }
    </script>
    <script type="text/javascript">
        setTimeout(function() { $('#delete_msg').fadeOut();}, 6000);
    </script>


    <script type="text/javascript">

        {{-- jquery (java script) based realtime showing/hiding of fields --}}
        {{-- foreach form field --}}
        @if(isset ($form_fields))
            @foreach($form_fields as $field)

                {{-- that has a select field with an array() inside --}}
                @if (isset($field['select']) && is_array($field['select']))

                    {{-- load on document initialization --}}
                    @include('Generic.form-js-select')

                    {{-- the element change function --}}
                    $('#{{$field['name']}}').change(function() {
                        @include('Generic.form-js-select')
                    });

                @endif

                {{-- current element is a checkbox --}}
                @if (array_key_exists('form_type', $field) && $field['form_type'] == 'checkbox')

                    @if (!isset($form_js_checkbox_blade_included))
                        @include('Generic.form-js-checkbox')
                        <?php $form_js_checkbox_blade_included = True; ?>
                    @endif

                    {{-- call onLoad to initialize the websites visibility states --}}
                    par__toggle_class_visibility_depending_on_checkbox('{{$field['name']}}');

                    {{-- call on change of a checkbox --}}
                    $('#{{$field['name']}}').change(function() {
                        par__toggle_class_visibility_depending_on_checkbox('{{$field['name']}}');
                    });

                @endif

                @if (array_key_exists('autocomplete', $field) && count($field['autocomplete']) === 2)

                    $(document).ready(function() {
                        $('{{'#'.$field["name"]}}').autocomplete({
                            source:function (data, response) {
                                $.ajax({
                                    url:'/admin/{!! reset($field["autocomplete"]) !!}/autocomplete/{!! end($field["autocomplete"]) !!}?q=' + $('#{!! $field["name"] !!}').val(),
                                    success:function(data){
                                        response(data);
                                    }
                                })
                            }
                        });
                    });

                @endif

            @endforeach
        @endif

        @include('Generic.form-js-fill-input-from-href')

    </script>
@stop
