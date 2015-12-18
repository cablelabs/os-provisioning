<?php 
$panel_right = [['name' => 'Edit', 'route' => 'Modem.edit', 'link' => [$view_var->id]], 
				['name' => 'Analyses', 'route' => 'Provmon.index', 'link' => [$view_var->id]],
				['name' => 'CPE-Analysis', 'route' => 'Provmon.cpe', 'link' => [$view_var->id]]];
?>

@extends ('Generic.edit')