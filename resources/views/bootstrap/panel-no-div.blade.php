		<div class="panel panel-inverse card-3" data-sortable-id="table-index-{{ isset($i) ? $i : '1'}}">
			@include ('bootstrap.panel-header', ['view_header' => $view_header])

			<div class="panel-body fader">
				@yield($content)
			</div>
		</div>
