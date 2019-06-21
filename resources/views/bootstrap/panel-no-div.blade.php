		<div class="panel panel-inverse card-3" data-sort-id="{{ $tab['name'] . '-' . $view }}">
			@include ('bootstrap.panel-header', ['view_header' => $view_header])

			<div class="panel-body fader">
				@yield($content)
			</div>
		</div>
