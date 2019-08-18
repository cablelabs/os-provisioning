<?php

namespace Modules\HfcSnmp\Entities;

return [
    'name' => 'HfcBase',
    'link' => 'HfcBase.index',
    'MenuItems' => [
        'MibFile' => [
            'link' => 'MibFile.index',
            'icon'	=> 'fa-file-o',
            'class' => MibFile::class,
        ],
    ],
];
