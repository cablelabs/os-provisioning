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
        'templateRelation' => 'Wenn Unternehmen und SEPA account nicht gesetzt sind, wird die Vorlage als Basis-Vorlage verwendet. Ist das Unternehmen ausgewählt (aber kein SEPA-Konto), wird die Vorlage für die Dokumente dieses Unternehmens verwendet (anstelle des Basis-Templates). Ist ein SEPA-Konto gewählt, wird die Vorlage für die Dokumente dieses Kontos genutzt (unabhängig von Basis- und eventuell eingestellter Unternehmens-Vorlage). Kann bei Basis-Vorlagen nicht geändert werden!',
        'filenamePattern' => 'Muster für die Namen der erzeugten Dateien. Werte in geschweiften Klammern {} sind Platzhalter und werden ersetzt. Wenn nicht angegeben wird das Standard-Muster verwendet.',
        'uploadTemplate' => 'Dateinamen werden in Kleinbuchstaben umgewandelt, Leerzeichen und Schrägstriche durch Unterstriche ersetzt. Aktuell nur LaTeX-Vorlagen (*.tex) erlaubt.',
    ],
    'type' => 'Für IPv6 werden bisher nur öffentliche CPE IP-Pools unterstützt.',
];
