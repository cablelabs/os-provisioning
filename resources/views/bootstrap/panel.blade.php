<?php
	$style = 'height:100%';
	if (isset($height))
		$style = ($height == 'auto') ? '' : "height:$height%";

	$overflow_y = isset($overflow) ? $overflow : 'auto';

	$display = isset($options['display']) ? 'display: '.$options['display'] : '';
?>

{{-- begin col-dyn --}}
@if(isset($md))
<div class="col-{{ $md }}">
@endif
	<div class="panel panel-inverse card-2" data-sort-id="{{ isset($tab) ? $tab['name'] . '-' . $view : 1 }}">
		@include ('bootstrap.panel-header', ['view_header' => $view_header])
		<div class="panel-body fader" style="overflow-y:{{ $overflow_y }}; {{ $style }}; {{ $display }}">
			@yield($content)
		</div>
	</div>
@if(isset($md))
</div>
@endif
