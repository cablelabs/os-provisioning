<?php

return [
 /**
  *	MODULE: BillingBase	
  */
	//BillingBaseController
	'BillingBase_MandateRef'		=> "Eine Vorlage kann mit SQL-Spalten des Auftrags oder mit der Mandat-Tabelle erstellt werden - mögliche Felder: \n", 
									// A Template can be built with sql columns of contract or mandate table - possible fields: \n"
	'BillingBase_InvoiceNrStart' 	=> 'Rechnungsnummer startet jedes neue Jahr mit dieser Nummer.', 
									//Invoice Number Counter starts every new year with this number'
	'BillingBase_SplitSEPA'			=> 'SEPA-Überweisungen sind in unterschiedliche XML-Dateien aufgeteilt, abhängig von ihrem Übertragungstyp.', 
									//'Sepa Transfers are split to different XML-Files dependent of their transfer type',
	'BillingBase_ItemTermination'	=> 'Erlaubt es Kunden gebuchte Produkte nur bis zum letzten Tag des Monats zu kündigen.', 
									//'Allow Customers only to terminate booked products on last day of month',

	//CompanyController
	'Company_Management'			=> 'Trennung der Namen durch Komma.', 
									//'Comma separated list of names',
	'Company_Directorate'			=> 'Trennung der Namen durch Komma.',	
									//'Comma separated list of names',
	'Company_TransferReason'		=> 'Vorlage aller Rechnungsklassen als Datenfeld-Schlüssel - Vertrags- und Rechnungsnummer sind standardmäßig ausgewählt.', 
									//'Template from all Invoice class data field keys - Contract Number and Invoice Nr is default',

	//CostCenterController
	'CostCenter_BillingMonth'		=> 'Standard: 6 (Juni) - wenn nicht festgelegt. Bei Veränderung muss der eingetragene Monat mindestens dem aktueller Monat entsprechen, um fehlende Zahlungen zu vermeiden.',
									//'Default: 6 (June) - if not set. Has to be minimum current month on change to avoid missing payments',

	//ItemController
	'Item_ProductId'				=> 'Alle Felder außer dem Abrechnungszyklus müssen vor eine Änderung des Produkts gelöscht werde! Andernfalls können die Produkte in den meisten Fällen nicht gespeichert werden.', 
									//'All fields besides Billing Cycle have to be cleared before a type change! Otherwise items can not be saved in most cases',
	'Item_ValidFrom'				=> 'Für einmalige (Zusatz-)Zahlungen kann das Feld genutzt werden, um die Zahlung zu teilen - nur  Jahr und Monat (jjjj.mm) werden berücksichtigt.',
									//'For One Time Payments the fields can be used to split payment - Only YYYY-MM is considered then!',
	'Item_ValidFromFixed'			=> 'Feste Termine werden für die Abrechnung verwendet und werden nicht durch externe Aufträge aktualisiert.',
									//'Fixed dates are used for billing and not updated by external orders',
	'Item_ValidToFixed'				=> 'Feste Termine werden für die Abrechnung verwendet und werden nicht durch externe Aufträge aktualisiert.',
									//'Fixed dates are used for billing and not updated by external orders',
	'Item_CreditAmount'				=> 'Gross price actualy - will be changed in future to Net price',

	//ProductController
	'Product_Name' 					=> 'Für Kredite ist es möglich einen Typ zuzuweisen, indem der Typname dem Namen des Kredits angefügt wird - z.B.: \'Kredit Gerät\'', 
									//'For Credits it is possible to assign a Type by adding the type name to the Name of the Credit - e.g.: \'Credit Device\'',
	'Product_Type'					=> 'Alle Felder außer dem Abrechnungszyklus müssen vor eine Änderung des Produkts gelöscht werde! Andernfalls können die Produkte in den meisten Fällen nicht gespeichert werden.',
									//'All fields besides Billing Cycle have to be cleared before a type change! Otherwise products can not be saved in most cases',
	'Product_Number_of_Cycles' 		=> 'Achtung! Für alle Produkte, die in einem wiederkehrenden Zyklus bezahlt werden steht der Preis für jede einzelne Zahlung. Für Produkte, die einmalig bezahlt werden wird der Preis durch die Anzahl der Zyklen geteilt.', 
									//'Take Care!: for all repeatedly payed products the price stands for every charge, for Once payed products the Price is divided by the number of cycles',

	//SalesmanController
	'Salesman_ProductList'			=> 'Füge alle Produkttypen an, für die eine Provision erzielt werden kann - möglich:', 
									//'Add all Product types he gets for - possible: ',

	//SepaAccountController
	'SepaAccount_InvoiceHeadline'	=> 'Ersetzt die Überschrift der Rechnung, die für diese Kostenstelle erstellt wird.',
									//'Replaces Headline in Invoices created for this Costcenter',
	'SepaAccount_InvoiceText'		=> 'Der Text der vier verschiedenen \'Rechnungstext\'-Felder wird automatisch in Abhängigkeit von Gesamtkosten und SEPA-Mandat gwählt und wird in der entsprechenden Rechnung für den Kunden festgelegt. Es ist möglich, alle Datenfeld-Schlüssel der Rechnungsklasse als Platzhalter in Form von {Feldname} zu verwenden, um eine Art von Vorlage zu erstellen . Diese werden durch den Ist-Wert der Rechnung ersetzt ',
									//'The Text of the separate four \'Invoice Text\'-Fields is automatically chosen dependent on the total charge and SEPA Mandate and is set in the appropriate Invoice for the Customer. It is possible to use all data field keys of the Invoice Class as placeholder in the form of {fieldname} to build a kind of template. These are replaced by the actual value of the Invoice.',
	// SettlementrunController
	'settlement_verification' 		=> 'Mit aktivierter Checkbox kann der Abrechnungslauf nicht wiederholt werden. Rechnungen der Kunden werden nur mit aktivierter Checkbox angezeigt.',

 /**
  *	MODULE: ProvBase	
  */
	//ModemController
	'Modem_NetworkAccess'			=> 'Deaktivieren/Aktivieren des Netzwerkzugriffs - Achtung: Wenn das Abrechnungsmodul installiert ist, wird diese Checkbox täglich überschrieben, je nach Gültigkeit der Tarif-Option - vorausgesetzt sie wurde nicht manuell gesetzt.', 
									//'Disable/Enable Network Access - Take Care: If Billing-Module is installed this Checkbox will be overwritten daily during check of valid Tariff Item',

 /**
  *	MODULE: ProvVoip	
  */
	//PhonenumberManagementController
	'PhonenumberManagement_CarrierIn'=> 'Im Falle einer neuen Nummer, setzen Sie diesrs Feld auf EnviaTEL',
									//'In case of a new number set this to EnviaTEL',
	'PhonenumberManagement_EkpIn'	=> 'Im Falle einer neuen Nummer, setzen Sie diesrs Feld auf EnviaTEL',
									//'In case of a new number set this to EnviaTEL',

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