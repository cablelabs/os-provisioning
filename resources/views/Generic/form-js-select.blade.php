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

Java Edit Form Blade - select function internals.
This blade is used for jquery (java script) realtime based showing/hiding of fields

NOTE: will be used from form-js blade and must be called inside java context

@param $field: the form field that has a valid select entry with type array

--}}

<?php
    // generate hide all variable, to hide all classes
    $classes = [];
    foreach ($field['select'] as $class) {
        $classes[] = ".$class";
    }

    $classes = implode(', ', $classes);
    $hide_all = "$('$classes').hide();";
?>

{!! $hide_all !!}

{{-- handle select fields --}}
@if ($field['form_type'] == 'select')
    @foreach($field['select'] as $val => $hide)
        <?php $val = is_string($val) ? $val = '"'.$val.'"' : $val; ?>
        if ($('#{!! $field['name'] !!}').val() == {!! $val !!}) {
            $(".{{$hide}}").show();
        }
    @endforeach
@endif
