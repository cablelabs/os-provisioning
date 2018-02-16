<?php
	$style = 'height:100%';
	if (isset($height))
	{
		$style = ($height == 'auto') ? '' : "height:$height%";
	}

	$overflow_y = 'auto';
	if (isset($overflow))
	{
		$overflow_y = $overflow;
	}
?>

{{-- begin col-dyn --}}
	<div class="panel panel-inverse card-2" data-sortable-id="table-index-{{ isset($i) ? $i : '1'}}">
		@include ('bootstrap.panel-header', ['view_header' => $view_header])
		<div class="panel-body fader" style="overflow-y:{{ $overflow_y }}; {{ $style }}">
			@yield($content)
		</div>
	</div>
