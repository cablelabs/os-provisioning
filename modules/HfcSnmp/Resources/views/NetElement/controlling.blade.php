@extends ('Layout.split-nopanel')


@section('content_top')

	@if ($reload)
		{{-- Seconds to refresh the page --}}
		<META HTTP-EQUIV="refresh" CONTENT="{{$reload}}">
	@endif

	{{ $headline }}

@stop



@section ('content_left')

	@include ('Generic.logging')


	{{ Form::model($view_var, array('route' => array($form_update, $view_var->id, $param_id, $index), 'method' => 'put', 'files' => true)) }}

		{{-- Error | Success Message --}}
		@if (Session::has('message'))
			@DivOpen(12)
				@if (Session::get('message_color') == 'primary')
					@DivOpen(5) @DivClose()
					@DivOpen(4)
				@endif
				<h4 style='color:{{ Session::get('message_color') }}' id='success_msg'>{{ Session::get('message') }}</h4>
				@if (Session::get('message_color') == 'primary')
					@DivClose()
				@endif
			@DivClose()
		@endif

		{{-- LIST --}}
		@foreach ($form_fields['list'] as $field)
			{{ $field }}
		@endforeach

		{{-- FRAMES --}}
		@foreach ($form_fields['frame'] as $order)
			@foreach ($order as $row)

				<div class="col-md-12" style="padding-right: 0px; padding-left: 0px;">
				@foreach ($row as $col)

					<?php
						$col_width = (int) (12 / count($row));
					?>

					<div class="col-md-{{$col_width}} well" style="padding-right: 0px; padding-left: 0px;">

						@foreach ($col as $field)
							{{ $field }}
						@endforeach

					</div>

				@endforeach
				</div>

			@endforeach
		@endforeach

		{{-- TABLE --}}
		@foreach ($form_fields['table'] as $table)
			{{ $table }}
		@endforeach

	{{-- Save Button --}}
	<div class="d-flex justify-content-center">
			<input class="btn btn-primary" style="simple" value="{{\App\Http\Controllers\BaseViewController::translate_view($save_button , 'Button') }}" type="submit">
	</div>

	{{ Form::close() }}

	{{-- java script--}}
	@include('Generic.form-js')


@stop

@section('javascript_extra')
{{-- JS DATATABLE CONFIG --}}
<!-- Hallo Tada - das ist der Test ob das ganze überhaupt ankommt-->
<script language="javascript">
	var table = $('table.controllingtable').DataTable(
		{
		{{-- Translate Datatables Base --}}
			@include('datatables.lang')
		{{-- Buttons above Datatable for export, print and change Column Visibility --}}
			@include('datatables.buttons')
		iDisplayLength: -1,
		responsive: true,
		autoWidth: false, {{-- Option to ajust Table to Width of container --}}
		dom:	"<'row'<'col-sm-12'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-5'i>>", {{-- sets order and what to show  --}}
		stateSave: true, {{-- Save Search Filters and visible Columns --}}
		fixedHeader: {
			headerOffset: $('#header').outerHeight(),
		},
	});
	window.onresize = function(event) {
		table.responsive.recalc();
	}
</script>

@stop
