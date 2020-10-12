<?php

namespace Modules\HfcSnmp\Entities;

return [
    'name' => 'HfcBase',
    'link' => 'HfcBase.index',
    'parent' => 'HfcBase',
    'MenuItems' => [
        'MibFile' => [
            'link' => 'MibFile.index',
            'icon'	=> 'fa-file-o',
            'class' => MibFile::class,
        ],
        // 	'SnmpValues' =>
        // 	['link' => 'SnmpValue.index',
        // 	'icon'	=> 'fa-']
    ],
];
