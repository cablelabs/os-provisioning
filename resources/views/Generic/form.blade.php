{{--

@param $form_fields: the form fields to be displayed as array()
@param $save_button_name: the save button text

--}}

<?php
	$blade_type = 'form';
	$button_title = $save_button_title_key ? 'title="'.trans('messages.'.$save_button_title_key).'"' : '';
	$second_button_title = $second_button_title_key ? 'title="'.trans('messages.'.$second_button_title_key).'"' : '';
?>

{{-- Error Message --}}
<?php $col = \Acme\Html\FormBuilder::get_layout_form_col_md()['label'] ?>

<div id='top_message' class="note note-{{ Session::get('message_color')}} fade in m-b-15" style="display:none;">
	<strong><h5>{{ Session::get('message') }}</h5></strong>
</div>


@include('Generic.above_infos')

@foreach($form_fields as $fields)
	{!! $fields['html'] !!}
@endforeach

@can($action, $model_name )
	<div class="row d-flex justify-content-center">
	@if ($edit_view_save_button)
		@if ($edit_view_second_button)
		<div class='col-6'>
		@endif
		<div class="text-center">
			<button type="submit" class="btn btn-primary m-r-5 m-t-15" style="simple" name="_save" value="1" {{ $button_title }}>
				<i class="fa fa-save fa-lg m-r-10" aria-hidden="true"></i>
				{{ \App\Http\Controllers\BaseViewController::translate_view($save_button_name , 'Button') }}
			</button>
		</div>
		@if ($edit_view_second_button)
		</div>
		@endif
	@endif
	@if ($edit_view_second_button)
		@if ($edit_view_save_button)
		<div class='col-6'>
		@endif
		<div class="text-center">
			<button type="submit" class="btn btn-primary m-r-5 m-t-15" style="simple" name="_2nd_action" value="1" {{ $second_button_title }}>
				<i class="fa fa-refresh fa-lg m-r-10" aria-hidden="true"></i>
				{{ \App\Http\Controllers\BaseViewController::translate_view($second_button_name , 'Button') }}
			</button>
		</div>
		@if ($edit_view_save_button)
		</div>
		@endif
	@endif
	</div>
@endcan
{{-- javascript--}}
@include('Generic.form-js')
