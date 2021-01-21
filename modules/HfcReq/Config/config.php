<?php

namespace Modules\HfcReq\Entities;

$config = [
    'link' => null,
    'parent' => 'HfcReq',
    'MenuItems' => [
        'NetElementType' => [
            'link'	=> 'NetElementType.index',
            'icon'	=> 'fa-object-group',
            'class' => NetElementType::class,
        ],
        'NetElement' => [
            'link'	=> 'NetElement.index',
            'icon'	=> 'fa-object-ungroup',
            'class' => NetElement::class,
        ],
    ],
    'hfParameters' => [
        'us_pwr' => 'US Power',
        'us_snr' => 'US SNR',
        'ds_pwr' => 'DS Power',
        'ds_snr' => 'DS SNR',
    ],
];

if (\Module::collections()->has('HfcBase')) {
    $config['link'] = 'HfcBase.index';
    $config['parent'] = 'HfcBase';
}

return $config;
