<?php

return [
    'transaction' => [
        'create' => 'Erstelle offenen Posten aufgrund von',
        'credit' => [
            'diff' => [
                'contractInvoice' => 'Der Vertrag :contract und die Rechnungsnummer aus dem Verwendungszweck gehören nicht zum selben Vertrag (Die Rechnung gehört zum Vertrag :invoice)',
                'contractSepa' => 'Der Verwendungszweck enthält die Vertragsnummer :contract, aber das gefundene SEPA-Mandat gehört zum Vertrag :sepamandate',
                'invoiceSepa' => 'Das SEPA-Mandat des überweisenden Kontos gehört im NMSPrime zum Vertrag :sepamandate, aber die Rechnung aus dem Verwendungszweck gehört zum Vertrag :invoice',
            ],
            'missAll' => 'Es konnte weder eine zugehörige Vertragsnummer, noch ein SEPA-Mandat, noch eine Rechnungsnummer gefunden werden',
            'missInvoice' => 'Der Verwendungszweck enthält eine Rechnungsnummer, die nicht zu NMSPrime gehört',
        ],
        'debit' => [
            'diffContractSepa' => 'SEPA-Mandat und Rechnungsnummer gehören zu unterschiedlichen Verträgen',
            'missSepaInvoice' => 'Weder das SEPA-Mandat noch die Rechnungsnummer konnten in der Datenbank gefunden werden',
        ],
        'default' => [
            'debit' => 'Transaktion von :holder mit Rechnungsnr :invoiceNr, SEPA-Mandatsreferenz :mref, Betrag :price und IBAN :iban',
            'credit' => 'Transaktion von :holder mit Betrag :price, IBAN :iban und Verwendungszweck \':reason\'',
        ],
        'exists' => 'Ignoriere Transaktion. :debitCredit wurde bereits importiert. (Betrag :price; Beschreibung :description)',
    ],
];
