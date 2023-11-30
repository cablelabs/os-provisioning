<?php

// to be executed as a tinker script

$apiRoutes = collect(Route::getRoutes()->getIterator())
    ->filter(fn ($route) => Str::contains($route->uri, 'api/v'))
    ->map(fn ($route) => Str::replaceFirst('/v{ver}/', '/v0/', $route->uri))
    ->sort(SORT_NATURAL | SORT_FLAG_CASE);

$base = 'https://localhost:8080';
$user = 'root@localhost.com';
$pass = 'secret';
$opts = [
    'http' => [
        'method' => 'GET',
        'header' => 'Authorization: Basic '.base64_encode($user.':'.$pass),
    ],
];

$types = [
    'bigint' => 'integer',
    'integer' => 'integer',
    'string' => 'string',
    'boolean' => 'boolean',
    'date' => 'string',
    'text' => 'string',
    'decimal' => 'number',
    'float' => 'number',
    'smallint' => 'integer',
];

// integer: int32, int64
// number: float, double
// string: date, date-time, password, byte, binary
$formats = [
    'bigint' => 'int64',
    'date' => 'date',
    'decimal' => 'float',
    'float' => 'float',
];

$colRename = [
    'ModemOption' => 'modem_option',
    'TicketType' => 'ticket_type',
    'GlobalConfig' => 'global_config',
    'User' => 'users',
    'Role' => 'roles',
];

$ignore = [
    'configfile.firmware_upload',
    'contract.related_phonenrs',
    'ticket.tickettypes_ids[]',
    'ticket.users_ids[]',
    'ticketsystem.noreply_mail',
    'ticketsystem.noreply_name',
    'ticketsystem.open_tickets',
    'netelement.infrastructure_file_upload',
    'netelement.enable_agc',
    'mibfile.mibfile_upload',
    'parameter.name',
    'sepaaccount.template_invoice_upload',
    'sepaaccount.template_cdr_upload',
    'company.logo_upload',
    'company.conn_info_template_fn_upload',
    'settlementrun.voucher_nr',
    'settlementrun.banking_file_upload',
    'ccc.image_upload',
    'global_config.password_reset_interval',
    'global_config.login_img_upload',
    'global_config.is_all_nets_sidebar_enabled',
    'users.password_confirmation',
    'users.roles_ids[]',
    'users.users_ids[]',
    'roles.users_ids[]',
];

$ret = [
    'openapi' => '3.0.3',
    'info' => [
        'title' => 'NMS Prime API',
        'description' => 'Description of the NMS Prime API',
        'contact' => [
            'email' => 'ole.ernst@nmsprime.com',
        ],
        'license' => [
            'name' => 'Apache 2.0',
            'url' => 'http://www.apache.org/licenses/LICENSE-2.0.html',
        ],
        'version' => '0.0.1',
    ],
    'components' => [
        'schemas' => [
            'DeleteResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => [
                        'type' => 'boolean',
                    ],
                ],
            ],
            'GenericResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => [
                        'type' => 'boolean',
                    ],
                    'id' => [
                        'type' => 'integer',
                        'format' => 'int64',
                    ],
                ],
            ],
        ],
    ],
];

DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('configfile_device', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('configfile_public', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('domain_type', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('mta_type', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('phonetariff_type', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('phonetariff_voip_protocol', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('ticket_priority', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('oid_html_type', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('oid_type', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('product_type', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('product_billing_cycle', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('numberrange_type', 'string');
DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('billingbase_userlang', 'string');

foreach ($apiRoutes as $route) {
    if (! Str::contains($route, '/create')) {
        continue;
    }

    $response = json_decode(file_get_contents("$base/$route", false, stream_context_create($opts)), true);
    if (! $response) {
        continue;
    }

    $fields = $response['models'];
    $route = dirname($route);
    $entity = basename($route);

    $ret['components']['schemas']["${entity}Response"]['type'] = 'object';
    $ret['components']['schemas']["${entity}Response"]['properties']['success']['type'] = 'boolean';
    $ret['components']['schemas']["${entity}Response"]['properties']['models']['type'] = 'object';
    $ret['components']['schemas']["${entity}Response"]['properties']['models']['additionalProperties']['$ref'] = "#/components/schemas/$entity";

    $ret['components']['schemas']["${entity}SingleResponse"]['type'] = 'object';
    $ret['components']['schemas']["${entity}SingleResponse"]['properties']['id']['type'] = 'integer';
    $ret['components']['schemas']["${entity}SingleResponse"]['properties']['id']['format'] = 'int64';
    $ret['components']['schemas']["${entity}SingleResponse"]['properties']['success']['type'] = 'boolean';
    $ret['components']['schemas']["${entity}SingleResponse"]['properties']['models']['type'] = 'object';
    $ret['components']['schemas']["${entity}SingleResponse"]['properties']['models']['additionalProperties']['$ref'] = "#/components/schemas/$entity";
    $ret['components']['schemas']["${entity}SingleResponse"]['properties']['models']['maxProperties'] = 1;

    $required = [];
    $ret['components']['schemas'][$entity]['type'] = 'object';
    foreach ($fields as $name => $values) {
        $column = array_key_exists($entity, $colRename) ? $colRename[$entity] : strtolower($entity);
        if (in_array("$column.$name", $ignore)) {
            continue;
        }

        try {
            $columnType = Schema::getColumnType($column, $name);
        } catch(Doctrine\DBAL\Exception $e) {
            echo "can't retrieve column type for $entity, $name: ".$e->getMessage()."\n";
            $columnType = null;
        }

        if ($type = $types[$columnType] ?? null) {
            $ret['components']['schemas'][$entity]['properties'][$name]['type'] = $type;
        }

        if ($format = $formats[$columnType] ?? null) {
            $ret['components']['schemas'][$entity]['properties'][$name]['format'] = $format;
        }

        if ($description = $values['description'] ?? null) {
            $ret['components']['schemas'][$entity]['properties'][$name]['description'] = $description;
            if (Str::endsWith($description, '*')) {
                $required[] = $name;
            }
        }

        $ret['paths']["/$route"]['get'] = [
            'summary' => "Get all existing {$entity}s",
            'description' => "Get all existing {$entity}s",
            'operationId' => "get{$entity}s",
            'responses' => [
                '200' => [
                    'description' => 'Successful operation',
                    'content' => ['application/json' => ['schema' => ['$ref' => "#/components/schemas/${entity}Response"]]],
                ],
                '400' => [
                    'description' => "$entity not found",
                ],
            ],
        ];

        $ret['paths']["/$route/{{$entity}Id}"]['get'] = [
            'summary' => "Get existing $entity",
            'description' => "Get existing $entity by Id",
            'operationId' => "get{$entity}ById",
            'parameters' => [
                [
                    'name' => "{$entity}Id",
                    'in' => 'path',
                    'description' => "ID of $entity to return",
                    'required' => true,
                    'schema' => ['type' => 'integer', 'format' => 'int64'],
                ],
            ],
            'responses' => [
                '200' => [
                    'description' => 'Successful operation',
                    'content' => ['application/json' => ['schema' => ['$ref' => "#/components/schemas/${entity}SingleResponse"]]],
                ],
                '400' => [
                    'description' => "$entity not found",
                ],
            ],
        ];

        $ret['paths']["/$route"]['post'] = [
            'summary' => "Create $entity",
            'description' => "Create $entity",
            'operationId' => "create{$entity}",
            'requestBody' => [
                'description' => "Create $entity",
                'content' => ['application/json' => ['schema' => ['$ref' => "#/components/schemas/$entity"]]],
                'required' => true,
            ],
            'responses' => [
                '200' => [
                    'description' => 'Successful operation',
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/GenericResponse']]],
                ],
                '400' => [
                    'description' => "$entity not found",
                ],
            ],
        ];

        $ret['paths']["/$route/{{$entity}Id}"]['patch'] = [
            'summary' => "Update existing $entity",
            'description' => "Update existing $entity by Id",
            'operationId' => "update{$entity}ById",
            'parameters' => [
                [
                    'name' => "{$entity}Id",
                    'in' => 'path',
                    'description' => "ID of $entity to return",
                    'required' => true,
                    'schema' => ['type' => 'integer', 'format' => 'int64'],
                ],
            ],
            'requestBody' => [
                'description' => "Update existing $entity by Id",
                'content' => ['application/json' => ['schema' => ['$ref' => "#/components/schemas/$entity"]]],
                'required' => true,
            ],
            'responses' => [
                '200' => [
                    'description' => 'Successful operation',
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/GenericResponse']]],
                ],
                '400' => [
                    'description' => "$entity not found",
                ],
            ],
        ];

        $ret['paths']["/$route/{{$entity}Id}"]['delete'] = [
            'summary' => "Delete existing $entity",
            'description' => "Delete existing $entity by Id",
            'operationId' => "delete{$entity}ById",
            'parameters' => [
                [
                    'name' => "{$entity}Id",
                    'in' => 'path',
                    'description' => "ID of $entity to delete",
                    'required' => true,
                    'schema' => ['type' => 'integer', 'format' => 'int64'],
                ],
            ],
            'responses' => [
                '200' => [
                    'description' => 'Successful operation',
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/DeleteResponse']]],
                ],
                '400' => [
                    'description' => "$entity not found",
                ],
            ],
        ];
    }

    if ($required) {
        $ret['components']['schemas'][$entity]['required'] = $required;
    }
}

yaml_emit_file('openapi.yaml', $ret);
exit;
