@extends('Generic.create')

@section('content_right')

<?php

	// try to get free numbers; these can be given as string (HTML) or as array

	// get numbers for the active provider
	if (\PPModule::is_active('provvoipenvia')) {
		$currently_free_numbers = \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia::get_free_numbers_for_view();
		$free_numbers_panel_headline = 'Asking Envia for free numbers';
	}

?>
	{{-- show this panel if information about free numbers is available --}}
	@if (isset($currently_free_numbers))
		@section('free_numbers_panel')

			@if (is_array($currently_free_numbers))
				@foreach ($currently_free_numbers as $free_number)
					{{ $free_number }}<br>
				@endforeach
			@elseif (is_string($currently_free_numbers))
				{{ $currently_free_numbers }}
			@endif

		@stop

		@include ('bootstrap.panel', array ('content' => 'free_numbers_panel', 'view_header' => $free_numbers_panel_headline, 'md' => 3))
	@endif

@stop
