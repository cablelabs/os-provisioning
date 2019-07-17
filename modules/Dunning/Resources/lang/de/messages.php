<?php

return [
    'addedDebts' => 'Füge offene Posten zu folgenden :count Verträgen hinzu: :numbers',
    'transaction' => [
        'create' => 'Erstelle offenen Posten aufgrund von',
        'credit' => [
            // 'diff' => [
            //     'contractInvoice' => 'Der Vertrag :contract und die Rechnungsnummer aus dem Verwendungszweck gehören nicht zum selben Vertrag (Die Rechnung gehört zum Vertrag :invoice)',
            //     'contractSepa' => 'Der Verwendungszweck enthält die Vertragsnummer :contract, aber das gefundene SEPA-Mandat gehört zum Vertrag :sepamandate',
            //     'invoiceSepa' => 'Das SEPA-Mandat des überweisenden Kontos gehört im NMSPrime zum Vertrag :sepamandate, aber die Rechnung aus dem Verwendungszweck gehört zum Vertrag :invoice',
            // ],
            // 'missAll' => 'Es konnte weder eine zugehörige Vertragsnummer, noch ein SEPA-Mandat, noch eine Rechnungsnummer gefunden werden.',
            'missInvoice' => 'Der Verwendungszweck enthält eine Rechnungsnummer, die nicht zu NMSPrime gehört.',
            'multipleContracts' => 'NMSPrime beachtet derzeit weder Prä- noch Suffix der Vertragsnummern, um den Vertrag zu bestimmen, der der Transaktion zugeordnet sein könnte.',
            'noInvoice' => [
                'contract' => 'Die Überweisung könnte zur im Verwendungszweck angegeben Vertragsnummer :contract gehören.',
                'default' => 'Die (korrekte) Rechnungsnummer fehlt im Verwendungszweck.',
                'notFound' => 'Die angegebene Rechnungsnummer :number konnte nicht im System gefunden werden.',
                'sepa' => 'Die Überweisung könnte zum Vertrag :contract der gefundenen IBAN gehören.',
                'special' => 'Füge Offene Posten zu den über die Vertragsnummer des Verwendungszwecks oder der IBAN gefundenen Verträge :numbers trotz fehlender Rechnungsnummer hinzu, da der jeweilige überwiesene Betrag mit dem letzten Rechnungsbetrag übereinstimmt.',
            ],
        ],
        'debit' => [
            'diffContractSepa' => 'SEPA-Mandat und Rechnungsnummer gehören zu unterschiedlichen Verträgen.',
            'missSepaInvoice' => 'Weder das SEPA-Mandat, noch die Rechnungsnummer konnten in der Datenbank gefunden werden.',
        ],
        'default' => [
            'debit' => 'Lastschrift-Transaktion von \':holder\' mit Rechnungsnr \':invoiceNr\', SEPA-Mandatsreferenz \':mref\', Betrag :price, IBAN \':iban\' und Verwendungszweck \':reason\'',
            'credit' => 'Überweisung von \':holder\' mit Betrag :price, IBAN \':iban\' und Verwendungszweck \':reason\'',
        ],
        'exists' => 'Ignoriere Transaktion. :debitCredit wurde bereits importiert. (Betrag :price; Beschreibung :description)',
    ],
    'parseMt940Failed' => 'Fehler beim parsen der hochgeladenen Datei. Siehe Logfile. (:msg)',
];
