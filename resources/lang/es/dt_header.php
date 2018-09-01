<?php

return [
    // Index DataTable Header
    // Auth
    'users.login_name' => 'Nombre de usuario',
    'users.first_name' => 'Nombre',
    'users.last_name' => 'Apellido',
    'roles.title' => 'Función',
    'roles.rank' => 'Nivel',
    'roles.description' => 'Descripción 	',
    // GuiLog
    'guilog.created_at' => 'Hora',
    'guilog.username' => 'Usuario',
    'guilog.method' => 'Acción',
    'guilog.model' => 'Modelo',
    'guilog.model_id' => 'Modelo ID',
    // Company
    'company.name' => 'Nombre De La Empresa',
    'company.city' => 'Ciudad',
    'company.phone' => 'Número de teléfono celular',
    'company.mail' => 'Email',
    // Costcenter
    'costcenter.name' => 'Precio',
    'costcenter.number' => 'Importe',
    //Invoices
    'invoice.type' => 'Tipo',
    'invoice.year' => 'Año',
    'invoice.month' => 'Mes',
    //Item //**

    // Product
    'product.type' => 'Tipo',
    'product.name' => 'Nombre del producto',
    'product.price' => 'Precio',
    // Salesman
    'salesman.id' => 'ID',
    'salesman_id' 		=> 'ID del vendedor',
    'salesman_firstname' => 'Nombre',
    'salesman_lastname' => 'Apellido',
    'commission in %' 	=> 'Comisión en %',
    'contract_nr' 		=> 'Nro. de Contrato',
    'contract_name' 	=> 'Cliente',
    'contract_start' 	=> 'Inicio de Contrato',
    'contract_end' 		=> 'Fin de Contrato',
    'product_name' 		=> 'Producto',
    'product_type' 		=> 'Tipo de Producto',
    'product_count' 	=> 'Contar',
    'charge' 			=> 'Cambiar',
    'salesman.lastname' => 'Apellidos',
    'salesman.firstname' => 'Nombres',
    'salesman_commission' => 'Comisión',
    'sepaaccount_id' 	=> 'ID Cuenta SEPA',
    // SepaAccount
    'sepaaccount.name' => 'Nombre de la cuenta',
    'sepaaccount.institute' => 'Institución',
    'sepaaccount.iban' => 'IBAN',
    // SepaMandate
    'sepamandate.sepa_holder' => 'Poseedor de cuenta',
    'sepamandate.sepa_valid_from' => 'Valida desde',
    'sepamandate.sepa_valid_to' => 'Valida hasta',
    'sepamandate.reference' => 'Referencia de cuenta',
    // SettlementRun
    'settlementrun.year' => 'Año',
    'settlementrun.month' => 'Mes',
    'settlementrun.created_at' => 'Creado el',
    'verified' => 'Verificado?',
    // MPR
    'mpr.name' => 'Nombre',
    // NetElement
    'netelement.id' => 'ID',
    'netelement.name' => 'Elemento de red',
    'netelement.ip' => 'IP Adress',
    'netelement.state' => 'Estado',
    'netelement.pos' => 'Posición',
    // NetElementType
    'netelementtype.name' => 'Tipo de elemento de red',
    //HfcSnmp
    'parameter.oid.name' => 'Nombre OID',
    //Mibfile
    'mibfile.id' => 'ID',
    'mibfile.name' => 'Archivo MIB',
    'mibfile.version' => 'Versión',
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
    'email.greylisting' => '¿Activo listas de rechazo transitorio?',
    'email.blacklisting' => 'Lista negra habilitada?',
    'email.forwardto' => 'Reenviar a:',
    // CMTS
    'cmts.id' => 'ID',
    'cmts.hostname' => 'Nombre de host',
    'cmts.ip' => 'IP',
    'cmts.company' => 'Fabricante',
    'cmts.type' => 'Tipo',
    // Contract
    'contract.company' => 'Empresa',
    'contract.number' => 'Numero',
    'contract.firstname' => 'Nombres',
    'contract.lastname' => 'Apellidos',
    'contract.zip' => 'Código postal',
    'contract.city' => 'Ciudad',
    'contract.street' => 'Calle',
    'contract.house_number' => 'Numero de vivienda',
    'contract.district' => 'Provincia',
    'contract.contract_start' => 'Fecha de inicio',
    'contract.contract_end' => 'Fecha final',
    // Domain
    'domain.name' => 'Nombre del dominio',
    'domain.type' => 'Tipo',
    'domain.alias' => 'Alias',
    // Endpoint
    'endpoint.ip' => 'IP',
    'endpoint.hostname' => 'Nombre de host',
    'endpoint.mac' => 'MAC',
    'endpoint.description' => 'Descripción 	',
    // IpPool
    'ippool.id' => 'ID',
    'ippool.type' => 'Tipo',
    'ippool.net' => 'Red',
    'ippool.netmask' => 'Máscara de red',
    'ippool.router_ip' => 'Ip de Router',
    'ippool.description' => 'Descripcion',
    // Modem
    'modem.house_number' => 'Numero de vivienda',
    'modem.id' => 'Modem ID',
    'modem.mac' => 'Direccion MAC',
    'modem.model' => 'Modelo',
    'modem.sw_rev' => 'Version de Firmware',
    'modem.name' => 'Nombre del modem',
    'modem.firstname' => 'Nombres',
    'modem.lastname' => 'Apellidos',
    'modem.city' => 'Ciudad',
    'modem.street' => 'Calle',
    'modem.district' => 'Distrito',
    'modem.us_pwr' => 'Nivel US',
    'modem.geocode_source' => 'Geolocalización',
    'modem.inventar_num' => 'Serial',
    'contract_valid' => 'Contrato valido?',
    // QoS
    'qos.name' => 'Nombre de QoS',
    'qos.ds_rate_max' => 'Velocidad maxima de bajada',
    'qos.us_rate_max' => 'Velocidad maxima de subida',
    // Mta
    'mta.hostname' => 'Nombre de Host',
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
    'phonetariff.usable' => 'Disponible?',
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
    'ticket.created_at' => 'Creando el',
    'ticket.assigned_users' => 'Usuarios asignados',
    'assigned_users' => 'Usuarios asignados',
    'tickettypes.name' => 'Tipo',
];
