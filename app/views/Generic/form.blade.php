<script>setTimeout("document.getElementById('success_msg').style.display='none';", 2500);</script>


	@foreach($form_fields as $field)

		<?php
			$value   = null;
			$options = null;

			if (array_key_exists('value', $field))
				$value = $field["value"];
			if (array_key_exists('options', $field))
				$options = $field["options"];
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

	@endforeach

	{{ Form::submit('Save') }}

	<h3 id='success_msg'>{{ Session::get('message') }}</h3>
