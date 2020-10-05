<?php

return [
    // Index DataTable Header
    'amount' => 'Betrag',
    'city' => 'Stadt',
    'connected' => 'Angeschlossen',
    'connection_type' => 'Anschlusstyp',
    'deprecated' => 'Veraltet',
    'district' => 'Ortsteil',
    'email' => 'Email',
    'expansion_degree' => 'Ausbaugrad',
    'floor' => 'Etage',
    'group_contract' => 'Gruppen-vertrag',
    'house_nr' => 'Hausnr',
    'iban' => 'IBAN',
    'id'            => 'ID',
    'name' => 'Name',
    'number' => 'Nummer',
    'occupied' => 'Bewohnt',
    'prio'          => 'Priorität',
    'street' => 'Straße',
    'type' => 'Typ',
    'zip' => 'PLZ',
    'sum' => 'Summe',
    'apartment' => [
        'number' => 'Nummer',
        'connected' => 'Angeschlossen',
        'occupied' => 'Bewohnt',
    ],
    'contact' => [
        'administration' => 'Hausverwaltung',
    ],
    // Auth
    'users' => [
        'login_name' => 'Loginname',
        'first_name' => 'Vorname',
        'last_name' => 'Nachname',
        'geopos_updated_at' => 'Letztes Geopos-Update',
    ],
    'roles.title' => 'Name',
    'roles.rank' => 'Rang',
    'roles.description' => 'Beschreibung',
    // GuiLog
    'guilog.created_at' => 'Zeitpunkt',
    'guilog.username' => 'Nutzer',
    'guilog.method' => 'Aktion',
    'guilog.model' => 'Tabelle',
    'guilog.model_id' => 'ID',
    // Company
    'company.name' => 'Unternehmen',
    'company.city' => 'Stadt',
    'company.phone' => 'Telefonnummer',
    'company.mail' => 'E-Mail',
    // Costcenter
    'costcenter.name' => 'Kostenstelle',
    'costcenter.number' => 'Nummer',
    'debt' => [
        'date' => 'Belegdatum',
        'due_date' => 'Fälligkeitsdatum',
        'indicator' => 'Mahnkennzeichen',
        'missing_amount' => 'Ausstand',
        'number' => 'OP-Nummer',
        'total_fee' => 'Gebühren',
        'voucher_nr' => 'Belegnr',
    ],
    //Invoices
    'invoice.type' => 'Typ',
    'invoice.year' => 'Jahr',
    'invoice.month' => 'Monat',
    //Item
    'item.valid_from' => 'Posten Gültig ab',
    'item.valid_from_fixed' => 'Ab Startdatum aktiv',
    'item.valid_to' => 'Posten Gültig bis',
    'item.valid_to_fixed' => 'Festes Enddatum',
    'fee' => 'Gebühr',
    'product' => [
        'proportional' => 'Anteilig',
        'type' => 'Typ',
        'name' => 'Produkt',
        'price' => 'Preis',
    ],
    // Salesman
    'salesman.id' => 'ID',
    'salesman_id' 		=> 'Verkäufer-ID',
    'salesman_firstname' => 'Vorname',
    'salesman_lastname' => 'Nachname',
    'commission in %' 	=> 'Provision in %',
    'contract_nr' 		=> 'Kundennr',
    'contract_name' 	=> 'Kunde',
    'contract_start' 	=> 'Vertragsbeginn',
    'contract_end' 		=> 'Vertragsende',
    'product_name' 		=> 'Produkt',
    'product_type' 		=> 'Produkttyp',
    'product_count' 	=> 'Anzahl',
    'charge' 			=> 'Gebühr',
    'salesman.lastname' => 'Nachname',
    'salesman.firstname' => 'Vorname',
    'salesman_commission' => 'Provision',
    'sepaaccount_id' 	=> 'SEPA-Konto',
    'sepaaccount' => [
        'iban' => 'IBAN',
        'institute' => 'Bank',
        'name' => 'Kontoname',
        'template_invoice' => 'Rechnungsvorlage',
    ],
    // SepaMandate
    'sepamandate.holder' => 'Kontoinhaber',
    'sepamandate.valid_from' => 'Gültig ab',
    'sepamandate.valid_to' => 'Gültig bis',
    'sepamandate.reference' => 'Mandatsreferenz',
    'sepamandate.disable' => 'Deaktiviert',
    // SettlementRun
    'settlementrun.year' => 'Jahr',
    'settlementrun.month' => 'Monat',
    'settlementrun.created_at' => 'Erstellt am',
    'settlementrun.executed_at' => 'Durchgeführt am',
    'verified' => 'Überprüft?',
    // MPR
    'mpr.name' => 'Name',
    'mpr.id'    => 'ID',
    // NetElement
    'netelement.id' => 'ID',
    'netelement.name' => 'Netzelement',
    'netelement.ip' => 'IP Adresse',
    'netelement.state' => 'Status',
    'netelement.pos' => 'Position',
    'netelement.options' => 'Optionen',

    // NetElementType
    'netelementtype.name' => 'Netzelementtyp',
    //HfcSnmp
    'parameter.oid.name' => 'OID Bezeichnung',
    //Mibfile
    'mibfile.id' => 'ID',
    'mibfile.name' => 'Mibfilename',
    'mibfile.version' => 'Version',
    // OID
    'oid.name_gui' => 'GUI Beschriftung',
    'oid.name' => 'OID Bezeichnung',
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
    'contact.firstname1' => 'Vorname 1',
    'firstname2' => 'Vorname 2',
    'lastname1' => 'Nachname 1',
    'lastname2' => 'Nachname 2',
    'tel' => 'Telefonnummer',
    'tel_private' => 'Telefonnummer privat',
    'email1' => 'E-Mail 1',
    'email2' => 'E-Mail 2',
    // NetGw
    'netgw.id' => 'ID',
    'netgw.hostname' => 'Name',
    'netgw.ip' => 'IP',
    'netgw.company' => 'Hersteller',
    'netgw.series' => 'Serie',
    // Contract
    'contact_id' => 'Gruppenvertrag',
    'contract.city' => 'Stadt',
    'company' => 'Firma',
    'contract.contract_end' => 'Vertragsende',
    'contract.contract_start' => 'Vertragsbeginn',
    'contract.district' => 'Bezirk',
    'contract.firstname' => 'Vorname',
    'contract.house_number' => 'Hausnr',
    'contract.id' => 'Vertrag',
    'contract.lastname' => 'Nachname',
    'contract.number' => 'Nummer',
    'contract.street' => 'Straße',
    'contract.zip' => 'PLZ',
    // Domain
    'domain.name' => 'Domäne',
    'domain.type' => 'Typ',
    'domain.alias' => 'Alias',
    // Endpoint
    'endpoint.ip' => 'IP',
    'endpoint.hostname' => 'Server-Name',
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
    'modem.city' => 'Stadt',
    'modem.district' => 'Bezirk',
    'modem.firstname' => 'Vorname',
    'modem.geocode_source' => 'Geocode-Quelle',
    'modem.house_number' => 'Hausnr',
    'modem.id' => 'Modem',
    'modem.inventar_num' => 'Inventar-Nr',
    'modem.lastname' => 'Nachname',
    'modem.mac' => 'MAC Adresse',
    'modem.model' => 'Modell',
    'modem.name' => 'Modemname',
    'modem.street' => 'Straße',
    'modem.sw_rev' => 'Firmware-Version',
    'modem.ppp_username' => 'PPP Nutzername',
    'modem.us_pwr' => 'US Pegel',
    'contract_valid' => 'Vertrag gültig?',
    // Node
    'node' => [
        'name' => 'Name',
        'headend' => 'Kopfstelle',
        'type' => 'Signalart',
    ],
    // QoS
    'qos.name' => 'QoS Name',
    'qos.ds_rate_max' => 'Maximale DS Geschwindigkeit',
    'qos.us_rate_max' => 'Maximale US Geschwindigkeit',
    // Mta
    'mta.hostname' => 'Server-Name',
    'mta.mac' => 'MAC-Adresse',
    'mta.type' => 'Provisionierungstyp',
    // Configfile
    'configfile.name' => 'Konfigurationsdatei',
    // PhonebookEntry
    'phonebookentry.id' => 'ID',
    // Phonenumber
    'phonenumber.prefix_number' => 'Vorwahl',
    'phonenr_act' => 'Aktivierungsdatum',
    'phonenr_deact' => 'Deaktivierungsdatum',
    'phonenr_state' => 'Status',
    'modem_city' => 'Modem-Ort',
    'sipdomain' => 'Registrar',
    // Phonenumbermanagement
    'phonenumbermanagement.id' => 'ID',
    'phonenumbermanagement.activation_date' => 'Aktivierungsdatum',
    'phonenumbermanagement.deactivation_date' => 'Deaktivierungsdatum',
    // PhoneTariff
    'phonetariff.name' => 'Telefontarif',
    'phonetariff.type' => 'Typ',
    'phonetariff.description' => 'Beschreibung',
    'phonetariff.voip_protocol' => 'VOIP Protokoll',
    'phonetariff.usable' => 'Verfügbar',
    // ENVIA enviaorder
    'enviaorder.ordertype'  => 'Bestelltyp',
    'enviaorder.orderstatus'  => 'Bestellstatus',
    'escalation_level' => 'Statuslevel',
    'enviaorder.created_at'  => 'Erstellt am',
    'enviaorder.updated_at'  => 'Bearbeitet am',
    'enviaorder.orderdate'  => 'Bestelldatum',
    'enviaorder_current'  => 'Bearbeitung notwendig?',
    'enviaorder.contract.number' => 'Vertrag',
    'enviaorder.modem.id' => 'Modem',
    'phonenumber.number' => 'Rufnummer',
    //ENVIA Contract
    'enviacontract.contract.number' => 'Vertrag',
    'enviacontract.end_date' => 'Enddatum',
    'enviacontract.envia_contract_reference' => 'envia-TEL-Vertragsreferenz',
    'enviacontract.modem.id' => 'Modem',
    'enviacontract.start_date' => 'Anfangsdatum',
    'enviacontract.state' => 'Status',
    // CDR
    'cdr.calldate' => 'Anrufzeitpunkt',
    'cdr.caller' => 'Anrufer',
    'cdr.called' => 'Angerufener',
    'cdr.mos_min_mult10' => 'minimaler MOS',
    // Numberrange
    'numberrange.id' => 'ID',
    'numberrange.name' => 'Name',
    'numberrange.start' => 'Start',
    'numberrange.end' => 'Ende',
    'numberrange.prefix' => 'Präfix',
    'numberrange.suffix' => 'Suffix',
    'numberrange.type' => 'Typ',
    'numberrange.costcenter.name' => 'Kostenstelle',
    'realty' => [
        'administration' => 'Hausverwaltung',
        'agreement_from' => 'Gültig von',
        'agreement_to' => 'Gültig bis',
        'apartmentCount' => 'Wohnungen gesamt',
        'apartmentCountConnected' => 'Wohnungen angeschlossen',
        'city' => 'Stadt',
        'concession_agreement' => 'Gestattungs-vertrag',
        'contact_id' => 'Hausverwaltung',
        'contact_local_id' => 'Lokaler Kontakt',
        'district' => 'Ortsteil',
        'house_nr' => 'Hausnr',
        'last_restoration_on' => 'Letzte Restaurierung / Sanierung',
        'name' => 'Name',
        'street' => 'Straße',
        'zip' => 'PLZ',
    ],
    // NAS
    'nas' => [
        'nasname' => 'Name',
    ],
    // Ticket
    'ticket' => [
        'id' => 'ID',
        'name' => 'Titel',
        'type' => 'Typ',
        'priority' => 'Priorität',
        'state' => 'Status',
        'user_id' => 'Erstellt von',
        'created_at' => 'Erstellt am',
        'assigned_users' => 'Bearbeiter',
        'ticketable_id' => 'id',
        'ticketable_type' => 'Typ',
    ],
    'assigned_users' => 'Bearbeiter',
    'tickettypes.name' => 'Typ',
];
