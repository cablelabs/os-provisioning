{{--

@param $form_fields: the form fields to be displayed as array()
@param $save_button: the save button title

--}}

<script>setTimeout("document.getElementById('success_msg').style.display='none';", 6000);</script>

<script type='text/javascript'>
	/*
	 * jQuery Plugin for "on Hover" Mouse Events:
	 * use title option in html element,
	 * see help msg on BaseViewController->html_form_fields()
	 */
	$(function() {
		$( document ).tooltip();
	});
</script>


	<h4 id='success_msg'>{{ Session::get('message') }}</h4>

	@foreach($form_fields as $fields)
		{{ $fields['html'] }}
	@endforeach

	{{ Form::submit($save_button) }}
