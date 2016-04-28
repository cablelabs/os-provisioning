{{--

@param $form_fields: the form fields to be displayed as array()
@param $save_button: the save button title

--}}


<h4 id='success_msg'>{{ Session::get('message') }}</h4>

@foreach($form_fields as $fields)
	{{ $fields['html'] }}
@endforeach

{{ Form::submit($save_button) }}


{{-- java script--}}
@include('Generic.form-js')