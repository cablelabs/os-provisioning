<?php

return [
/**
 * Index Page - Datatables
 */
	'SortSearchColumn'				=> 'Diese Spalte kann nicht sortiert oder durchsucht werden.',
	'PrintVisibleTable'				=> 'Druckt den aktuell sichtbaren Bereich der Tabelle. Um alles zu drucken bitte im Filter \"Alle\" auswählen. Das Laden kann einige Sekunden dauern.',
	'ExportVisibleTable'			=> 'Exportiert den aktuell sichtbaren Bereich der Tabelle. Um alles zu exportieren bitte im Filter \"Alle\" auswählen. Das Laden kann einige Sekunden dauern.',
	'ChangeVisibilityTable'			=> 'Mit dieser Option können Spaten ein-/ausgeblendet werden.',

 /**
  *	MODULE: BillingBase
  */
	//BillingBaseController
	'BillingBase_extra_charge' 		=> 'Aufschlag auf Einkaufspreis in %. Nur wenn nicht schon vom Provider berechnet!',
	'BillingBase_cdr_retention' 	=> 'Anzahl der Monate, die Einzelverbindungsnachweise gespeichert werden dürfen/müssen.',
	'BillingBase_cdr_offset' 		=> "ACHTUNG: Eine Erhöhung der Differenz führt bei bereits vorhandenen Daten im nächsten Abrechnungslauf zu überschriebenen EVNs - Stellen Sie sicher, dass diese gesichert/umbenannt wurden!\n\n1 - wenn die Einzelverbindungsnachweise vom Juni zu den Rechnungen vom Juli gehören; 0 - wenn beide für den selben Monat abgerechnet werden; 2 - wenn die Einzelverbindungsnachweise vom Januar zu den Rechnungen vom März gehören.",
	'BillingBase_fluid_dates' 		=> 'Aktivieren Sie diese Checkbox wenn Sie Tarife mit ungewissem Start- und/oder Enddatum eintragen möchten. In dem Fall werden 2 weitere Checkboxen (Gültig ab fest, Gültig bis fest) auf der Posten-Seite angezeigt. Weitere Erklärungen finden Sie neben diesen Feldern!',
	'BillingBase_InvoiceNrStart' 	=> 'Rechnungsnummer startet jedes neue Jahr mit dieser Nummer.',
	'BillingBase_ItemTermination'	=> 'Erlaubt es Kunden gebuchte Produkte nur bis zum letzten Tag des Monats zu kündigen.',
	'BillingBase_MandateRef'		=> "Eine Vorlage kann mit SQL-Spalten des Auftrags oder mit der Mandat-Tabelle erstellt werden - mögliche Felder: \n",
	'BillingBase_SplitSEPA'			=> 'SEPA-Überweisungen sind in unterschiedliche XML-Dateien aufgeteilt, abhängig von ihrem Übertragungstyp.',

	//CompanyController
	'Company_Management'			=> 'Trennung der Namen durch Komma.',
	'Company_Directorate'			=> 'Trennung der Namen durch Komma.',
	'Company_TransferReason'		=> 'Vorlage aller Rechnungsklassen als Datenfeld-Schlüssel - Vertrags- und Rechnungsnummer sind standardmäßig ausgewählt.',

	//CostCenterController
	'CostCenter_BillingMonth'		=> 'Abrechnungsmonat für jährliche Posten. Gilt für den Monat für den die Rechnungen erstellt werden. Standard: 6 (Juni) - wenn nicht festgelegt. Bitte seien Sie vorsichtig beim Ändern innerhalb des Jahres: das Resultat könnten fehlende Zahlungen sein!',

	//ItemController
	'Item_ProductId'				=> 'Alle Felder außer dem Abrechnungszyklus müssen vor eine Änderung des Produkts gelöscht werde! Andernfalls können die Produkte in den meisten Fällen nicht gespeichert werden.',
	'Item_ValidFrom'				=> 'Für einmalige (Zusatz-)Zahlungen kann das Feld genutzt werden, um die Zahlung zu teilen - nur  Jahr und Monat (jjjj.mm) werden berücksichtigt.',
	'Item_ValidFromFixed'			=> 'Dieses Feld ist standardmäßig gesetzt! Deaktivieren Sie diese Checkbox, wenn der Tarif zum gewünschten Startdatum inaktiv bleiben soll (z.B. falls auf einen Portierungstermin gewartet wird). Der Tarif startet damit nicht und wird auch nicht abgerechnet bis Sie die Checkbox aktivieren. Bei Erreichen des Startdatums wird dieses außerdem jeden Tag erneut auf den darauffolgenden Tag gesetzt. Info: Feste Termine werden nicht durch externe Aufträge (z.B. vom Telefonie-Provider) aktualisiert.',
	'Item_ValidToFixed'				=> 'Dieses Feld ist standardmäßig gesetzt! Deaktivieren Sie diese Checkbox, wenn das Enddatum noch ungewiss ist. Der Tarif bleibt damit aktiv und wird weiterhin abgerechnet bis Sie die Checkbox aktivieren. Bei Erreichen des Enddatums wird dieses außerdem jeden Tag erneut auf den darauffolgenden Tag gesetzt. Info: Feste Termine werden nicht durch externe Aufträge (z.B. vom Telefonie-Provider) aktualisiert.',
	'Item_CreditAmount'				=> 'Nettobetrag, der dem Kunde gutgeschrieben werden soll. Achtung: Ein negativer Betrag wird dem Kunde abgezogen!',

	//ProductController
	'Product_maturity' 				=> 'Beispiele: 14D (14 Tage), 3M (Drei Monate), 1Y (Ein Jahr)',
	'Product_Name' 					=> 'Für Kredite ist es möglich einen Typ zuzuweisen, indem der Typname dem Namen des Kredits angefügt wird - z.B.: \'Kredit Gerät\'',
	'Product_Number_of_Cycles' 		=> 'Achtung! Für alle Produkte, die in einem wiederkehrenden Zyklus bezahlt werden steht der Preis für jede einzelne Zahlung. Für Produkte, die einmalig bezahlt werden wird der Preis durch die Anzahl der Zyklen geteilt.',
	'Product_Type'					=> 'Alle Felder außer dem Abrechnungszyklus müssen vor eine Änderung des Produkts gelöscht werde! Andernfalls können die Produkte in den meisten Fällen nicht gespeichert werden.',

	//SalesmanController
	'Salesman_ProductList'			=> 'Füge alle Produkttypen an, für die eine Provision erzielt werden kann - möglich:',

	// SepaMandate
	'sm_cc' 						=> 'Tragen Sie hier eine Kostenstelle ein, um über dieses Konto nur Posten/Produkte abzurechnen, die derselben Kostenstelle zugewiesen sind. Dem Konto eines SEPA-Mandats ohne zugewiesene Kostenstelle werden alle entstandenen Kosten abgebucht, die keinem anderen Mandat zugordnet werden können. Anmerkung: Entstehen Kosten, die keinem SEPA-Mandat zugeordnet werden können, wird angenommen, dass diese in bar beglichen werden.',
	'sm_recur' 						=> 'Aktivieren, wenn vor dem Anlegen bereits Transaktionen von diesem Konto vorgenommen worden. Setzt den Status auf Folgelastschrift. Anmerkung: Wird nur bei der ersten Lastschrift beachtet!',

	//SepaAccountController
	'SepaAccount_InvoiceHeadline'	=> 'Ersetzt die Überschrift der Rechnung, die für diese Kostenstelle erstellt wird.',
	'SepaAccount_InvoiceText'		=> 'Der Text der vier verschiedenen \'Rechnungstext\'-Felder wird automatisch in Abhängigkeit von Gesamtkosten und SEPA-Mandat gwählt und wird in der entsprechenden Rechnung für den Kunden festgelegt. Es ist möglich, alle Datenfeld-Schlüssel der Rechnungsklasse als Platzhalter in Form von {Feldname} zu verwenden, um eine Art von Vorlage zu erstellen . Diese werden durch den Ist-Wert der Rechnung ersetzt ',

	// SettlementrunController
	'settlement_verification' 		=> 'Mit aktivierter Checkbox kann der Abrechnungslauf nicht wiederholt werden. Rechnungen der Kunden werden nur mit aktivierter Checkbox angezeigt.',

 /**
  *	MODULE: HfcReq
  */
	'netelementtype_reload' 		=> 'In Sekunden. 0s zum Deaktivieren des Autoreloads.',
	'undeleteables' 				=> 'Net & Cluster können weder gelöscht werden, noch kann der Name geändert werden, da die Existenz dieser Typen Vorraussetzung für die Erzeugung des Entitity-Relationship-Diagramms ist.',

 /**
  *	MODULE: HfcSnmp
  */
	'mib_filename' 					=> 'Der Dateiname setzt sich aus MIB Name und Revision zusammen. Existiert bereits ein MIB-File mit selbem Dateiname und ist identisch, kann dieses nicht erneut angelegt werden.',
	'oid_link' 						=> 'Gehe zu OID Einstellungen',
	'oid_table' 					=> 'INFO: Dieser Parameter gehört zu einer Tabellen-OID. Durch Hinzufügen von SubOIDs or Indizes werden die SnmpWerte nur für diese abgefragt. Neben einem besseren Überblick auf der Einstellungen-Übersicht des Netzelements kann dies deren Aufrufgeschwindigkeit deutlich beschleunigen.',
	'parameter_3rd_dimension' 		=> 'Durch Aktivieren der Checkbox wird dieser Parameter zur Einstellungsseite hinter einem Element in der Tabelle hinzugefügt.',
	'parameter_diff' 				=> 'Bei Aktivierter Checkbox wird nur die Differenz des aktuell zum zuletzt abgefragten Wert angezeigt.',
	'parameter_divide_by' 			=> 'Durch Angabe von OIDs wird dieser Wert prozentual zur Summe der zu diesen OIDs abgefragten Werte dargestellt. Dies funktioniert vorerst nur in SubOIDs exakt definierter Tabellen. Die hier angegebenen OIDs müssen als Parameter in der SubOID-List eingetragen sein.',
	'parameter_indices' 			=> 'Durch Angabe einer durch Kommas getrennten Liste der Indizes der Tabellenreihen, werden die SnmpWerte nur für diese Einträge abgefragt.',
	'parameter_html_frame' 			=> 'Hat keinen Einfluss auf SubOIDs innerhalb von Tabellen (aber auf 3. Dimension-Parameter!).',

 /**
  *	MODULE: ProvBase
  */
	//ModemController
	'Modem_NetworkAccess'			=> 'Netzwerkzugriff für CPEs. (MTAs werden nicht beachtet und gehen immer online, wenn alle restlich notwendigen Konfigurationen korrekt vorgenommen wurden) - Achtung: Mit Billingmodul wird diese Checkbox während der nächtlichen Prüfung (nur) bei Tarifänderung überschrieben.',
	'contract_number' 				=> 'Achtung - Kundenkennwort wird bei Änderung automatisch geändert!',
	'mac_formats'					=> "Erlaubte Formate (Groß-/Kleinschreibung nicht unterschieden):\n\n1) AA:BB:CC:DD:EE:FF\n2) AABB.CCDD.EEFF\n3) AABBCCDDEEFF",

 /**
  *	MODULE: ProvVoip
  */
	//PhonenumberManagementController
	'PhonenumberManagement_CarrierIn' => 'Bei eingehender Portierung auf vorherigen Provider setzen. Bei Beantragung einer neuen Rufnummer EnviaTEL auswählen.',
	'PhonenumberManagement_CarrierInWithEnvia' => 'Bei Beantragung einer neuen Rufnummer setzen Sie dieses Feld auf EnviaTEL.',
	'PhonenumberManagement_EkpIn' => 'Bei eingehender Portierung auf vorherigen Provider setzen. Bei Beantragung einer neuen Rufnummer EnviaTEL auswählen.',
	'PhonenumberManagement_EkpInWithEnvia' => 'Bei Beantragung einer neuen Rufnummer setzen Sie dieses Feld auf EnviaTEL.',
	'PhonenumberManagement_TRC' => 'Nur zur Info: Sperrklassenänderungen müssen beim aktuellen Provider durchgeführt werden.',
	'PhonenumberManagement_TRCWithEnvia' => 'Sperrklassenänderungen müssen auch bei EnviaTEL vorgenommen werden (Update VoIP account)!',
	'PhonenumberManagement_Autogenerated' => 'Dieses Management wurde automatisch erzeugt. Bitte sämtliche Werte überprüfen und nach evtl. Korrektur den Haken entfernen',

/**
  * MODULE VoipMon
  */
	'mos_min_mult10' 				=> 'Minimaler Mean Opionion Score während des Anrufs',
	'caller' 						=> 'Betrachtrung der Anrufrichtung von Anrufer zu Angerufenem',
	'a_mos_f1_min_mult10' 			=> 'Minimaler Mean Opionion Score während des Anrufs mit einem festen Jitter-Buffer von 50ms',
	'a_mos_f2_min_mult10' 			=> 'Minimaler Mean Opionion Score während des Anrufs mit einem festen Jitter-Buffer von 200ms',
	'a_mos_adapt_min_mult10' 		=> 'Minimaler Mean Opionion Score während des Anrufs mit einem adaptiven Jitter-Buffer von 500ms',
	'a_mos_f1_mult10' 				=> 'durchschnittl. Mean Opionion Score während des Anrufs mit einem festen Jitter-Buffer von 50ms',
	'a_mos_f2_mult10' 				=> 'durchschnittl. Mean Opionion Score während des Anrufs mit einem festen Jitter-Buffer von 200ms',
	'a_mos_adapt_mult10' 			=> 'durchschnittl. Mean Opionion Score während des Anrufs mit einem adaptiven Jitter-Buffer von 500ms',
	'a_sl1' 						=> 'Anzahl der Pakete, welche einen aufeinander folgenden Paketverlust während des Anrufs aufweisen',
	'a_sl9' 						=> 'Anzahl der Pakete, welche neun aufeinander folgende Paketverluste während des Anrufs aufweisen',
	'a_d50' 						=> 'Anzahl der Pakete, welche eine Paketverzögerung (Packet Delay Variation - z.B. Jitter) zwischen 50ms and 70ms aufweisen',
	'a_d300' 						=> 'Anzahl der Pakete, welche eine Paketverzögerung (Packet Delay Variation - z.B. Jitter) von über 300ms aufweisen',
	'called' 						=> 'Betrachtrung der Anrufrichtung von Angerufenem zum Anrufer',
/**
 * Module Ticketsystem
 */
	'assign_user' => 'Zuweisen eines Users zu einem Ticket.',
];
