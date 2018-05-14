@if (isset($relation['view']['vars']))
	<div class="row">
		@if ($rerun_button)
			<div class="col-12 text-center m-b-20">
				{{ Form::open(array('route' => ['SettlementRun.update', $view_var->id,] ,'method' => 'put')) }}
					{{ Form::hidden('rerun', true) }}
					{{ Form::submit( \App\Http\Controllers\BaseViewController::translate_view('Rerun Accounting Command for current Month', 'Button') , ['style' => 'simple']) }}
				{{ Form::close() }}
			</div>
		@endif

		@if (\Session::get('job_id'))
			{{-- accountingCommand running --}}
			<div class="alert alert-warning fade in m-b-15">{{ trans('messages.accCmd_processing') }}</div>
			<div id="state" class="col-12"></div>
		@else
			@foreach($relation['view']['vars'] as $sepaacc => $files)
				@DivOpen(6)
					<table class="table table-bordered">
					<th class="text-center active"> {{ $sepaacc }} </th>
					@foreach ($files as $key => $file)
						<tr><td class="text-center">{{ HTML::linkRoute('Settlement.download', $file->getFilename(), ['id' => $view_var->id, 'sepaacc' => $sepaacc, 'key' => $key]) }}</td></tr>
					@endforeach
					</table>
				@DivClose()
			@endforeach
		@endif

	</div>
@endif


@section ('javascript_extra')

	@if (\Session::get('job_id'))
		<script type="text/javascript">

			$(document).ready(function()
			{
				setTimeout(function()
				{
					var source = new EventSource("<?php echo route('SettlementRun.check_state'); ?>");
					source.onmessage = function(e)
					{
						if (e.data == 'reload')
							location.reload();
						else
							document.getElementById('state').innerHTML = e.data;
					}

				}, 500);
			});
		</script>
	@endif

@stop
