{{--

@param $form_fields: the form fields to be displayed as array()
@param $save_button: the save button title

--}}

{{-- Error Message --}}
<?php $col = \Acme\Html\FormBuilder::get_layout_form_col_md()['label'] ?>
@DivOpen(12)
	@DivOpen($col)	@DivClose()
	@DivOpen(12-$col)
		<h5 style='color:{{ Session::get('message_color') }}' id='success_msg'>{{ Session::get('message') }}</h5>
	@DivClose()
@DivClose()


@foreach($form_fields as $fields)
	{{ $fields['html'] }}
@endforeach

@if ($edit_view_save_button)
	{{ Form::submit(trans('view.Button_'.$save_button)) }}
@endif


{{-- java script--}}
@include('Generic.form-js')