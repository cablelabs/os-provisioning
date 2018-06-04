
@extends('Generic.edit')

@section('content_left')

	@DivOpen(12)
	<table class="table-hover">
	@foreach($form_fields as $field)
		<tr>
			<td style="padding-right:20px;">{{ $field['description'] }}</td>
			<td>{{ $field['field_value'] }}</td>
		</tr>
	@endforeach
	</table>
	@DivClose()

	<?php

		if ($additional_data['relations']) {

			echo '<div class="col-md-12" style="margin-top: 30px; padding-top: 20px; border-top:solid #888 1px">';

			if ($additional_data['relations']['head']) {
				echo $additional_data['relations']['head'];
			}

			foreach ($additional_data['relations']['hints'] as $class => $content) {
				echo "<br>";
				echo "<b><u>".$class."</u></b><br>";
				echo $content;
				echo "<br>";
			}

			if ($additional_data['relations']['links']) {

				echo "<br>";
				$tmp = array();
				foreach ($additional_data['relations']['links'] as $linktext => $link) {
					array_push($tmp, '<a href="'.$link.'" target="_self">» '.$linktext.'</a>');
				}
				echo implode('<br>', $tmp);
			}

			echo '</div>';
		}
	?>


@stop
