<script>setTimeout("document.getElementById('success_msg').style.display='none';", 2500);</script>

<table>

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

		<tr>
			<td>{{ Form::label($field["name"], $field["description"]) }}</td>
			<td>
				<?php
					if 		(!$value && !$options) 	echo Form::$field["form_type"]($field["name"]);
					elseif 	($value && !$options) 	echo Form::$field["form_type"]($field["name"], $value);
					elseif 	($options) 				echo Form::$field["form_type"]($field["name"], $value, $options);
				?>
			</td>
			<td>{{ $errors->first($field["name"]) }}</td>
		</tr>

	@endforeach

	<tr>
		<td>{{ Form::submit('Save') }}</td>
		<td id='success_msg'>{{ Session::get('message') }}</td>
	</tr>

</table>