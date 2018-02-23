<?php
	$style = 'height:100%';
	if (isset($height))
		$style = ($height == 'auto') ? '' : "height:$height%";

	$overflow_y = isset($overflow) ? $overflow : 'auto';
?>

{{-- begin col-dyn --}}
<!-- <div class="col-md-{{$md}} ui-sortable"> -->
<div class="col-md-{{$md}}">
	<!-- <div class="panel panel-inverse card-2" data-sortable-id="table-index-{{ isset($i) ? $i : '1'}}"> -->
	<div class="panel panel-inverse card-2">
		@include ('bootstrap.panel-header', ['view_header' => $view_header])
		<div class="panel-body fader" style="overflow-y:{{ $overflow_y }}; {{ $style }}">
			@yield($content)
		</div>
	</div>
</div>
