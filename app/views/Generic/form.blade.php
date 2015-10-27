<script>setTimeout("document.getElementById('success_msg').style.display='none';", 2500);</script>

<table>

	@foreach($form_fields as $field)

	<tr>
		<td>{{ Form::label($field["name"], $field["description"]) }}</td>
		<td><?php 
			if (array_key_exists('value', $field))
				echo Form::$field["form_type"]($field["name"], $field["value"]);
			else
				echo Form::$field["form_type"] ($field["name"]);?>
			</td>
		<td>{{ $errors->first('name') }}</td>
	</tr>

	@endforeach

	<tr>
		<td>{{ Form::submit('Save') }}</td>
		<td id='success_msg'>{{ Session::get('message') }}</td>
	</tr>

</table>