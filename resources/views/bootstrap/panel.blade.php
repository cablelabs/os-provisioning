	<!-- begin col-dyn -->
	<div class="col-md-{{$md}} ui-sortable">
		<div class="panel panel-inverse" data-sortable-id="table-basic-{{$md}}">
			@include ('bootstrap.panel-header', ['view_header' => $view_header])

			<div class="panel-body fader" style="overflow-y:auto; height:100%">
				@yield($content)
			</div>
		</div>
	</div>
	