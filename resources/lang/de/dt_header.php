<?php 
return [
// Index DataTable Header
	// GuiLog
	'guilog.created_at' => 'Zeitpunkt',
	'guilog.username' => 'Nutzer',
	'guilog.method' => 'Aktion',
	'guilog.model' => 'Model',
	'guilog.model_id' => 'Model ID',
	// Company	
	'company.name' => 'Unternehmen', 
	'company.city' => 'Stadt',
	'company.phone' => 'Telefonnummer',
	'company.mail' => 'E-Mail',
	// Costcenter
	'costcenter.name' => 'Kostenstelle',
	'costcenter.number' => 'Nummer',
	//Invoices
	'invoice.type' => 'Typ',
	'invoice.year' => 'Jahr', 
	'invoice.month' => 'Monat',
	//Item //**

	// Product
	'product.type' => 'Typ',
	'product.name' => 'Produkt',
	'product.price' => 'Preis',
	// Salesman
	'salesman.id' => 'ID',
	'salesman.lastname' => 'Nachname',
	'salesman.firstname' => 'Vorname',
	// SepaAccount
	'sepaaccount.name' => "Kontoname",
	'sepaaccount.institute' => 'Bank',
	'sepaaccount.iban' => 'IBAN',
	// SepaMandate
	'sepamandate.sepa_holder' => 'Kontoinhaber',
	'sepamandate.sepa_valid_from' => 'Gültig ab',
	'sepamandate.sepa_valid_to' => 'Gültig bis',
	'sepamandate.reference' => 'Kontoreferenz',
	// SettlementRun
	'settlementrun.year' => 'Jahr',
	'settlementrun.month' => 'Monat',
	'settlementrun.created_at' => 'Erstellt am',
	'verified' => 'Überprüft?',
	// MPR
	'mpr.name' => 'Name',
	// NetElement
	'netelement.id' => 'ID',	
	'netelement.name' => 'Netzelement',
	'netelement.ip' => 'IP Adresse',
	'netelement.state' => 'Status',
	'netelement.pos' => 'Position',
	// NetElementType
	'netelementtype.name' => 'Netzelementtyp',
	//HfcSnmp
	'parameter.oid.name' => 'OID Name',
	//Mibfile
	'mibfile.id' => 'ID',
	'mibfile.name' => 'Mibfilename',
	'mibfile.version' => 'Version',
	// OID
	'oid.name_gui' => 'GUI Beschriftung', 
	'oid.name' => 'OID Name',
	'oid.oid' => 'OID',
	'oid.access' => 'Schreibschutz',
	//SnmpValue
	'snmpvalue.oid_index' => 'OID Index',
	'snmpvalue.value' => 'OID Wert',
	// MAIL
	'email.localpart' => 'Lokalteil',
	'email.index' => 'Primäre E-Mail Adresse',
	'email.greylisting' => 'Greylisting Aktiv?',
	'email.blacklisting' => 'E-Mail auf Blacklist?',
	'email.forwardto' => 'Weiterleiten an:',
	// CMTS
	'cmts.id' => 'ID',
	'cmts.hostname' => 'Hostname',
	'cmts.ip' => 'IP',
	'cmts.company' => 'Hersteller',
	'cmts.type' => 'Typ',
	// Contract
	'contract.number' => 'Vertragsnummer',
	'contract.firstname' => 'Vorname',
	'contract.lastname' => 'Nachname',
	'contract.zip' => 'Postleitzahl',
	'contract.city' => 'Stadt',
	'contract.street' => 'Straße',
	'contract.house_number' => 'Hausnummer',
	'contract.district' => 'Bezirk',
	'contract.contract_start' => 'Vertragsanfang',
	'contract.contract_end' => 'Vertragsende',
	// Domain
	'domain.name' => 'Domain',
	'domain.type' => 'Typ',
	'domain.alias' => 'Alias',
	// Endpoint
	'endpoint.hostname' => 'Hostname',
	'endpoint.mac' => 'MAC',
	'endpoint.description' => 'Beschreibung',
	// IpPool
	'ippool.id' => 'ID',
	'ippool.type' => 'Typ',
	'ippool.net' => 'Netz',
	'ippool.netmask' => 'Netzmaske',
	'ippool.router_ip' => 'Router IP',
	'ippool.description' => 'Beschreibung',
	// Modem
	'modem.id' => 'Modemnummer',
	'modem.mac' => 'MAC Adresse',
	'modem.name' => 'Modemname',
	'modem.lastname' => 'Nachname',
	'modem.city' => 'Stadt',
	'modem.street' => 'Straße',
	'modem.us_pwr' => 'US level',
	'contract_valid' => 'Vertrag gültig?',
	// QoS
	'qos.name' => 'QoS Name',
	'qos.ds_rate_max' => 'Maximale DS Geschwindigkeit',
	'qos.us_rate_max' => 'Maximale US Geschwindigkeit',
	// Mta
	'mta.hostname' => 'Hostname',
	'mta.mac' => 'MAC-Adresse',
	'mta.type' => 'Provisionierungstyp',
	// Configfile
	'configfile.name' => 'Konfiguartionsdatei',	
	// PhonebookEntry
	'phonebookentry.id' => 'ID',
	// Phonenumber
	'phonenumber.prefix_number' => 'Vorwahl',
	'phonenumber.number' => 'Nummer',
	'phonenr_act' => 'Aktivierungsdatum',
	'phonenr_deact' => 'Deaktivierungsdatum',
	'phonenr_state' => 'Status',
	// Phonenumbermanagement
	'phonenumbermanagement.id' => 'ID',
	// PhoneTariff
	'phonetariff.name' => 'Telefontarif',
	'phonetariff.type' => 'Typ',
	'phonetariff.description' => 'Beschreibung',
	'phonetariff.voip_protocol' => 'VOIP Protokoll',
	'phonetariff.usable' => 'Verfügbar',
	// ENVIA enviaorder
	'enviaorder_ordertype'  => 'Bestelltyp',
	'enviaorder_orderstatus'  => 'Bestellstatus',
	'enviaorder_escalation_level' => 'Statuslevel',
	'enviaorder_contract_nr'  => 'Vertrag',
	'enviaorder_modem_nr'  => 'Modem',
	'enviaorder_phonenumber_nrs'  => 'Telefonnummern',
	'enviaorder.created_at'  => 'Erstellt am',
	'enviaorder.updated_at'  => 'Bearbeitet am',
	'enviaorder.orderdate'  => 'Bestelldatum',
	'enviaorder_current'  => 'Bearbeitung notwendig?',
	'enviaorder_solve_link' => 'Als gelöst markeren?',
	// CDR
	'cdr.calldate' => 'Anrufzeitpunkt',
	'cdr.caller' => 'Anrufer',
	'cdr.called' => 'Angerufener',
	'cdr.mos_min_mult10' => 'minimaler MOS',
];
