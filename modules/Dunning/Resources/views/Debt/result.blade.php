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
      <i class="fa fa-file-invoice-dollar-o"></i>
      <i class="fa fa-dollar"></i>
      <i class="fa fa-id-card-o"></i>
      <i class="far fa-money-bill-alt"></i>
      <i class="fas fa-donate"></i> {{ $debt }}
    </td>
  </tr>
</table>
