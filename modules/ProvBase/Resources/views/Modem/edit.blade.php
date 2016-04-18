<?php
$panel_right = [['name' => 'Edit', 'route' => 'Modem.edit', 'link' => [$view_var->id]],
				['name' => 'Analyses', 'route' => 'Provmon.index', 'link' => [$view_var->id]],
				['name' => 'CPE-Analysis', 'route' => 'Provmon.cpe', 'link' => [$view_var->id]]];

// MTA: only show MTA analysis if Modem has MTAs
if (isset($view_var->mtas[0]))
	array_push($panel_right, ['name' => 'MTA-Analysis', 'route' => 'Provmon.mta', 'link' => [$view_var->id]]);
?>

@extends ('Generic.edit')