{{--

Java Edit Form Blade - select function internals.
This blade is used for jquery (java script) realtime based showing/hiding of fields

NOTE: will be used from form-js blade and must be called inside java context

@param $field: the form field that has a valid select entry with type array

--}}

<?php
    // generate hide all variable, to hide all classes
    $hide_all = '';
    foreach ($field['select'] as $select)
        $hide_all .= ' $(".'.$select.'").hide();';
?>

{!! $hide_all !!}

{{-- handle select fields --}}
@if ($field['form_type'] == 'select')
    @foreach($field['select'] as $val => $hide)
        <?php $val = is_string($val) ? $val = '"'.$val.'"' : $val; ?>
        if ($('#{!! $field['name'] !!}').val() == {!! $val !!})
        {
            $(".{{$hide}}").show();
        }
    @endforeach
@endif
