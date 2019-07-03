<?php

/*
|--------------------------------------------------------------------------
| Language lines for module ProvBase
|--------------------------------------------------------------------------
|
| The following language lines are used by the module ProvBase
 */

return [
    'documentTemplate' => [
        'baseTemplate' => 'Base template',
        'buttonDownloadTemplate' => 'Download',
        'buttonTestTemplate' => 'Test',
        'chooseTemplateFile' => 'Choose template file or',
        'company' => 'Company or',
        'filenamePattern' => 'Filename pattern',
        'sepaaccount' => 'SEPA account',
        'type' => 'Document type',
        'uploadTemplateFile' => 'Upload template file',
    ],
    'documentType' => [
        'viewType' => [
            // used in console command; keys have to correlate to database entries in documenttype:type
            'cdr' => 'CDR',
            'connection_info' => 'Connection info',
            'contract_change' => 'Contract change',
            'contract_end' => 'Contract end',
            'contract_start' => 'Contract start',
            'invoice' => 'Invoice',
            'letterhead' => 'Letterhead',
            'phonenumber_activation' => 'Phonenumber activation',
            'phonenumber_deactivation' => 'Phonenumber deactivation',
            'upload' => 'Upload',
        ],
    ],
    'net' => 'Net',
    'dhcp' => [
        'lifetime' => 'Lifetime',
        'expiration' => 'Expiration',
    ],
];
