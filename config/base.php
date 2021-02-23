<?php

$config = [
    'link' => null,
    'MenuItems' => [
        'Config Page' => [
            'link' => 'Config.index',
            'icon' => 'fa-book',
            'class' => App\GlobalConfig::class,
        ],
        'Log' => [
            'link' => 'GuiLog.index',
            'icon' => 'fa-history',
            'class' => App\GuiLog::class,
        ],
    ],

];

$modulesListFile = '/var/www/nmsprime/modules_statuses.json';
if (! is_file($modulesListFile)) {
    return $config;
}

$modulesList = json_decode(file_get_contents($modulesListFile));
if (isset($modulesList->Dashboard) && $modulesList->Dashboard) {
    $config['link'] = 'Dashboard.index';
}

return $config;
