<?php
return [
	// Index DataTable Header
    // Missing on MPR
	'id' => 'ID mpr',
	'prio' => 'Prioridad',
	// Auth
	'authusers.login_name' => 'Nombre de usuario',
	'authusers.first_name' => 'Nombres',
	'authusers.last_name' => 'Apellidos',
	'authrole.name' => 'Nombre de rol',
	// GuiLog
	'guilog.created_at' => 'Creado el',
	'guilog.username' => 'Usuario',
	'guilog.method' => 'Accion',
	'guilog.model' => 'Modelo',
	'guilog.model_id' => 'ID del modelo',
	// Company
	'company.name' => 'Nombre de la compa&ntilde;ia',
	'company.city' => 'Ciudad',
	'company.phone' => 'Numero telefonico',
	'company.mail' => 'E-Mail',
	// Costcenter
	'costcenter.name' => 'Centro de costes',
	'costcenter.number' => 'Numero',
	//Invoices
	'invoice.type' => 'Tipo',
	'invoice.year' => 'A&ntilde;o',
	'invoice.month' => 'Mes',
	//Item //**

	// Product
	'product.type' => 'Tipo',
	'product.name' => 'Nombre del producto',
	'product.price' => 'Precio',
	// Salesman
	'salesman.id' => 'ID',
	'salesman.lastname' => 'Apellidos',
	'salesman.firstname' => 'Nombres',
	// SepaAccount
	'sepaaccount.name' => "Nombre de cuenta SEPA",
	'sepaaccount.institute' => 'Asociacion',
	'sepaaccount.iban' => 'IBAN',
	// SepaMandate
	'sepamandate.sepa_holder' => 'Poseedor de cuenta',
	'sepamandate.sepa_valid_from' => 'Valida desde',
	'sepamandate.sepa_valid_to' => 'Valida hasta',
	'sepamandate.reference' => 'Referencia de cuenta',
	// SettlementRun
	'settlementrun.year' => 'A&ntilde;o',
	'settlementrun.month' => 'Mes',
	'settlementrun.created_at' => 'Creado el',
	'verified' => 'Verificado?',
	// MPR
	'mpr.name' => 'Nombre',
	// NetElement
	'netelement.id' => 'ID',
	'netelement.name' => 'Elemento de red',
	'netelement.ip' => 'Direccion IP',
	'netelement.state' => 'Estado',
	'netelement.pos' => 'Posicion',
	'netelement.options' => 'Opciones',
	// NetElementType
	'netelementtype.name' => 'Tipo de elemento de red',
	//HfcSnmp
	'parameter.oid.name' => 'Nombre OID',
	//Mibfile
	'mibfile.id' => 'ID',
	'mibfile.name' => 'Nombre MIB',
	'mibfile.version' => 'Version',
	// OID
	'oid.name_gui' => 'Etiqueta de GUI',
	'oid.name' => 'Nombre OID',
	'oid.oid' => 'OID',
	'oid.access' => 'Tipo de acceso',
	//SnmpValue
	'snmpvalue.oid_index' => 'Indice OID',
	'snmpvalue.value' => 'Valor OID',
	// MAIL
	'email.localpart' => 'Parte local',
	'email.index' => 'E-Mail primario?',
	'email.greylisting' => 'Greylisting activo?',
	'email.blacklisting' => 'Lista negra habilitada?',
	'email.forwardto' => 'Reenviar a:',
	// CMTS
	'cmts.id' => 'ID',
	'cmts.hostname' => 'Hostname',
	'cmts.ip' => 'IP',
	'cmts.company' => 'Fabricante',
	'cmts.type' => 'Tipo',
	// Contract
	'contract.company' => 'Compa&ntilde;ia',
	'contract.number' => 'Numero',
	'contract.firstname' => 'Nombres',
	'contract.lastname' => 'Apellidos',
	'contract.zip' => 'ZIP',
	'contract.city' => 'Ciudad',
	'contract.street' => 'Calle',
    'contract.house_number' => 'Numero de vivienda',
	'contract.district' => 'Distrito',
	'contract.contract_start' => 'Fecha de inicio',
	'contract.contract_end' => 'Fecha de fin',
	// Domain
	'domain.name' => 'Nombre del dominio',
	'domain.type' => 'Tipo',
	'domain.alias' => 'Alias',
	// Endpoint
	'endpoint.ip' => 'IP',
	'endpoint.hostname' => 'Hostname',
	'endpoint.mac' => 'MAC',
	'endpoint.description' => 'Descripcion',
	// IpPool
	'ippool.id' => 'ID',
	'ippool.type' => 'Tipo',
	'ippool.net' => 'Net',
	'ippool.netmask' => 'Netmask',
	'ippool.router_ip' => 'Router IP',
	'ippool.description' => 'Descripcion',
	// Modem
    'modem.house_number' => 'Numero de vivienda',
	'modem.id' => 'Modem ID',
	'modem.mac' => 'Direccion MAC',
	'modem.name' => 'Nombre del modem',
	'modem.firstname' => 'Nombres',
	'modem.lastname' => 'Apellidos',
	'modem.city' => 'Ciudad',
	'modem.street' => 'Calle',
	'modem.district' => 'Distrito',
	'modem.us_pwr' => 'US level',
	'modem.geocode_source' => 'Geocode origin',
	'modem.inventar_num' => 'Serial',
	'contract_valid' => 'Contrato valido?',
	// QoS
	'qos.name' => 'Nombre QoS',
	'qos.ds_rate_max' => 'Velocidad DS maxima',
	'qos.us_rate_max' => 'Velocidad US maxima',
	// Mta
	'mta.hostname' => 'Hostname',
	'mta.mac' => 'Direccion MAC',
	'mta.type' => 'Protocolo VOIP',
	// Configfile
	'configfile.name' => 'Archivo de configuracion',
	// PhonebookEntry
	'phonebookentry.id' => 'ID',
	// Phonenumber
	'phonenumber.prefix_number' => 'Prefijo',
	'phonenumber.number' => 'Numero',
	'phonenr_act' => 'Fecha de activacion',
	'phonenr_deact' => 'Fecha de desactivacion',
	'phonenr_state' => 'Estado',
	'modem_city' => 'Ciudad del modem',
	// Phonenumbermanagement
	'phonenumbermanagement.id' => 'ID',
	'phonenumbermanagement.activation_date' => 'Fecha de activacion',
	'phonenumbermanagement.deactivation_date' => 'Fecha de desactivacion',
	// PhoneTariff
	'phonetariff.name' => 'Tarifa telefonica',
	'phonetariff.type' => 'Tipo',
	'phonetariff.description' => 'Descripcion',
	'phonetariff.voip_protocol' => 'Protocolo VOIP',
	'phonetariff.usable' => 'Usable?',
	// ENVIA enviaorder
	'enviaorder.ordertype'  => 'Tipo de orden',
	'enviaorder.orderstatus'  => 'Estado de orden',
	'escalation_level' => 'Nivel de estado',
	'enviaorder.created_at'  => 'Creado el',
	'enviaorder.updated_at'  => 'Subido el',
	'enviaorder.orderdate'  => 'Fecha de orden',
	'enviaorder_current'  => 'Acciones necesarias?',
	//ENVIA Contract
	'enviacontract.envia_contract_reference' => 'envia TEL referencia de contrato',
	'enviacontract.state' => 'Estado',
	'enviacontract.start_date' => 'Fecha de inicio',
	'enviacontract.end_date' => 'Fecha de desenlace',
	// CDR
	'cdr.calldate' => 'Fecha de llamada',
	'cdr.caller' => 'Emisor',
	'cdr.called' => 'Receptor',
	'cdr.mos_min_mult10' => 'MOS minimo',
	// Numberrange
	'numberrange.id' => 'ID',
	'numberrange.name' => 'Nombre',
	'numberrange.start' => 'Inicio',
	'numberrange.end' => 'Fin',
	'numberrange.prefix' => 'Prefijo',
	'numberrange.suffix' => 'Sufijo',
	'numberrange.type' => 'Tipo',
	'numberrange.costcenter.name' => 'Centro de costes',
	// Ticket
	'ticket.id' => 'ID',
	'ticket.name' => 'Titulo',
	'ticket.type' => 'Tipo',
	'ticket.priority' => 'Prioridad',
	'ticket.state' => 'Estado',
	'ticket.user_id' => 'Creado por',
	'ticket.created_at' => 'Creado el',
	'ticket.assigned_users' => 'Usuarios asignados',
	'assigned_users' => 'Usuarios asignados',
	'tickettypes.name' => 'Tipo',
];
