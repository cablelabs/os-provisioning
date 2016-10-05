{{--

This blade is used for jquery (java script) realtime based showing/hiding of fields depending on checkbox state.

NOTE: will be used from form-js blade and must be called inside javascript context

@param $field: the form field that has a valid select entry with type array

--}}

if ($('#{{$field['name']}}').prop('checked')) {
	$('.show_on_{{$field['name']}}').show();
	$('.hide_on_{{$field['name']}}').hide();
}
else {
	$('.show_on_{{$field['name']}}').hide();
	$('.hide_on_{{$field['name']}}').show();
};
