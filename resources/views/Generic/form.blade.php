{{--

@param $form_fields: the form fields to be displayed as array()
@param $save_button: the save button title

--}}

<?php
	$blade_type = 'form';
?>

{{-- Error Message --}}
<?php $col = \Acme\Html\FormBuilder::get_layout_form_col_md()['label'] ?>
@DivOpen(12)
	@DivOpen($col)	@DivClose()
	@DivOpen(12-$col)
		<h5 style='color:{{ Session::get('message_color') }}' id='success_msg'>{{ Session::get('message') }}</h5>
	@DivClose()
@DivClose()


@include('Generic.above_infos')

@foreach($form_fields as $fields)
	{{ $fields['html'] }}
@endforeach

@if ($edit_view_save_button)
	@if ($edit_view_force_restart_button)
	<div class='col-md-5'>
	@endif
	<div class="text-center">
		<button type="submit" class="btn btn-primary m-r-5 m-t-15" style="simple" name="_save" value="1">
			<i class="fa fa-save fa-lg m-r-10" aria-hidden="true"></i>
			{{ \App\Http\Controllers\BaseViewController::translate_view($save_button , 'Button') }}
		</button>
	</div>
	@if ($edit_view_force_restart_button)
	</div>
	@endif
@endif
@if ($edit_view_force_restart_button)
	@if ($edit_view_save_button)
	<div class='col-md-6'>
	@endif
	<div class="text-center">
		<button type="submit" class="btn btn-primary m-r-5 m-t-15" style="simple" name="_force_restart" value="1">
			<i class="fa fa-refresh fa-lg m-r-10" aria-hidden="true"></i>
			{{ \App\Http\Controllers\BaseViewController::translate_view($force_restart_button , 'Button') }}
		</button>
	</div>
	@if ($edit_view_save_button)
	</div>
	@endif
@endif


{{-- java script--}}
@include('Generic.form-js')
