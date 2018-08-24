<?php

return[
    'MenuItems' => [
        'Config Page' => [
            'link' => 'Config.index',
            'icon' => 'fa-book',
            'class' => App\GlobalConfig::class,
        ],
     'Logging' => [
         'link' => 'GuiLog.index',
            'icon' => 'fa-history',
            'class' => App\GuiLog::class,
        ],
    ],
];
