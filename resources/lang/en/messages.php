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
	'ALL' 					=> 'ALL',
	'Call Data Record'		=> 'Call Data Record',
	'ccc'					=> 'Customer Control Center',
	'cdr' 					=> 'cdr',
	'cdr_discarded_calls' 	=> "CDR: Contract Nr or ID ':contractnr' not found in database - :count calls of phonenumber :phonenr with price of :price :currency are discarded.",
	'cdr_missing_phonenr' 	=> "Parse CDR.csv: Detected call data records with phonenr :phonenr that is missing in database. Discard :count phonecalls with charge of :price :currency.",
	'cdr_missing_reseller_data' => 'Missing Reseller Data in Environment File!',
	'cdr_offset' 			=> 'CDR to Invoice time difference in Months',
	'close' 				=> 'Close',
	'contract_early_cancel' => 'Do you really want to cancel this contract before tariffs end of term :date is reached?',
	'conn_info_err_create' 	=> 'Error Creating PDF - See Logfiles or ask Admin!',
	'conn_info_err_template' => 'Could not Read Template - See GlobalConfig or Company if it is set!',
	'cpe_log_error' 		=> 'was not registering on Server - No log entry found',
	'cpe_not_reachable' 	=> 'but not reachable from WAN-side due to manufacturing reasons (it can be possible to enable ICMP response via modem configfile)',
	'cpe_fake_lease'		=> 'The DHCP server has not generated a lease for this endpoint, because the IP address is statically assigned and the server does not need to keep track of it. The following lease has been manually generated for reference only:',
	'D' 					=> 'day|days',
	'dashbrd_ticket' 		=> 'My New Tickets',
	'device_probably_online' =>	':type is probably online',
	'eom' 					=> 'to end of month',
	'envia_no_interaction' 	=> 'No Envia Orders require Interaction',
	'envia_interaction'	 	=> 'Envia Order requires Interaction|Envia Orders require Interaction',
	'home' 					=> 'Startseite',
	'indices_unassigned' 	=> 'Some of the assigned Indices could not be assigned to a corresponding parameter! They are actually just not used. You can delete them or keep them for later. Just compare the list of parameters of the netelementtype with the list of indices here.',
	'item_credit_amount_negative' => 'A negative credit amount becomes a debit! Are you sure that the customer shall be charged?',
	'invoice' 				=> 'Invoice',
	'Invoices'				=> 'Invoices',
	'log_out'				=> 'Log Out',
	'M' 					=> 'month|months',
	'Mark solved'			=> 'Mark as solved?',
	'missing_product' 		=> 'Missing Product!',
	'modem_eventlog_error'	=> 'Modem eventlog not found',
	'modem_force_restart_button_title' => 'Only restarts the modem. Doesn\'t save any changed data!',
	'modem_monitoring_error'=> 'This could be because the Modem was not online until now. Please note that Diagrams are only available
		from the point that a modem was online. If all diagrams did not show properly then it should be a
		bigger problem and there should be a cacti misconfiguration. Please consider the administrator on bigger problems.',
	'modem_no_diag'			=> 'No Diagrams available',
	'modem_lease_error'		=> 'No valid Lease found',
	'modem_lease_valid' 	=> 'Modem has a valid lease',
	'modem_log_error' 		=> 'Modem was not registering on Server - No log entry found',
	'modem_configfile_error'=> 'Modem configfile not found',
	'modem_offline'			=> 'Modem is Offline',
	'modem_restart_error' 		=> 'Could not restart Modem! (offline?)',
	'modem_restart_success_cmts' => "Restarted modem via CMTS",
	'modem_restart_success_direct' => "Restarted Modem directly via SNMP",
	'modem_save_button_title' 	=> 'Saves changed data. Determines new geoposition when address data was changed (and assigns it to a new MPR if necessary). Rebuilds the configfile and restarts the modem if at least one of the following has changed: Public IP, network access, configfile, QoS, MAC-address',
	'modem_statistics'		=> 'Number Online / Offline Modems',
	'month' 				=> 'Month',
	'mta_configfile_error'	=> 'MTA configfile not found',
	'noCC'					=> 'no Costcenter assigned',
	'oid_list' 				=> 'Warning: OIDs that not already exist in Database are discarded! Please upload MibFile before!',
	'password_change'		=> 'Change Password',
	'password_confirm'		=> 'Confirm Password',
	'phonenumber_nr_change_hlkomm' => 'Please be aware that future call data records can not be assigned to this contract anymore when you change this number. This is because HL Komm or Pyur only sends the phonenumber with the call data records.',
	'phonenumber_overlap_hlkomm' => 'This number exists or existed within the last :delay month(s). As HL Komm or Pyur only sends the phonenumber with the call data records, it won\'t be possible to assign possible made calls to the appropriate contract anymore! This can result in wrong charges. Please only add this number if it\'s a test number or you are sure that there will be no calls to be charged anymore.',
	'show_ags' 				=> 'Show AG Select Field on Contract Page',
	'snmp_query_failed' 	=> 'SNMP Query failed for following OIDs: ',
	'sr_repeat' 			=> 'Repeat for specific SEPA-Account', // Settlementrun repeat
	'upload_dependent_mib_err' => "Please Upload dependent ':name' before!! (OIDs cant be translated otherwise)",
	'user_settings'			=> 'User Settings',
	'user_glob_settings'	=> 'Global User Settings',

	'voip_extracharge_default' => 'Extra Charge Voip Calls default in %',
	'voip_extracharge_mobile_national' => 'Extra Charge Voip Calls mobile national in %',
	'Y' 					=> 'year|years',
];
