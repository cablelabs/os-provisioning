<?php

return [
//SEARCH
		'Search_EnterKeyword' 		=> 'Enter Keyword',
		'Search_MatchesFor'			=> 'Matches for',
		'Search_In'					=> 'in',
//jQuery
		//Translations for this at https://datatables.net/plug-ins/i18n/
		'jQuery_sEmptyTable'		=> 'No data available in table',
		'jQuery_sInfo'				=> 'Showing _START_ to _END_ of _TOTAL_ entries',
		'jQuery_sInfoEmpty'			=> 'Showing 0 to 0 of 0 entries',
		'jQuery_sInfoFiltered'		=> '(filtered from _MAX_ total entries)',
		'jQuery_sInfoPostFix'		=> '',
		'jQuery_sInfoThousands'		=> ',',
		'jQuery_sLengthMenu'		=> 'Show _MENU_ entries',
		'jQuery_sLoadingRecords'	=> 'Loading...',
		'jQuery_sProcessing'		=> 'Processing...',
		'jQuery_sSearch'			=> 'Search:',
		'jQuery_sZeroRecords'		=> 'No matching records found',
		'jQuery_PaginatesFirst'		=> 'First',
		'jQuery_PaginatesPrevious'	=> 'Previous',
		'jQuery_PaginatesNext'		=> 'Next',
		'jQuery_PaginatesLast'		=> 'Last',
		'jQuery_sLast'				=> ': activate to sort column ascending',
		'jQuery_sLast'				=> ': activate to sort column descending',
		'jQuery_All'				=> 'All',

//MENU
	//Main Menu
		'Menu_MainMenu' 			=> 'Main Menu',
		'Menu_Config Page'			=> 'Global Config Page',
		'Menu_Logging'				=> 'Logging',
		'Menu_Product List'			=> 'Product List',
		'Menu_SEPA Accounts'		=> 'SEPA Accounts',
		'Menu_Settlement Run'		=> 'Settlement Run',
		'Menu_Cost Center'			=> 'Cost Center',
		'Menu_Companies'			=> 'Companies',
		'Menu_Salesmen'				=> 'Salesmen',
		'Menu_Tree Table'			=> 'Tree Table',
		'Menu_Devices'				=> 'Devices',
		'Menu_DeviceTypes'			=> 'DeviceTypes',
		'Menu_Contracts'			=> 'Contracts',
		'Menu_Modems'				=> 'Modems',
		'Menu_Endpoints'			=> 'Endpoints',
		'Menu_Configfiles' 			=> 'Configfiles',
		'Menu_QoS' 					=> 'QoS',
		'Menu_CMTS' 				=> 'CMTS',
		'Menu_Ip-Pools' 			=> 'IP-Pools',
		'Menu_MTAs' 				=> 'MTAs',
		'Menu_Phonenumbers'			=> 'Phonenumbers',
		'Menu_PhoneTariffs'			=> 'PhoneTariffs',

	//User Settings
		'Menu_UserSettings'     	=> 'Nutzereinstellungen',
	    'Menu_UserGlobSettings' 	=> 'Globale Nutzereinstellungen',
	    'Menu_Logout'               => 'Ausloggen',

//HEADER
	//General
	    'Header_GlobalSearch'		=> 'Systemweite Suche',
	    'Header_Overview'			=> 'Übersicht',
	    'Header_Assigned'			=> 'Zugewiesene',
	    'Header_Create'				=> 'Erstellen',
	//Module specific

	//Global
	    //Logs
	    'Header_Logs'				=> 'Logs',
	    'Header_EditLogs'			=> 'Log Detail',
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
	    'Header_EditCompany'		=> 'Firma bearbeiten',
		//Salesman
	    'Header_EditSalesman'		=> 'Verkäufer bearbeiten',
	    //Items
	    'Header_Item'				=> 'Produkt|Produkte',
	    'Header_EditItem'			=> 'Produkt bearbeiten', //??
	//SNMP Modul
	    //Device
	    'Header_Device'				=> 'Gerät|Geräte',
	    'Header_EditDevice'			=> 'Gerät bearbeiten',
	    //Device Type
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

	//Header Relation
		'Assigned'  				=> 'Zugewiesene',
	//Header Controler index
		'SEPA Account' 				=> 'SEPA-Konten',
		'Create'					=> 'Erstelle ',
		'Edit'						=> 'Ändere ',

//BUTTON
		'Sign me in'				=> 'Einloggen',
		'Button_Create'				=> 'Erstelle',
		'Button_Delete'				=> 'Markierte Einträge löschen',
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
		'Button_Create Company'			=> 'Neue Firma',
		//Salesman
		'Button_Create Salesman'		=> 'Neuer Verkäufer',
		//Item
		'Button_Create Item'			=> 'Neues Produkt',

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
		'Button_Create Mta'    			=> 'Neues MTA',
		//Phonenumber
		'Button_Create Phonenumber'		=> 'Neue Telefonnummer',
		//Phone tariff
		'Button_Create Phone tariffs'	=> 'Neuer Telefontarif',
];