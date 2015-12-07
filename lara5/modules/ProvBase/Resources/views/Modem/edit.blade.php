<?php 
$panel_right = [['name' => 'Edit', 'route' => 'Modem.edit', 'link' => [$view_var->id]], 
				['name' => 'Analyses', 'route' => 'Modem.ping', 'link' => [$view_var->id]]];

?>

@extends ('Generic.edit')