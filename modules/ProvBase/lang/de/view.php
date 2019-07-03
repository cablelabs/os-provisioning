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
        'baseTemplate' => 'Basis-Vorlage',
        'buttonDownloadTemplate' => 'Herunterladen',
        'buttonTestTemplate' => 'Testen',
        'chooseTemplateFile' => 'Vorlagen-Datei auswählen oder',
        'company' => 'Unternehmen oder',
        'filenamePattern' => 'Dateinamen-Muster',
        'sepaaccount' => 'SEPA-Konto',
        'type' => 'Dokument-Typ',
        'uploadTemplateFile' => 'Vorlagen-Datei hochladen',
    ],
    'documentType' => [
        'viewType' => [
            // used in console command; keys have to correlate to database entries in documenttype:type
            'cdr' => 'Einzelverbindungsnachweis',
            'connection_info' => 'Anschlussinformationen',
            'contract_change' => 'Vertragsänderung',
            'contract_end' => 'Vertragsende',
            'contract_start' => 'Vertragsbeginn',
            'invoice' => 'Rechnung',
            'letterhead' => 'Briefpapier',
            'phonenumber_activation' => 'Rufnummeraktivierung',
            'phonenumber_deactivation' => 'Rufnummerdeaktivierung',
            'upload' => 'Upload',
        ],
    ],
    'net' => 'Netz',
    'dhcp' => [
        'lifetime' => 'Gültig',
        'expiration' => 'Ablauf',
    ],
];
