<?php
	$bsclass = '';
	if ($debt < 0) {
		$bsclass = 'success';
	} elseif ($debt > 0) {
		$bsclass = 'warning';
	}
?>

<table class="table">
  <tr>
    <td class="{{ $bsclass }}" align="center">
      <i class="fa fa-dollar"></i>
      {{ $debt }}
    </td>
  </tr>
</table>
