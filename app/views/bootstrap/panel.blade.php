	<!-- begin col-8 -->
	<div class="col-md-{{$md}} ui-sortable">
		<div class="panel panel-inverse" data-sortable-id="table-basic-{{$md}}">
			@include ('bootstrap.panel-header')

			<div class="panel-body fader">
				@yield($content)
			</div>
		</div>
	</div>