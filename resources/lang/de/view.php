<?php

return [

//
//SEARCH
//
		'Search_EnterKeyword' 		=> 'Suchbegriff eingeben',
		'Search_MatchesFor'			=> 'Ergebnisse für',
		'Search_In'					=> 'in der Tabelle',
		'Search_Search ...'			=> 'Suchbegriff eingeben ...',

//
//jQuery
//
		//Translations for this at https://datatables.net/plug-ins/i18n/
		'jQuery_sEmptyTable'		=> 'Keine Daten in der Tabelle vorhanden',
		'jQuery_sInfo'				=> '_START_ bis _END_ von _TOTAL_ Einträgen',
		'jQuery_sInfoEmpty'			=> '0 bis 0 von 0 Einträgen',
		'jQuery_sInfoFiltered'		=> '(gefiltert von _MAX_ Einträgen)',
		'jQuery_sInfoPostFix'		=> '',
		'jQuery_sInfoThousands'		=> '.',
		'jQuery_sLengthMenu'		=> '_MENU_ Einträge anzeigen',
		'jQuery_sLoadingRecords'	=> 'Wird geladen...',
		'jQuery_sProcessing'		=> 'Bitte warten...',
		'jQuery_sSearch'			=> 'Suchen',
		'jQuery_sZeroRecords'		=> 'Keine Einträge vorhanden.',
		'jQuery_PaginatesFirst'		=> 'Erste',
		'jQuery_PaginatesPrevious'	=> 'Zurück',
		'jQuery_PaginatesNext'		=> 'Nächste',
		'jQuery_PaginatesLast'		=> 'Letzte',
		'jQuery_sLast'				=> ':sSortAscending aktivieren, um Spalte aufsteigend zu sortieren',
		'jQuery_sLast'				=> ':sSortDescending aktivieren, um Spalte absteigend zu sortieren',
		'jQuery_All'				=> 'Alle',

//
//MENU
//
	//Main Menu
		'Menu_MainMenu' 			=> 'Hauptmenü',
		'Menu_Config Page'			=> 'Systemkonfiguration',
		'Menu_Logging'				=> 'Logs',
		'Menu_Product List'			=> 'Produktliste',
		'Menu_SEPA Accounts'		=> 'SEPA-Konten',
		'Menu_Settlement Run'		=> 'Abrechnungslauf',
		'Menu_Cost Center'			=> 'Kostenstelle',
		'Menu_Companies'			=> 'Unternehmen',
		'Menu_Salesmen'				=> 'Verkäufer',
		'Menu_Tree Table'			=> 'Baumdiagramm',
		'Menu_Devices'				=> 'Geräte',
		'Menu_DeviceTypes'			=> 'Gerätetypen',
		'Menu_Contracts'			=> 'Verträge',
		'Menu_Modems'				=> 'Modems',
		'Menu_Endpoints'			=> 'Endpunkte',
		'Menu_Configfiles' 			=> 'Konfigurationsdateien',
		'Menu_QoS' 					=> 'QoS',
		'Menu_CMTS' 				=> 'CMTS',
		'Menu_Ip-Pools' 			=> 'IP-Bereiche',
		'Menu_MTAs' 				=> 'MTAs',
		'Menu_Phonenumbers'			=> 'Telefonnummern',
		'Menu_PhoneTariffs'			=> 'Telefontarife',
		'Menu_Envia orders'			=> 'Envia Bestellungen',
		'Menu_CDRs'					=> 'EVNs',

	//User Settings
		'Menu_UserSettings'			=> 'Nutzereinstellungen',
		'Menu_UserGlobSettings'		=> 'Globale Nutzereinstellungen',
		'Menu_Logout'				=> 'Ausloggen',
		'Menu_UserRoleSettings'		=> 'Nutzerrollen',

//
//HEADER
//
	//General
		'Header_GlobalSearch'		=> 'Systemweite Suche',
		'Header_Overview'			=> 'Übersicht',
		'Header_Assigned'			=> 'Zugewiesene',
		'Header_Create'				=> 'Erstellen',
		'Header_Users'				=> 'Benutzer|Benutzer',
		'Header_EditUsers'			=> 'Benutzer bearbeiten',
	//
	//Module specific
	//
	//Global'Config
		//Global
		'Header_Global Configurations' => 'Systemkonfiguration',
		'Header_Global Config'		=> 'Globale Konfiguration|Globale Konfigurationen',
		'Header_EditGlobal Config'	=> 'Globale Konfiguration bearbeiten',
		'Header_Billing Config'		=> 'Moduleinstellungen für Billing Base',
		'Header_EditBilling Config' => 'Moduleinstellungen für Billing Base bearbeiten',
		'Header_ProvVoip'			=> 'Moduleinstellungen VOIP',
		'Header_EditProvVoip Config'=> 'Moduleinstellungen VOIP bearbeiten',
		'Header_Ccc Config'			=> 'Moduleinstellungen Kundenkontrollzentrum',
		'Header_EditCcc Config'		=> 'Moduleinstellungen Kundenkontrollzentrum bearbeiten',
		'Header_Prov Base'			=> 'Moduleinstellungen Provisioning',
		'Header_EditProv Base Config'=> 'Moduleinstellungen Provisioning bearbeiten',
		'Header_HfcBase'			=> 'Moduleinstellungen HFC',
		'Header_EditHfc Base Config'=> 'Moduleinstellungen HFC bearbeiten',
		//Logs
		'Header_Logs'				=> 'Logs',
		'Header_EditLogs'			=> 'Log Details',
	//Billing Base
		//Prduct Entry
		'Header_Product Entry'		=> 'Produkt|Produktangebot',
		'Header_EditProduct Entry'	=> 'Produkt bearbeiten',
		//SEPA Accounts
		'Header_SEPA Account'		=> 'SEPA-Konto|SEPA-Konten', //Workaround decide which one to use
		'Header_EditSEPA Account'	=> 'SEPA-Konto bearbeiten',
		//CostCenter
		'Header_CostCenter'			=> 'Kostenstelle|Kostenstellen', //Workaround decide which one to use
		'Header_Cost Center'		=> 'Kostenstelle|Kostenstellen',
		'Header_EditCost Center'	=> 'Kostenstelle bearbeiten',
		//Company
		'Header_Company'			=> 'Unternehmen|Unternehmen',
		'Header_EditCompany'		=> 'Unternehmen bearbeiten',
		//Salesman
		'Header_Salesman'			=> 'Verkäufer|Verkäufer',
		'Header_EditSalesman'		=> 'Verkäufer bearbeiten',
		//Items
		'Header_Item'				=> 'Posten|Posten',
		'Header_EditItem'			=> 'Posten bearbeiten',
		// Invoice
		'Header_Invoice' 			=> 'Rechnung|Rechnungen',

		//Settlement Run
		'Header_Settlement Run'		=> 'Abrechnungslauf|Abrechnungsläufe',
		'Header_EditSettlement Run' => 'Abrechnungslauf bearbeiten',
	//SNMP Modul
		//Device
		'Header_Device'				=> 'Gerät|Geräte',
		'Header_EditDevice'			=> 'Gerät bearbeiten',
		//Device Type
		'Header_Device Type'		=> 'Gerätetyp|Gerätetypen',
		'Header_EditDevice Type'	=> 'Gerätetyp bearbeiten',
	//Provisioning
		//Contract
		'Header_Contract'			=> 'Vertrag|Verträge',
		'Header_EditContract'		=> 'Vertrag bearbeiten',
		'Header_SepaMandate'		=> 'SEPA-Mandat|SEPA-Mandate',
		//Modems
		'Header_Modems'				=> 'Modem|Modems', //workaround
		'Header_EditModems'			=> 'Modem bearbeiten',
		'Header_Modem'				=> 'Modem|Modems',
		'Header_EditModem'			=> 'Modem bearbeiten',
		//Endpoint
		'Header_Endpoints'			=> 'Endpunkt|Endpunkte',
		'Header_EditEndpoints'		=> 'Endpunkt bearbeiten',
		//Configfiles
		'Header_Configfiles'		=> 'Konfigurationsdatei|Konfigurationsdateien',
		'Header_EditConfigfiles'	=> 'Konfigurationsdatei bearbeiten',
		//QoS
		'Header_QoS'				=> 'QoS-Regel|QoS-Regeln',
		'Header_EditQoS'			=> 'QoS-Regel bearbeiten',
		//CMTS
		'Header_CMTS'				=> 'CMTS|CMTSs',
		'Header_EditCMTS'			=> 'CMTS bearbeiten',
		//IpPool
		'Header_IpPool'				=> 'IP-Bereich|IP-Bereiche', //workaround
		'Header_EditIpPool'			=> 'IP-Bereich bearbeiten',
		'Header_IP-Pools'			=> 'IP-Bereich|IP-Bereiche',
		'Header_EditIP-Pools'		=> 'IP-Bereich bearbeiten',

	//VOIP
		//MTA
		'Header_Mta'				=> 'MTA|MTAs',
		'Header_EditMta'			=> 'MTA bearbeiten',
		'Header_MTAs'				=> 'MTA|MTAs',
		'Header_EditMTAs'			=> 'MTA bearbeiten',
		//Phonenumber
		'Header_Phonenumber'		=> 'Telefonnummer|Telefonnummern', //workaround
		'Header_EditPhonenumber'	=> 'Telefonnummer bearbeiten',
		'Header_Phonenumbers'		=> 'Telefonnummer|Telefonnummern',
		'Header_EditPhonenumbers'	=> 'Telefonnummer bearbeiten',
		//Phone tariff
		'Header_Phone tariffs'		=> 'Telefontarif|Telefontarife',
		'Header_EditPhone tariffs'	=> 'Telefontarif bearbeiten',
	//ProvVoipEnvia
		'Header_EnviaOrders'		=> 'Envia Bestellung|Envia Bestellungen',

	//HFC
		//Tree
		'Header_Tree Table' 		=> 'Baumelement|Baumtabelle', //??
		'Header_EditTree Table'		=> 'Baumelement bearbeiten', //??
		//MPR
		'Header_Mpr'				=> 'Modem Positionierungsregel|Modem Positionierungsregeln',
		'Header_Modem Positioning Rule' => 'Modem Positionierungsregel|Modem Positionierungsregeln',
		'Header_EditModem Positioning Rule' => 'Modem Positionierungsregel bearbeiten',
		'Header_MprGeopos'			=> 'Geoposition für Modem Positionierungsregel|Geopositionen für Modem Positionierungsregel',
		'Header_Modem Positioning Rule Geoposition'	=> 'Geoposition für Modem Positionierungsregel|Geopositionen für Modem Positionierungsregel',
		'Header_EditModem Positioning Rule Geoposition' => 'Geoposition für Modem Positionierungsregel bearbeiten',
	//Header Relation
		'Assigned'  				=> 'Zugewiesene',
	//Header Controler index
		'SEPA Account' 				=> 'SEPA-Konten',
		'Create'					=> 'Erstelle ',
		'Edit'						=> 'Ändere ',

//
//BUTTON
//
		'Button_Create Users'		=> 'Neuer Benutzer',
		'Sign me in'				=> 'Einloggen',
		'Button_Create'				=> 'Erstelle',
		'Button_Delete'				=> 'Markierte Einträge löschen',
		'Button_Force Restart'		=> 'Neustart erzwingen',
		'Button_Save'				=> 'Speichern',
		'Button_Save / Restart'		=> 'Speichern / Neustart',
	//BillingBase
		//Product List
		'Button_Create Product Entry'	=> 'Neues Produkt',
		//SEPA-Konto
		'Button_Create SEPA Account'	=> 'Neues SEPA-Konto', //Workaround decide which one to use
		'Button_Create SepaAccount'		=> 'Neues SEPA-Konto',
		//CostCenter
		'Button_Create Cost Center' 	=> 'Neue Kostenstelle', //Workaround decide which one to use
		'Button_Create CostCenter' 		=> 'Neue Kostenstelle',
		//Company
		'Button_Create Company'			=> 'Neues Unternehmen',
		//Salesman
		'Button_Create Salesman'		=> 'Neuer Verkäufer',
		//Item
		'Button_Create Item'			=> 'Neuer Posten',

		//Settlement Run
		'Button_Create Settlement Run'	=> 'Neuer Abrechnungslauf',
		'Button_Rerun Accounting Command for current Month'	=> 'Vorgang für den aktuellen Monat erneut ausführen',

	//SNMP Modul
		//Device
		'Button_Create Device'			=> 'Neues Gerät',
		//Device Type
		'Button_Create Device Type'		=> 'Neuer Gerätetyp',

	//Provisioning
		//Contract
		'Button_Create Contract'		=> 'Neuer Vertrag',
		'Button_Create SepaMandate'		=> 'Neues SEPA-Mandat',
		//Modems
		'Button_Create Modem'			=> 'Neues Modem',
		//Endpoints
		'Button_Create Endpoints'		=> 'Neuer Endpunkt',
		//Configfiles
		'Button_Create Configfiles'		=> 'Neue Konfigurationsdatei',
		//QoS
		'Button_Create QoS'				=> 'Neue QoS-Regel',
		//CMTS
		'Button_Create CMTS'			=> 'Neue CMTS',
		//IpPool
		'Button_Create IpPool'			=> 'Neuer IP-Bereich', //workaround
		'Button_Create IP-Pools'		=> 'Neuer IP-Bereich',


	//VOIP
		//MTA
		'Button_Create Mta'				=> 'Neues MTA',
		//Phonenumber
		'Button_Create Phonenumber'		=> 'Neue Telefonnummer',
		//Phone tariff
		'Button_Create Phone tariffs'	=> 'Neuer Telefontarif',
	//HFC
		//Tree Table
		'Button_Create Tree Table'		=> 'Neues Baumelement',
		//MPR
		'Button_Create Modem Positioning Rule' => 'Neue Modem Positionierungsregel',
		'Button_Create Mpr'				=> 'Neue MPR',
		'Button_Create Modem Positioning Rule Geoposition' => 'Neue Geoposition für Modem Positionierungsregel',
		'Button_Create MprGeopos'		=> 'Neue MPR-Geoposition',

//
// DASHBOARD
//
	'Dashboard_Contracts'			=> 'AKTIVE VERTRÄGE',
	'Dashboard_ContractAnalytics'	=> 'Analyse Verträge (letzte 12 Monate)',
	'Dashboard_NoContracts'			=> 'Keine Verträge vorhanden.',
	'Dashboard_Income'				=> 'ERLÖSE',
	'Dashboard_Net Income'			=> 'NETTOERLÖSE',
	'Dashboard_IncomeAnalytics'		=> 'Erlöse Detailübersicht',
	'Dashboard_Date'				=> 'DATUM',
	'Dashboard_LinkDetails'			=> 'Zeige Details',
	'Dashboard_Other'				=> 'Sonstiges',
];
