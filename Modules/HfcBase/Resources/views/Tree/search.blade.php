		<?php
			// searchscope for following form is the current model
			$next_scope = 'Modem';
		?>
		{{ Form::openDivClass(6) }}
			{{ Form::model(null, array('route'=>'Modem.fulltextSearch', 'method'=>'GET')) }}

				@if (isset($preselect_field))
					{{ Form::hidden('preselect_field', $preselect_field) }}
					{{ Form::hidden('preselect_value', $preselect_value) }}
				@endif
				
				@include('Generic.searchform')
			{{ Form::close() }}
		{{ Form::closeDivClass() }}