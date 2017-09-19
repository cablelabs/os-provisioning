<?php

return [

	/*
	|--------------------------------------------------------------------------
	| All other Language Lines - TODO: split descriptions and messages?
	|--------------------------------------------------------------------------
	*/

	// Descriptions of Form Fields in Edit/Create
	'accCmd_processing' 	=> 'The SettlementRun is executed. Please wait until this process has finished.',
	'alert' 				=> 'Attention!',
	'Call Data Record'		=> 'Call Data Record',
	'ccc'					=> 'Customer Control Center',

	'cdr_offset' 			=> 'CDR to Invoice time difference in Months',
	'close' 				=> 'Close',
	'cpe_log_error' 		=> 'was not registering on Server - No log entry found',
	'conn_info_err_create' 	=> 'Error Creating PDF - See Logfiles or ask Admin!',
	'conn_info_err_template' => 'Could not Read Template - See GlobalConfig or Company if it is set!',
	'cpe_not_reachable' 	=> 'but not reachable from WAN-side due to manufacturing reasons (it can be possible to enable ICMP response via modem configfile)',
	'device_probably_online' =>	':type is probably online',
	'home' 					=> 'Startseite',
	'item_credit_amount_negative' => 'A negative credit amount becomes a debit! Are you sure that the customer shall be charged?',
	'invoice' 				=> 'Invoice',
	'Invoices'				=> 'Invoices',
	'log_out'				=> 'Log Out',
	'missing_product' 		=> 'Missing Product!',
	'modem_eventlog_error'	=> 'Modem eventlog not found',
	'modem_monitoring_error'=> 'This could be because the Modem was not online until now. Please note that Diagrams are only available
		from the point that a modem was online. If all diagrams did not show properly then it should be a
		bigger problem and there should be a cacti misconfiguration. Please consider the administrator on bigger problems.',
	'modem_no_diag'			=> 'No Diagrams available',
	'modem_lease_error'		=> 'No valid Lease found',
	'modem_lease_valid' 	=> 'Modem has a valid lease',
	'modem_log_error' 		=> 'Modem was not registering on Server - No log entry found',
	'modem_configfile_error'=> 'Modem configfile not found',
	'modem_offline'			=> 'Modem is Offline',
	'month' 				=> 'Month',
	'oid_list' 				=> 'Warning: OIDs that not already exist in Database are discarded! Please upload MibFile before!',
	'password_change'		=> 'Change Password',
	'password_confirm'		=> 'Confirm Password',
	'upload_dependent_mib_err' => "Please Upload dependent ':name' before!! (OIDs cant be translated otherwise)",
	'user_settings'			=> 'User Settings',
	'user_glob_settings'	=> 'Global User Settings',

	'voip_extracharge_default' => 'Extra Charge Voip Calls default in %',
	'voip_extracharge_mobile_national' => 'Extra Charge Voip Calls mobile national in %',
// Index DataTable Header
	// GiuLog
	'guilog.created_at' => 'Time',
	'guilog.username' => 'User',
	'guilog.method' => 'Action',
	'guilog.model' => 'Model',
	'guilog.model_id' => 'Model ID',
	// Company
	'company.name' => 'Name', 
	'company.city' => 'City',
	'company.phone' => 'Mobile Number',
	'company.mail' => 'E-Mail',
	// Costcenter
	'costcenter.name' => 'Name',
	'costcenter.number' => 'Number',
	//Invoices
	'invoice.type' => 'Type',
	'invoice.year' => 'Year', 
	'invoice.month' => 'Month',
	//Item //**

	// Product
	'product.type' => 'Type',
	'product.name' => 'Name',
	'product.price' => 'Price',
	// Salesman
	'salesman.id' => 'ID',
	'salesman.lastname' => 'Lastname',
	'salesman.firstname' => 'Firstname',
	// SepaAccount
	'sepaaccount.name' => "Account Name",
	'sepaaccount.institute' => 'Institute',
	'sepaaccount.iban' => 'IBAN',
	// SepaMandate
	'sepamandate.sepa_holder' => 'Account Holder',
	'sepamandate.sepa_valid_from' => 'Valid from',
	'sepamandate.sepa_valid_to' => 'Valid to',
	'sepamandate.reference' => 'Account reference',
	// SettlementRun
	'settlementrun.year' => 'Year',
	'settlementrun.month' => 'Month',
	'settlementrun.created_at' => 'Created at',
	'verified' => 'Verified?',
	// MPR
	'mpr.name' => 'Name',
	// NetElement
	'netelement.id' => 'ID',	
	'netelement.name' => 'Netelement',
	'netelement.ip' => 'IP Adress',
	'netelement.pos' => 'Position',
	// NetElementType
	'netelementtype.name' => 'Netelementtype',
	//HfcSnmp
	'parameter.oid.name' => 'OID Name',
	//Mibfile
	'mibfile.id' => 'ID',
	'mibfile.name' => 'Name',
	'mibfile.version' => 'Version',
	// OID
	'oid.name_gui' => 'GUI Label', 
	'oid.name' => 'OID Name',
	'oid.oid' => 'OID',
	'oid.access' => 'Access Type',
	//SnmpValue
	'snmpvalue.oid_index' => 'OID Index',
	'snmpvalue.value' => 'OID Value',
	// MAIL
	'email.localpart' => 'Local Part',
	'email.index' => 'Primary E-Mail?',
	'email.greylisting' => 'Greylisting active?',
	'email.blacklisting' => 'On Blacklist?',
	'email.forwardto' => 'Forward to:',
	// CMTS
	'cmts.id' => 'ID',
	'cmts.hostname' => 'Hostname',
	'cmts.ip' => 'IP',
	'cmts.company' => 'Manufacturer',
	'cmts.type' => 'Type',
	// Contract
	'contract.number' => 'Contractnumber',
	'contract.firstname' => 'Firstname',
	'contract.lastname' => 'Lastname',
	'contract.zip' => 'ZIP',
	'contract.city' => 'City',
	'contract.street' => 'Street',
	'contract.house_number' => 'Hausenumber',
	'contract.district' => 'District',
	'contract.contract_start' => 'Contract Startdate',
	'contract.contract_end' => 'Contract Enddate',
	// Domain
	'domain.name' => 'Name',
	'domain.type' => 'Type',
	'domain.alias' => 'Alias',
	// Endpoint
	'endpoint.hostname' => 'Hostname',
	'endpoint.mac' => 'MAC',
	'endpoint.description' => 'Description',
	// IpPool
	'ippool.id' => 'ID',
	'ippool.type' => 'Type',
	'ippool.net' => 'Net',
	'ippool.netmask' => 'Netmask',
	'ippool.router_ip' => 'Router IP',
	'ippool.description' => 'Description',
	// Modem
	'modem.id' => 'Modem Number',
	'modem.mac' => 'MAC Addresse',
	'modem.name' => 'Name',
	'modem.lastname' => 'Lastname',
	'modem.city' => 'City',
	'modem.street' => 'Street',
	'modem.us_pwr' => 'US level',
	'contract_valid' => 'Contract valid?',
	// QoS
	'qos.name' => 'Name',
	'qos.ds_rate_max' => 'Maximum DS Speed',
	'qos.us_rate_max' => 'Maximum US Speed',
	// Mta
	'mta.hostname' => 'Hostname',
	'mta.mac' => 'MAC-Adress',
	'mta.type' => 'VOIP Protocol',
	// Configfile
	'configfile.name' => 'Configfile',
	// PhonebookEntry
	'phonebookentry.id' => 'ID',
	// Phonenumber
	'phonenumber.prefix_number' => 'Prefix',
	'phonenumber.number' => 'Number',
	'phonenr_act' => 'Activation date',
	'phonenr_deact' => 'Deactivation date',
	'phonenr_state' => 'Status',
	// Phonenumbermanagement
	'phonenumbermanagement.id' => 'ID',
	// PhoneTariff
	'phonetariff.name' => 'Name',
	'phonetariff.type' => 'Type',
	'phonetariff.description' => 'Description',
	'phonetariff.voip_protocol' => 'VOIP Protokoll',
	'phonetariff.usable' => 'Usable?',
	// ENVIA enviaorder
	'enviaorder_ordertype'  => 'Order Type',
	'enviaorder_orderstatus'  => 'Order Status',
	'enviaorder_escalation_level' => 'Statuslevel',
	'enviaorder_contract_nr'  => 'Contract',
	'enviaorder_modem_nr'  => 'Modem',
	'enviaorder_phonenumber_nrs'  => 'Telefone Number',
	'enviaorder.created_at'  => 'Created at',
	'enviaorder.updated_at'  => 'Updated at',
	'enviaorder.orderdate'  => 'Order date',
	'enviaorder_current'  => 'Action needed?',
	'enviaorder_solve_link' => 'Mark as solved?',
	// CDR
	'cdr.calldate' => 'Call Date',
	'cdr.caller' => 'Caller',
	'cdr.called' => 'Called',
	'cdr.mos_min_mult10' => 'Minimum MOS',
];
