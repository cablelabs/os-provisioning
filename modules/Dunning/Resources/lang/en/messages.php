<?php

return [
    'addedDebts' => 'Add debts to the following :count contracts: :numbers',
    'transaction' => [
        'create' => 'Create debt because of',
        'credit' => [
            // 'diff' => [
            //     'contractInvoice' => 'Contract :contract and invoice number from transfer reason do not belong to the same contract. (Invoice belongs to contract :invoice)',
            //     'contractSepa' => 'Transfer reason contains contract nr :contract but sepamandate belongs to contract :sepamandate',
            //     'invoiceSepa' => 'Found sepamandate belongs to contract :sepamandate, but the found invoice belongs to contract :invoice',
            // ],
            // 'missAll' => 'Neither contract, nor invoice, nor sepa mandate could be found',
            'missInvoice' => 'Transfer reason contains invoice number that does not belong to NMSPrime',
            'multipleContracts' => 'NMSPrime actually considers neither pre- nor suffix of the contract number to determine the contract possibly related to the transaction.',
            'noInvoice' => [
                'contract' => 'The transfer could belong to contract number :contract of the transfer reason.',
                'default' => 'The (correct) invoice number is missing in the transfer reason.',
                'notFound' => 'The given invoice number :number of the transfer reason could not be found in the system.',
                'sepa' => 'The transfer could belong to the contract :contract of the found IBAN.',
                'special' => 'Add debt to contracts :numbers found via contract number of the transfer reason or the IBAN besides missing the invoice number, because the last invoice amount being the same as the transaction amount for each contract.',
            ],
        ],
        'debit' => [
            'diffContractSepa' => 'SEPA mandate and invoice number belong to different contract',
            'missSepaInvoice' => 'Neither SepaMandate nor invoice nr could be found in the database',
        ],
        'default' => [
            'debit' => 'Debit-Transaction of :holder with invoice NR :invoiceNr, SepaMandate reference :mref, price :price IBAN :iban',
            'credit' => 'Credit transfer of :holder with price :price, IBAN :iban and transfer reason :reason',
        ],
        'exists' => 'Ignore :debitCredit transaction as debt was already imported. (Price :price; Description :description)',
    ],
    'parseMt940Failed' => 'Error on parsing the uploaded file. See logfile. (:msg)',
];
