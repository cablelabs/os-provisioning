<?php

return [
 /**
  *	MODULE: BillingBase
  */
	//BillingBaseController
 	'BillingBase_extra_charge' 		=> 'Aufschlag auf Einkaufspreis in %. Nur wenn nicht schon vom Provider berechnet!',
	'BillingBase_cdr_offset' 		=> "ACHTUNG: Eine Erhöhung der Differenz führt bei bereits vorhandenen Daten im nächsten Abrechnungslauf zu überschriebenen EVNs - Stellen Sie sicher, dass diese gesichert/umbenannt wurden!\n\n1 - wenn die Einzelverbindungsnachweise vom Juni zu den Rechnungen vom Juli gehören; 0 - wenn beide für den selben Monat abgerechnet werden; 2 - wenn die Einzelverbindungsnachweise vom Januar zu den Rechnungen vom März gehören.",
	'BillingBase_InvoiceNrStart' 	=> 'Rechnungsnummer startet jedes neue Jahr mit dieser Nummer.', 
	'BillingBase_ItemTermination'	=> 'Erlaubt es Kunden gebuchte Produkte nur bis zum letzten Tag des Monats zu kündigen.', 
	'BillingBase_MandateRef'		=> "Eine Vorlage kann mit SQL-Spalten des Auftrags oder mit der Mandat-Tabelle erstellt werden - mögliche Felder: \n", 
	'BillingBase_SplitSEPA'			=> 'SEPA-Überweisungen sind in unterschiedliche XML-Dateien aufgeteilt, abhängig von ihrem Übertragungstyp.', 

	//CompanyController
	'Company_Management'			=> 'Trennung der Namen durch Komma.', 
	'Company_Directorate'			=> 'Trennung der Namen durch Komma.',	
	'Company_TransferReason'		=> 'Vorlage aller Rechnungsklassen als Datenfeld-Schlüssel - Vertrags- und Rechnungsnummer sind standardmäßig ausgewählt.', 

	//CostCenterController
	'CostCenter_BillingMonth'		=> 'Standard: 6 (Juni) - wenn nicht festgelegt. Bei Veränderung muss der eingetragene Monat mindestens dem aktueller Monat entsprechen, um fehlende Zahlungen zu vermeiden.',

	//ItemController
	'Item_ProductId'				=> 'Alle Felder außer dem Abrechnungszyklus müssen vor eine Änderung des Produkts gelöscht werde! Andernfalls können die Produkte in den meisten Fällen nicht gespeichert werden.', 
	'Item_ValidFrom'				=> 'Für einmalige (Zusatz-)Zahlungen kann das Feld genutzt werden, um die Zahlung zu teilen - nur  Jahr und Monat (jjjj.mm) werden berücksichtigt.',
	'Item_ValidFromFixed'			=> 'Feste Termine werden für die Abrechnung verwendet und werden nicht durch externe Aufträge aktualisiert.',
	'Item_ValidToFixed'				=> 'Feste Termine werden für die Abrechnung verwendet und werden nicht durch externe Aufträge aktualisiert.',
	'Item_CreditAmount'				=> 'Nettobetrag, der dem Kunde gutgeschrieben werden soll',

	//ProductController
	'Product_Name' 					=> 'Für Kredite ist es möglich einen Typ zuzuweisen, indem der Typname dem Namen des Kredits angefügt wird - z.B.: \'Kredit Gerät\'', 
	'Product_Type'					=> 'Alle Felder außer dem Abrechnungszyklus müssen vor eine Änderung des Produkts gelöscht werde! Andernfalls können die Produkte in den meisten Fällen nicht gespeichert werden.',
	'Product_Number_of_Cycles' 		=> 'Achtung! Für alle Produkte, die in einem wiederkehrenden Zyklus bezahlt werden steht der Preis für jede einzelne Zahlung. Für Produkte, die einmalig bezahlt werden wird der Preis durch die Anzahl der Zyklen geteilt.', 

	//SalesmanController
	'Salesman_ProductList'			=> 'Füge alle Produkttypen an, für die eine Provision erzielt werden kann - möglich:', 

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
	'Modem_NetworkAccess'			=> 'Deaktivieren/Aktivieren des Netzwerkzugriffs - Achtung: Wenn das Abrechnungsmodul installiert ist, wird diese Checkbox täglich überschrieben, je nach Gültigkeit der Tarif-Option - vorausgesetzt sie wurde nicht manuell gesetzt.', 
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
];
