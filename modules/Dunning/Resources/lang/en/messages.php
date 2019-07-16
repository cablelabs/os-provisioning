<?php

return [
    'transaction' => [
        'create' => 'Create debt because of',
        'credit' => [
            'diff' => [
                'contractInvoice' => 'Contract :contract and invoice number from transfer reason do not belong to the same contract. (Invoice belongs to contract :invoice)',
                'contractSepa' => 'Transfer reason contains contract nr :contract but sepamandate belongs to contract :sepamandate',
                'invoiceSepa' => 'Found sepamandate belongs to contract :sepamandate, but the found invoice belongs to contract :invoice',
            ],
            'missAll' => 'Neither contract, nor invoice, nor sepa mandate could be found',
            'missInvoice' => 'Transfer reason contains invoice number that does not belong to NMSPrime',
        ],
        'debit' => [
            'diffContractSepa' => 'SEPA mandate and invoice number belong to different contract',
            'missSepaInvoice' => 'Neither SepaMandate nor invoice nr could be found in the database',
        ],
        'default' => [
            'debit' => 'Transaction of :holder with invoice NR :invoiceNr, SepaMandate reference :mref, price :price IBAN :iban',
            'credit' => 'Transaction of :holder with price :price, IBAN :iban and transfer reason :reason',
        ],
        'exists' => 'Ignore :debitCredit transaction as debt was already imported. (Price :price; Description :description)',
    ],
];
