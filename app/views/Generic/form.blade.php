<script>setTimeout("document.getElementById('success_msg').style.display='none';", 4000);</script>


	@foreach($form_fields as $field)

		<?php
			$value   = null;
			$options = null;

			// prepare $options and $value 
			if (array_key_exists('value', $field))
				$value = $field["value"];
			if (array_key_exists('options', $field))
				$options = $field["options"];

			// hide "hidden" fields and continue
			if (isset($_GET[$field['name']]))
			{
				echo Form::hidden ($field["name"], $_GET[$field['name']]);
				continue;
			}
		?>

		{{ Form::openGroup($field["name"], $field["description"]) }}
			<?php
				if 		(!$value && !$options) 	echo Form::$field["form_type"]($field["name"]);
				elseif 	($value && !$options) 	echo Form::$field["form_type"]($field["name"], $value);
				elseif 	($options) 				echo Form::$field["form_type"]($field["name"], $value, $options);
			?>
		{{ Form::closeGroup() }}

		<?php
			if (array_key_exists('space', $field))
				echo "<div class=col-md-12>_</div>";
		?>

	@endforeach

	{{ Form::submit('Save') }}

	<h3 id='success_msg'>{{ Session::get('message') }}</h3>
