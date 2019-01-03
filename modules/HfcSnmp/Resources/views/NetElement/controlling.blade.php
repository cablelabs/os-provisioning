@extends ('Layout.split-nopanel')


@section('content_top')

	@if ($reload)
		{{-- Seconds to refresh the page --}}
		<!-- <META HTTP-EQUIV="refresh" CONTENT="{{$reload}}"> -->
	@endif

	{!! $headline !!}

@stop


@section ('content_left')

	@include ('Generic.logging')

	{{-- Stop SSE Button --}}
	@if ($reload)
	<div class="row">
		@DivOpen(10)
			@if ($view_var->controlling_link)
				{!! link_to($view_var->controlling_link, 'View...', ['class' => 'btn btn-primary pull-right']) !!}
			@endif
		@DivClose()
		@DivOpen(2)
			<input id="stop-button" class="btn btn-primary" style="simple" onclick="close_source()" value="Stop updating">
		@DivClose()
	</div>
	@endif

	{{-- Error Message --}}
	<?php $blade_type = 'form' ?>
	@include('Generic.above_infos')

	{{-- PARAMETERS --}}
	@if (isset ($form_fields['list']))
		{!! Form::model($view_var, array('route' => array($form_update, $view_var->id, $param_id, $index), 'method' => 'put', 'files' => true)) !!}

		{{-- LIST --}}
		@if ($form_fields['list'])
		<div class="col-md-12 row" style="padding-right: 0px;"><div class="col-md-12 well row">
		@foreach ($form_fields['list'] as $field)
			<div class="col-md-6">
			{!! $field !!}
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
						{!! $field !!}
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
							{!! $field !!}
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
						<th align="center" style="padding: 4px">{!!$head!!}</th>
					@endforeach
				</thead>
				<tbody>
					@foreach ($table['body'] as $i => $row)
						<tr>
							<?php $i = str_replace('.', '', $i) ?>
							<td> {!! isset($table['3rd_dim']) ? HTML::linkRoute('NetElement.controlling_edit', $i, [$table['3rd_dim']['netelement_id'], $table['3rd_dim']['param_id'], $i]) : $i !!} </td>
							@foreach ($row as $col)
								<td align="center" style="padding: 4px"> {!! $col !!} </td>
							@endforeach
						</tr>
					@endforeach
				</tbody>
			</table>
		@endforeach

	{{-- Save Button --}}
	<div class="d-flex justify-content-center">
		<input
			class="btn btn-primary"
			style="simple"
			value="{!! \App\Http\Controllers\BaseViewController::translate_view($save_button_name , 'Button') !!}"
			type="submit">
	</div>

	{!! Form::close() !!}

@endif

	{{-- javascript --}}
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

	// use SSE for constant updating values as ajax uses more http overhead
	function update_snmp_values()
	{
		$('#stop-button').val('Stop updating');
		$('#stop-button').attr("onclick", "close_source()");

		console.log("Establish SSE connection");
		this.source = new EventSource("{!! route('NetElement.sse_get_snmpvalues', [$view_var->id, $param_id, $index, $reload]) !!}");

		this.source.onmessage = function(e)
		{
			var data = JSON.parse(e.data);

			console.log("Received data");

			for (var key in data)
			{
				if (document.getElementsByName(key)[0] instanceof HTMLInputElement)
					document.getElementsByName(key)[0].value = data[key];
				else
					document.getElementsByName(key)[0].innerHTML = data[key];
			}
		}
	}

	function close_source()
	{
		console.log('Close SSE connection');
		this.source.close();
		// $('#stop-button').remove();
		$('#stop-button').val('Start update again');
		$('#stop-button').attr("onclick", "update_snmp_values()");
	}

	$(document).ready(function()
	{
		if (Number("{{$reload}}"))
			setTimeout(update_snmp_values(), "{{$reload}}" * 1000);
	});

</script>

@stop
