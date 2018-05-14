<div class="table-responsive">
	<table class="table streamtable table-bordered" width="100%">
	<thead>
		<tr class="active">
			<th></th>
			<th>Time</th>
			<th>Type</th>
			<th>Message</th>
		</tr>
	</thead>
	<tbody>
		<div class="m-b-20" align='right'>
			{{ Form::open(array('route' => ['SettlementRun.log_dl', $view_var->id], 'method' => 'get')) }}
				{{ Form::submit(trans('view.sr_dl_logs') , ['style' => 'simple']) }}
			{{ Form::close() }}
		@DivClose()

		@if (isset($logs))
			@foreach($logs as $row)
				<tr class="{{ $row['color'] }}">
					<td></td>
					<?php unset($row['color']) ?>

					@foreach($row as $cell)
						<td>{{ $cell }}</td>
					@endforeach
				</tr>
			@endforeach
		@endif
	</tbody>
	</table>
</div>
