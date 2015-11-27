		<?php
			// searchscope for following form is the current model
			$next_scope = 'Modem';
		?>
		{{ Form::openDivClass(6) }}
			{{ Form::model(null, array('route'=>'Modem.fulltextSearch', 'method'=>'GET')) }}
				{{ Form::hidden('preselect_field', $field) }}
				{{ Form::hidden('preselect_value', $search) }}
				@include('Generic.searchform')
			{{ Form::close() }}
		{{ Form::closeDivClass() }}