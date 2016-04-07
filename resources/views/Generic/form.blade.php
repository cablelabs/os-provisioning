<script>setTimeout("document.getElementById('success_msg').style.display='none';", 6000);</script>

	<h4 id='success_msg'>{{ Session::get('message') }}</h4>

	@foreach($form_fields as $fields)
		{{ $fields['html'] }}
	@endforeach

	{{ Form::submit($save_button) }}
