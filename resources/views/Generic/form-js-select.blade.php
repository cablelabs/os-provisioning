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

{{$hide_all}}

// handle select fields
@if ($field['form_type'] == 'select')
	@foreach($field['select'] as $val => $hide)
		if ($('#{{$field['name']}}').val() == {{$val}})
		{
			$(".{{$hide}}").show();
		}
	@endforeach
@endif

// TODO: handle checkbox based selection
@if ($field['form_type'] == 'checkbox')
	@foreach($field['select'] as $val => $hide)
		if( $('#{{$field['name']}}:checked').length == {{$val}} ) {
			$(".{{$hide}}").show();
		} else {
			{{$hide_all}}
		}
	@endforeach
@endif