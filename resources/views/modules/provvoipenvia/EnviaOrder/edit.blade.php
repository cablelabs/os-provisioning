
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

		if ($additional_data['user_actions']['hints'] || $additional_data['user_actions']['links']) {
			echo '<div class="col-md-12" style="margin-top: 30px; padding-top: 20px; border-top:solid #888 1px">';

			if ($additional_data['user_actions']['head']) {
				echo $additional_data['user_actions']['head'];
			}

			foreach ($additional_data['user_actions']['hints'] as $class => $content) {
				echo "<br>";
				echo "<b><u>".$class."</u></b><br>";
				echo $content;
				echo "<br>";
			}

			if ($additional_data['user_actions']['links']) {

				echo "<br>";
				$tmp = array();
				foreach ($additional_data['user_actions']['links'] as $linktext => $link) {
					array_push($tmp, '<a href="'.$link.'" target="_self">» '.$linktext.'</a>');
				}
				echo implode('<br>', $tmp);
			}
			echo '</div>';
		}
	?>

	<div class="col-md-12" style="margin-top: 30px; padding-top: 20px; border-top:solid #888 1px">
		<?php

			// show the mailto links
			$tmp = array();
			foreach ($additional_data['mailto_links'] as $to => $link) {
				array_push($tmp, '<a href="'.$link.'">» Mail to '.$to.'</a>');
			}
			echo implode('<br>', $tmp);

		?>
	</div>
@stop
