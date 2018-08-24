<?php

return [
//SEARCH
		'Search_EnterKeyword' 		=> 'Suchbegriff eingeben',
		'Search_MatchesFor'			=> 'Ergebnisse für',
		'Search_In'					=> 'in der Tabelle',
//jQuery
		//Translations for this at https://datatables.net/plug-ins/i18n/
		'jQuery_sEmptyTable'		=> 'Keine Daten in der Tabelle vorhanden',
		'Search_Search ...'			=> 'Search ...',
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
		'jQuery_sLast'				=> ':sSortDescending aktivieren, um Spalte absteigend zu sortieren',
		'jQuery_sLast'				=> ': activate to sort column descending',
		'jQuery_All'				=> 'Alle',
		'jQuery_Print'				=> 'Drucken',
		'jQuery_colvis'				=> 'Spaltensichtbarkeit',
		'jQuery_colvisRestore'		=> 'Wiederherstellen',
		'jQuery_colvisReset'		=> 'Zurücksetzen',
		'jQuery_ExportTo'			=> 'Exportieren als',
                'jQuery_ImportCsv'              => 'CSV importieren',
//MENU
	//Main Menu
		'Menu_MainMenu' 			=> 'Hauptmenü',
		'Menu_Config Page'			=> 'Systemkonfiguration',
		'Menu_Logging'				=> 'Logs',
		'Menu_Product List'			=> 'Produktangebot',
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
		'Menu_Number Range'			=> 'Nummernkreis',
		'Menu_Configfiles' 			=> 'Konfigurationsdateien',
		'Menu_QoS' 					=> 'QoS',
		'Menu_CMTS' 				=> 'CMTS',
		'Menu_Ip-Pools' 			=> 'IP-Bereiche',
		'Menu_MTAs' 				=> 'MTAs',
		'Menu_Phonenumbers'			=> 'Telefonnummern',
		'Menu_PhoneTariffs'			=> 'Telefontarife',
		'Menu_Envia orders'			=> 'envia TEL Aufträge',
		'Menu_Envia contracts'		=> 'envia TEL Verträge',

	//User Settings
		'Menu_UserSettings'			=> 'Nutzereinstellungen',
		'Menu_UserGlobSettings' 	=> 'Globale Nutzereinstellungen',
		'Menu_Logout'				=> 'Ausloggen',
		'Menu_UserRoleSettings'		=> 'Nutzerrollen',

//HEADER
		'Menu_CDRs'					=> 'CDRs',
		'Menu_Tickets'				=> 'Tickets',
		'Menu_Comment'				=> 'Comments',
	//General
		'Header_GlobalSearch'		=> 'Systemweite Suche',
		'Header_Overview'			=> 'Übersicht',
		'Header_Assigned'			=> 'Zugewiesene',
		'Header_Create'				=> 'Erstellen',
	//Module specific
	//BillingBase
		//Product List
		'Header_Mpr'				=> 'MPR|MPRs',
		'Header_Modem Positioning Rule' => 'Modem Positioning Rule|Modem Positioning Rules',
		'Header_EditModem Positioning Rule' => 'Edit Modem Positioning Rule',
		'Header_MprGeopos'			=> 'MPR Geoposition|MPR Geopositions',
		'Header_Modem Positioning Rule Geoposition'	=> 'Modem Positioning Rule Geoposition|Modem Positioning Rule Geopositions',
		'Header_EditModem Positioning Rule Geoposition' => 'Edit Modem Positioning Rule Geoposition',
	//Global
		//Logs
		'Header_Logs'				=> 'Logs',
		'Header_EditLogs'			=> 'Log Details',
		'Header_Roles'				=> 'Rolle|Rollen',
	//Billing Base
		//Prduct Entry
		'Header_Product Entry'		=> 'Produkt|Produktangebot',
		'Header_Users'				=> 'User|Users',
		'Header_EditUsers'			=> 'Edit User',
		'Header_EditProduct Entry'	=> 'Produkt bearbeiten',
		//SEPA Accounts
		'Header_SEPA Account'		=> 'SEPA-Konto|SEPA-Konten', //Workaround decide which one to use
		'Header_EditSEPA Account'	=> 'SEPA-Konto bearbeiten',
		//CostCenter
		'Header_Global Configurations' => 'System Configurations',
		'Header_Global Config' 		=> 'Global Config|Global Configs',
		'Header_EditGlobal Config'	=> 'Edit Global Config',
		'Header_Billing Config' 	=> 'Modul Configuration for Billing',
		'Header_EditBilling Config' => 'Edit Modul Configuration for Billing',
		'Header_ProvVoip'			=> 'Modul Configuration for VOIP',
		'Header_EditProvVoip Config'=> 'Edit Modul Configuration for VOIP',
		'Header_Ccc Config'			=> 'Modul Configuration for Kundenkontrollzentrum',
		'Header_EditCcc Config'		=> 'Edit Modul Configuration for Kundenkontrollzentrum',
		'Header_Prov Base'			=> 'Modul Configuration for Provisioning',
		'Header_EditProv Base Config'=> 'Edit Modul Configuration for Provisioning',
		'Header_HfcBase'			=> 'Modul Configuration for HFC',
		'Header_EditHfc Base Config'=> 'Edit Modul Configuration for HFC',
		'Header_CostCenter'			=> 'Kostenstelle|Kostenstellen', //Workaround decide which one to use
		'Header_Cost Center'		=> 'Kostenstelle|Kostenstellen',
		'Header_EditCost Center'	=> 'Kostenstelle bearbeiten',
		//Company
		'Header_EditCompany'		=> 'Unternehmen bearbeiten',
		'Ability_Custom Abilities' 	=> 'Custom Abilities',
		'Ability_Authentication'		=> 'Authentication',
		'Ability_GlobalConfig' 		=> 'GlobalConfig',
		'Ability_Ccc'				=> 'Ccc',
		'Ability_BillingBase' 		=> 'BillingBase',
		'Ability_HFC' 				=> 'HFC',
		'Ability_ProvBase' 			=> 'ProvBase',
		'Ability_ProvVoip' 			=> 'ProvVoip',
		'Ability_ProvVoipEnvia'		=> 'ProvVoipEnvia',
		'Ability_VoipMon'			=> 'VoipMon',
		//Salesman
		'Header_EditSalesman'		=> 'Verkäufer bearbeiten',
		//Items
		'Header_Item'				=> 'Posten|Posten',
		'Header_EditItem'			=> 'Posten bearbeiten', //??
		//Numberrange
		'Header_NumberRange'		=> 'Nummernkreis|Nummernkreise',
	//SNMP Modul
		//Device
		'Header_Device'				=> 'Gerät|Geräte',
		'Header_EditDevice'			=> 'Gerät bearbeiten',
		//Device Type
		'Header_EditDevice Type'	=> 'Gerätetyp bearbeiten',
		'Header_Company'			=> 'Company|Companies',
	//Provisioning
		//Contract
		'Header_Salesman'			=> 'Salesman|Salesmen',
		'Header_Contract'			=> 'Vertrag|Verträge',
		'Header_EditContract'		=> 'Vertrag bearbeiten',
		'Header_SepaMandate'		=> 'SEPA-Mandat|SEPA-Mandate',
		//Modems
		'Header_Modems'				=> 'Modem|Modems', //workaround
		'Header_Invoice' 			=> 'Invoice|Invoices',
		'Header_EditModems'			=> 'Modem bearbeiten',
		'Header_Modem'				=> 'Modem|Modems',
		'Header_SEPA Mandate' 		=> 'SEPA Mandate',
		'Header_EditModem'			=> 'Modem bearbeiten',
		'Header_Settlement Run'		=> 'Settlement Run|Settlement Runs',
		'Header_EditSettlement Run' => 'Edit Settlement Run',
		//Endpoint
		'Header_Endpoints'			=> 'Endpunkt|Endpunkte',
		'Header_EditEndpoints'		=> 'Endpunkt bearbeiten',
		//Configfiles
		'Header_Configfiles'		=> 'Konfigurationsdatei|Konfigurationsdateien',
		'Header_EditConfigfiles'	=> 'Konfigurationsdatei bearbeiten',
		//QoS
		'Header_QoS'				=> 'QoS-Regel|QoS-Regeln',
		'Header_Device Type'		=> 'Device Type|Device Types',
		'Header_EditQoS'			=> 'QoS-Regel bearbeiten',
		//CMTS
		'Header_CMTS'				=> 'CMTS|CMTSs',
		'Header_EditCMTS'			=> 'CMTS bearbeiten',
		'Header_Config'				=> 'Konfigurationsvorschlag|Konfigurationsvorschläge',
		//IpPool
		'Header_IpPool'				=> 'IP-Bereich|IP-Bereiche',
		'Header_EditIpPool'			=> 'IP-Bereich bearbeiten',
		'Header_IP-Pools'			=> 'IP-Bereich|IP-Bereiche',
		'Header_EditIP-Pools'		=> 'IP-Bereich bearbeiten',
		// Tickets
		'Header_Ticket'				=> 'Ticket|Tickets',
		'Header_EditTicket'			=> 'Edit Ticket',
	//HFC
		//Topography
		'Header_Topography - Modems'=> 'Topografie - Modems',
		'navigate'					=> 'Navigieren',
		'draw box'					=> 'Box einzeichnen',
		'draw polygon'				=> 'Polygon einzeichnen',
		'modify'					=> 'Elemente ändern',
	//VOIP
		//MTA
		'Header_Mta'				=> 'MTA|MTAs',
		'Header_EditMta'			=> 'MTA bearbeiten',
		'Header_MTAs'				=> 'MTA|MTAs',
		'Header_EditMTAs'			=> 'MTA bearbeiten',
		//Phonenumber
		'Header_Phonenumber'		=> 'Telefonnummer|Telefonnummern',
		'Header_EditPhonenumber'	=> 'Telefonnummer bearbeiten',
		'Header_Phonenumbers'		=> 'Telefonnummer|Telefonnummern',
		'Header_EditPhonenumbers'	=> 'Telefonnummer bearbeiten',
		'Header_Tickets'			=> 'Ticket|Tickets',
		'Header_EditTickets'		=> 'Edit Tickets',
		//Phone tariff
		'Header_Comment'			=> 'Comment|Comments',
		'Header_EditComment'		=> 'Edit Comment',
		'Header_Phone tariffs'		=> 'Telefontarif|Telefontarife',
		'Header_EditPhone tariffs'	=> 'Telefontarif bearbeiten',
	//ProvVoipEnvia
		'Header_EnviaOrders'		=> 'envia TEL Auftrag|envia TEL Aufträge',
		'Header_EnviaContracts'		=> 'envia TEL Vertrag|envia TEL Verträge',

	//Header Relation
		// 'Assigned'  				=> 'Zugewiesene',
	//Header Controler index
		// 'SEPA Account' 				=> 'SEPA-Konten',
		// 'Create'					=> 'Erstelle ',
		// 'Edit'						=> 'Ändere ',

//BUTTON
		'Sign me in'				=> 'Einloggen',
		'Button_Create'				=> 'Erstelle',
		'Button_Delete'				=> 'Markierte Einträge löschen',
		'Button_Force Restart'		=> 'Neustart erzwingen',
		'Button_Save'				=> 'Speichern',
		'Button_Save / Restart'		=> 'Speichern / Neustart',
		'Button_manage'				=> 'Verwalten, schließt alle Basis und Sonderfähigkeiten mit ein. Schnellauswahl um alle Aktionen auf allen Seiten dieses Moduls auszuführen.',
		'Header_Tree Table' 		=> 'Tree Table|Tree Tables',
		'Header_EditTree Table'		=> 'Edit Tree Table',
		'Button_view'				=> 'Schnellauswahl um alle Seiten anzusehen. Grundfähigkeit um alle anderen Fähigkeiten auszuführen.',
		'Button_create'				=> 'Schnellauswahl um auf allen Seiten neue Elemente zu erstellen.',
		'Button_update'				=> 'Schnellauswahl um auf allen Seiten Elemente zu verändern.',
		'Button_delete'				=> 'Schnellauswahl um auf allen Seiten Elemente zu löschen.',
		'Button_Create Product Entry'	=> 'Neues Produkt',
		//CostCenter
		'Button_Create Cost Center' 	=> 'Neue Kostenstelle', //Workaround decide which one to use
		'Button_Create CostCenter' 		=> 'Neue Kostenstelle',
		'Button_Create Users'			=> 'Create User',
		//Company
		'Button_Create Company'			=> 'Neues Unternehmen',
		//Salesman
		'Button_Create Salesman'		=> 'Neuer Verkäufer',
		//Item
		'Button_Create Item'			=> 'Neuer Posten',
		'sr_dl_logs' 					=> 'Gesamtes Logfile herunterladen',
		//Numberrange
		'Button_Create NumberRange'		=> 'Neuer Nummernkreis',

	//SNMP Modul
		//Device
		'Button_Create Device'			=> 'Neues Gerät',
		//Device Type
		'Button_Create Device Type'		=> 'Neuer Gerätetyp',
		'Button_Create Phonenumber'		=> 'Create Phonenumber',
		'Button_Create Tree Table'		=> 'Create Tree Table',
		'Button_Create Modem Positioning Rule' => 'Create Modem Positioning Rule',
		'Button_Create Mpr'				=> 'Create Modem Positioning Rule Geoposition',
		'Button_Create Modem Positioning Rule Geoposition' => 'Create MprGeopos',
		'Button_Create MprGeopos'		=> 'Create MprGeopos',
		'Button_Create Tickets'		=> 'Create Tickets',

		'Button_Create Comment'		=> 'Create Comment',
		//SEPA-Konto
		'Assigned'  				=> 'Assigned',
		'Button_Create SEPA Account'	=> 'Neues SEPA-Konto', //Workaround decide which one to use
		'SEPA Account' 				=> 'SEPA-Account',
		'Create'					=> 'Create ',
		'Edit'						=> 'Edit ',
		'Button_Create SepaAccount'		=> 'Neues SEPA-Konto',

	//Provisioning
		//Contract
		'Button_Create Contract'		=> 'Create Contract',
		'Button_Create SepaMandate'		=> 'Create SEPA-Mandate',
		// //Modems
		'Button_Create Modem'			=> 'Create Modem',
		// //Endpoints
		'Button_Create Endpoints'		=> 'Create Endpoints',
		//MTA
		'Button_Create Mta'				=> 'Neues MTA',
		// //Phonenumber
		'Button_Create Phone tariffs'	=> 'Neuer Telefontarif',
		// //Configfiles
		'Button_Create Configfiles'		=> 'Create Configfile',
		// //QoS
		'Button_Create Settlement Run'	=> 'Create Settlement Run',
		'Button_Rerun Accounting Command' => 'Rerun Accounting Command',
		'Button_Create QoS'				=> 'Create QoS-Rule',
		// //CMTS
		'Button_Create CMTS'			=> 'Create CMTS',
		// //IpPool
		'Button_Create IpPool'			=> 'Create IpPool', //workaround
		'Button_Create IP-Pools'		=> 'Create IpPools',

// DASHBOARD
	'Dashboard_Contracts'			=> 'AKTIVE VERTRÄGE',
	'Dashboard_ContractAnalytics'	=> 'Analyse Verträge (letzte 12 Monate)',
	'Dashboard_NoContracts'			=> 'Keine Verträge vorhanden.',
	'Dashboard_Income'				=> 'ERLÖSE',
	'Dashboard_IncomeAnalytics'		=> 'Erlöse Detailübersicht',
	'Dashboard_Date'				=> 'DATUM',
	'Dashboard_LinkDetails'			=> 'Zeige Details',
	'Dashboard_Other'				=> 'Sonstiges',
	'Dashboard_Tickets' 			=> 'NEUE TICKETS',
	'Dashboard_NoTickets' 			=> 'Keine neuen Tickets.',
	'Dashboard_Quickstart' 			=> 'SCHNELLSTART',

//
// Numberrange
//
	//Type
	'Numberrange_Type_contract' => 'Vertrag',
	'Numberrange_Type_invoice' => 'Rechnung',

//
// Contract
	'Dashboard_Net Income'			=> 'NETTOERLÖSE',
//
	'Contract_Numberrange_Failure' => 'Keine freie Vertragsnummer für die gewählte Kostenstelle gefunden.',

	'Ticket_State_New' => 'Neu',
	'Ticket_State_In Process' => 'In Bearbeitung',
	'Ticket_State_Closed' => 'Geschlossen',
	'Ticket_Type_General' => 'Allgemein',
	'Ticket_Type_Technical' => 'Technik',
	'Ticket_Type_Accounting' => 'Buchhaltung',
	'Ticket_Priority_Trivial' => 'Niedrig',
	'Ticket_Priority_Minor' => 'Medium',
	'Ticket_Priority_Major' => 'Hoch',
	'Ticket_Priority_Critical' => 'Kritisch',
	'Numberrange_Start' => 'Beginn',
	'Numberrange_End' => 'Ende',
	'Numberrange_Suffix' => 'Suffix',
	'Numberrange_Prefix' => 'Präfix',
	'Numberrange_Type' => 'Typ',
];
