<?php

return [
    'Cacti' => [
        // source https://publicdomainvectors.org/en/free-clipart/Outlined-cactus/69391.html
        'icon'  => 'cacti.svg',
        'description' => 'apps.Cacti',
        'link' => Config('app.url').'/cacti',
        'website' => 'https://cacti.net',
        'rpmName' => 'cacti',
    ],
    'Icinga' => [
        // source https://www.svgrepo.com/svg/13675/network
        'icon'  => 'icinga.svg',
        'description' => 'apps.Icinga',
        'link' => Config('app.url').'/icingaweb2',
        'website' => 'https://icinga.com/docs/icinga-2/latest',
        'rpmName' => 'icinga2',
    ],
    'GenieACS' => [
        'icon'  => 'genieacs.svg',
        'description' => 'apps.GenieACS',
        'link' => Config('app.url').'/genieacs',
        'website' => 'https://genieacs.com',
        'rpmName' => 'genieacs',
    ],
];
