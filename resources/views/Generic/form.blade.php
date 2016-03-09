<script>setTimeout("document.getElementById('success_msg').style.display='none';", 6000);</script>

<h4 id='success_msg'>{{ Session::get('message') }}</h4>

@foreach($form_fields as $field)

	<?php 
		/*
		 * Preparations for Form View
		 * TODO: this should be moves to a sublayer in Controller Context ?
		 */
		$value   = null;
		$options = null;

		// prepare $options and $value 
		if (array_key_exists('value', $field))
			$value = $field["value"];
		if (array_key_exists('options', $field))
			$options = $field["options"];

		// dd($_GET, $field, $hide_hidden_0);
		/*
		 * Hide Fields:
		 *
		 * 1. Hide fields that are in HTML _GET array.
		 *    This is required for creating a "relational child" 
		 *    elements with pre-filled values. This must be first
		 *    done, otherwise pre-filling does not work
		 *
		 *    Example: Mta/create?modem_id=100002 -> creates MTA to Modem id 100002
		 */
		if (isset($_GET[$field['name']]) && (isset($field['hidden'])) && $field['hidden'] != '0')
		{
				echo Form::hidden ($field["name"], $_GET[$field['name']]);
				continue;
		}

		/* 
		 * 2. check if hidden is set in get_form_fields()
		 * 3. globally hide all relation fields 
		 *    (this means: all fields ending with _id)
		 */
		if (array_key_exists('hidden', $field) && $field['hidden'] != '0' )
		{
				echo Form::hidden ($field["name"]);
				continue;			
		}
	?>

	{{ Form::openGroup($field["name"], $field["description"]) }}
		<?php
			/*
			 * Output the Form Elements
			 */

			// Checkbox - where pre-checked is enabled
			if ($field["form_type"] == 'checkbox' && isset($field['checked'])) 
				echo Form::checkbox($field['name'], $value, null, ((isset($field['checked']) && $field['checked']) ? true : false));
			else
			{
				// All other Form Types
				if 		(!$value && !$options) 	echo Form::$field["form_type"]($field["name"]);
				elseif 	($value && !$options) 	echo Form::$field["form_type"]($field["name"], $value);
				elseif 	($options) 				echo Form::$field["form_type"]($field["name"], $value, $options);
			}
		?>
	{{ Form::closeGroup() }}

	<?php
		if (array_key_exists('space', $field))
			echo "<div class=col-md-12>_</div>";
	?>

@endforeach

{{ Form::submit($save_button) }}
