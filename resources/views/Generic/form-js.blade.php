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

            @endforeach
        @endif

        @include('Generic.form-js-fill-input-from-href')

    </script>
@stop
