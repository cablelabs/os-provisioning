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
		@if ($form_fields['list'])
		<div class="col-md-12 row" style="padding-right: 0px;"><div class="col-md-12 well row">
		@foreach ($form_fields['list'] as $field)
			<div class="col-md-6">
			{{ $field }}
			</div>
		@endforeach
		</div></div>
		@endif


		{{-- FRAMES --}}
		@if ($form_fields['frame']['linear'])
			<?php
				switch (count($form_fields['frame']['linear'])) {
					case 1:
						$col_width = 12; break;
					case 2:
					case 4:
						$col_width = 6; break;
					default:
						$col_width = 4; break;
				}
			?>
			<div class="col-md-12 row" style="padding-right: 0px;">
			@foreach ($form_fields['frame']['linear'] as $frame)
				<div class="col-md-{{$col_width}} well">
					@foreach ($frame as $field)
						{{ $field }}
					@endforeach
				</div>
			@endforeach
			</div>
		@endif

		@foreach ($form_fields['frame']['tabular'] as $row)
			<div class="col-md-12 row" style="padding-right: 0px;">
				<?php $col_width = (int) (12 / count($row)) ?>
				@foreach ($row as $col)
					<div class="col-md-{{$col_width}} well">
						@foreach ($col as $field)
							{{ $field }}
						@endforeach
					</div>
				@endforeach
			</div>
		@endforeach


		{{-- TABLES --}}
		@foreach ($form_fields['table'] as $table)
			<table class="table controllingtable table-condensed table-bordered d-table" id="datatable">
				<thead>
						<th style="padding: 4px"> Index </th>
					@foreach ($table['head'] as $oid => $head)
						<th align="center" style="padding: 4px">{{$head}}</th>
					@endforeach
				</thead>
				<tbody>
					@foreach ($table['body'] as $index => $row)
						<tr>
							<?php $index = str_replace('.', '', $index) ?>
							<td> {{ isset($table['3rd_dim']) ? HTML::linkRoute('NetElement.controlling_edit', $index, [$table['3rd_dim']['netelement_id'], $table['3rd_dim']['param_id'], $index]) : $index }} </td>
							@foreach ($row as $col)
								<td align="center" style="padding: 4px"> {{ $col }} </td>
							@endforeach
						</tr>
					@endforeach
				</tbody>
			</table>
		@endforeach


	{{-- Save Button --}}
	<div class="d-flex justify-content-center">
			<input class="btn btn-primary" style="simple" value="{{\App\Http\Controllers\BaseViewController::translate_view($save_button , 'Button') }}" type="submit">
	</div>

	{{ Form::close() }}

	{{-- java script --}}
	@include('Generic.form-js')


@stop

@section('javascript_extra')
{{-- JS DATATABLE CONFIG --}}
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
