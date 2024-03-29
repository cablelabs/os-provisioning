<?php

return [
    'Cacti' => [
        // source https://publicdomainvectors.org/en/free-clipart/Outlined-cactus/69391.html
        'icon'  => 'cacti.svg',
        'description' => 'apps.Cacti',
        'link' => '/cacti',
        'website' => 'https://cacti.net',
        'rpmName' => 'cacti-nmsprime',
    ],
    'Icinga' => [
        // source https://www.svgrepo.com/svg/13675/network
        'icon'  => 'icinga.svg',
        'description' => 'apps.Icinga',
        'link' => '/icingaweb2',
        'website' => 'https://icinga.com/docs/icinga-2/latest',
        'rpmName' => 'icinga2',
    ],
    'GenieACS' => [
        'icon'  => 'genieacs.svg',
        'description' => 'apps.GenieACS',
        'link' => '/genieacs',
        'website' => 'https://genieacs.com',
        'rpmName' => 'genieacs',
    ],
    'Grafana' => [
        'icon'  => 'grafana.svg',
        'description' => 'apps.Grafana',
        'link' => '/grafana',
        'website' => 'https://grafana.com/',
        'rpmName' => 'grafana',
        'maxDiagrams' => env('GRAFANA_MAX_DIAGRAMS', 200),
    ],
    'Kafka' => [
        'icon'  => 'Kafka.svg',
        'description' => 'apps.Kafka',
        'link' => '#',
        'website' => 'https://kafka.apache.org/',
        'rpmName' => 'kafka',
    ],
    'Telegraf' => [
        'icon'  => 'Telegraf.svg',
        'description' => 'apps.Telegraf',
        'link' => '#',
        'website' => 'https://docs.influxdata.com/telegraf/',
        'rpmName' => 'telegraf',
    ],
    'Prometheus' => [
        'icon'  => 'Prometheus.svg',
        'description' => 'apps.Prometheus',
        'link' => '#',
        'website' => 'https://prometheus.io/',
        'rpmName' => 'prometheus',
    ],
];
