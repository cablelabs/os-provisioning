@extends('Generic.create')

@section('content_right')

<?php

	// try to get free numbers; these can be given as string (HTML) or as array

	// get numbers for the active provider
	if (\Module::collections()->has('ProvVoipEnvia')) {

		$free_numbers_panel_headline = 'Asking envia TEL for free numbers';

		if (!App::environment('testing')) {	// get data from envia
			try {
				$currently_free_numbers = \Modules\ProvVoipEnvia\Entities\ProvVoipEnvia::get_free_numbers_for_view();
			}
			catch (Exception $ex) {
				echo "Exception getting free numbers from envia TEL: ".$ex->getMessage();
			}
		}
		else {	// for testing: inject fake data
			$currently_free_numbers = [0 => "01234/11111111", 1 => "01234/22222222"];
		}
	}

?>
	{{-- show this panel if information about free numbers is available --}}
	@if (isset($currently_free_numbers))
		@section('free_numbers_panel')

			@if (is_array($currently_free_numbers))

				<?php
					// set flag to include the correct JavaScript function
					// used in resources/views/Generic/form-js-fill-input-from-href.blade.php
					$load_input_from_href_filler_for_free_numbers = True;
				?>

				<h4>Success</h4>
				<h5>You can click a number to fill the form…</h5>
				<div id="free_numbers_return">
				@foreach ($currently_free_numbers as $free_number)
					<a href="#">{{ $free_number }}</a><br>
				@endforeach
				</div>
			@elseif (is_string($currently_free_numbers))
				{!! $currently_free_numbers !!}
			@endif

		@stop

		@include ('bootstrap.panel', array ('content' => 'free_numbers_panel', 'view_header' => $free_numbers_panel_headline, 'md' => 4))
	@endif

@stop
