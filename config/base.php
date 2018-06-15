<?php
return[
	'MenuItems' => [
		'Config page' => [
			'link' => 'Config.index',
			'icon' => 'fa-book',
			'class' => App\GlobalConfig::class
		],
	 'Logging' => [
		 'link' => 'GuiLog.index',
			'icon' => 'fa-history',
			'class' => App\GuiLog::class
		]
	]
];
