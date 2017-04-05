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


{{-- man can use the session key “tmp_info_above_form” to show additional data above the form for one screen --}}
{{-- simply use Session::push('tmp_info_above_form', 'your additional data') in your observers or where you want --}}
@if (Session::has('tmp_info_above_form'))
	@DivOpen(12)
	<?php
		$tmp_info_above_form = Session::get('tmp_info_above_form');

		// for better handling: transform strings to array (containing one element)
		if (is_string($tmp_info_above_form)) {
			$tmp_info_above_form = [$tmp_info_above_form];
		};
	?>
	@foreach($tmp_info_above_form as $info)
		<div style="font-weight: bold; padding-top: 0px; padding-left: 10px; margin-bottom: 5px; border-left: solid 2px #ffaaaa">
			{{ $info }}
		</div>
	@endforeach
	<br>
	<?php
		// as this shall not be shown on later screens: remove from session
		// we could use Session::flash for this behavior – but this supports no arrays…
		Session::forget('tmp_info_above_form'); ?>
	@DivClose()
@endif


@foreach($form_fields as $fields)
	{{ $fields['html'] }}
@endforeach

@if ($edit_view_save_button)
	@if ($edit_view_force_restart_button)
	<div class='col-md-5'>
	@endif
	{{ Form::submit( \App\Http\Controllers\BaseViewController::translate_view($save_button , 'Button'), ['name' => '_save']) }}
	@if ($edit_view_force_restart_button)
	</div>
	@endif
@endif
@if ($edit_view_force_restart_button)
	@if ($edit_view_save_button)
	<div class='col-md-6'>
	@endif
	{{ Form::submit( \App\Http\Controllers\BaseViewController::translate_view($force_restart_button , 'Button'), ['name' => '_force_restart']) }}
	@if ($edit_view_save_button)
	</div>
	@endif
@endif


{{-- java script--}}
@include('Generic.form-js')
