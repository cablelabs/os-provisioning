<?php

return [
 /*
  * Authentication and Base
  */
    'translate'					=> 'You can help translating NMS PRIME at',
    'assign_role'					=> 'Assign one or more Roles to this User. Users without a Role cant use the NMS because they got no Permissions.',
    'assign_users'					=> 'Assign one or more Users to this Role. Changes made here are not visible in the GuiLog of the user.',
    'assign_rank'					=> 'The rank of a role determines the ability to edit other users. <br \>You can assign values from 0 to 100. (higher is better). <br \>If a user has more than one role, the highest rank is used. <br \>If the ability to update users is set, the rank is also checked. Only if the rank of the editor is higher, permission is granted. Furthermore, when creating or updating users, only roles with equal or lower rank can be assigned.',
    'All abilities'					=> 'This ability allows all authorisation requests, except for the abilities, which are explicitly forbidden. This is mainly a helper ability. Forbidding is disabled, because only checked Abilities are allowed. If this Ability is not checked, you have to set all abilities by hand. If you change this ability, when many other abilities are set, it will take up to 1 minute to apply all the changes.',
    'View everything'			=> 'This ability allows to view all pages. Forbidding is disabled, because it makes the NMS unusable. This is mainly a helper ability for guests or very low priviledged users.',
    'Use api'					=> 'This ability allows or forbids to access the API routes with "Basic Auth" (the email is used as username).',
    'See income chart'			=> 'This ability allows or forbids to view the income chart on the dashboard.',
    'View analysis pages of modems'	=> 'This ability allows or forbids to access the analysis pages of a modem.',
    'View analysis pages of cmts' => 'This ability allows or forbids to access the analysis pages of a CMTS.',
    'Download settlement runs'	=> 'This ability allows or forbids the download of settlement runs. This ability has no impact if it is forbidden to manage settlement runs.',
 /*
  * Index Page - Datatables
  */
    'SortSearchColumn'				=> 'This Column cannot be searched or ordered.',
    'PrintVisibleTable'				=> 'Prints the shown table. If the table is filtered make sure to select the \\"All\\" option to display everything. Loading can take a few seconds.',
    'ExportVisibleTable'			=> 'Exports the shown table. If the table is filtered make sure to select the \\"All\\" option to display everything. Loading can take a few seconds.',
    'ChangeVisibilityTable'			=> 'Select the columns that should be visible.',

    // GlobalConfig
    'ISO_3166_ALPHA-2'				=> 'ISO 3166 ALPHA-2 (two characters, e.g. “US”). Used in address forms to specify the country.',
    'PasswordReset'           => 'This property defines the timespan in days in which the users of the administration panel should change their passwords. If you want to disable the password reset message, set the value to 0.',

    /*
     *	MODULE: BillingBase
     */
    //BillingBaseController
    'BillingBase' => [
        'cdr_offset'        => "TAKE CARE: incrementing this when having data from settlement runs leads to overwritten CDRs during next run - make sure to save/rename the history!\n\nExample: Set to 1 if Call Data Records from June belong to Invoices of July, Zero if it's the same month, 2 if CDRs of January belong to Invoices of March.",
        'cdr_retention'     => 'Months that Call Data Records may/have to be kept save',
        'extra_charge'      => 'Additional mark-on to purchase price. Only when not calculated through provider!',
        'fluid_dates'       => 'Check this box if you want to add tariffs with uncertain start and/or end date. If checked two new checkboxes (Valid from fixed, Valid to fixed) will appear on Item\'s edit/create page. Check out their help messages for further explanation!',
        'InvoiceNrStart'    => 'Invoice Number Counter starts every new year with this number',
        'ItemTermination'   => 'Allow Customers only to terminate booked products on last day of month',
        'MandateRef'        => "A Template can be built with sql columns of contract or mandate table - possible fields: \n",
        'rcd'               => 'Is also the date of value. Can also be set specifically for a contract on contract page',
        'showAGs'           => 'Adds a select list with contact persons to the contract page. The list has to be stored in appropriate Storage directory - check source code!',
        'SplitSEPA'         => 'Sepa Transfers are split to different XML-Files dependent of their transfer type',
        ],

    //CompanyController
    'Company_Management'			=> 'Comma separated list of names',
    'Company_Directorate'			=> 'Comma separated list of names',
    'Company_TransferReason'		=> 'Template from all Invoice class data field keys - Contract Number and Invoice Nr is default',
    'conn_info_template' 			=> 'Tex Template used to Create Connection Information on the Contract Page for a Customer',

    //CostCenterController
    'CostCenter_BillingMonth'		=> 'Accounting for yearly charged items - corresponds to the month the invoices are created for. Default: 6 (June) - if not set. Please be careful to not miss any payments when changing this!',

    //ItemController
    'Item_ProductId'				=> 'All fields besides Billing Cycle have to be cleared before a type change! Otherwise items can not be saved in most cases',
    'Item_ValidFrom'				=> 'For One Time Payments the fields can be used to split payment - Only YYYY-MM is considered then!',
    'Item_ValidFromFixed'			=> 'Checked by default! Uncheck if the tariff shall stay inactive when start date is reached (e.g. if customer is waiting for a phone number porting). The tariff will not start and not be charged until you activate the checkbox. Further the start date will be incremented every day by one day after reaching the start date. Info: The date is not updated by external orders (e.g. from telephony provider).',
    'Item_validTo'                  => 'It\'s possible to specify the number of months here - e.g. \'12M\' for 12 months. For monthly paid products it will just add the number of months - so start date 2018-05-04 will be valid to 2019-05-04. Single paid items with splitted payment will be charged 12 times - end date will be 2019-04-31 then.',
    'Item_ValidToFixed'				=> 'Checked by default! Uncheck if the end date is uncertain. If unchecked the tariff will not end and will be charged until you activate the checkbox. Further when the end date is reached it will be incremented every day by one day. Info: The date is not updated by external orders (e.g. from telephony provider).',
    'Item_CreditAmount'				=> 'Net Amount to be credited to Customer. Take Care: a negative amount becomes a debit!',

    //ProductController
    'product' => [
        'bundle'                => 'On bundled tarifs the minimum runtime of the contract is determined only be the internet tariff. Otherwise the last starting valid tariff (Voip or Internet) dictates this date.',
        'maturity_min'          => 'Tariff minimum period/runtime/term. E.g. 14D (14 days), 3M (three months), 1Y (one year)',
        'maturity'              => 'Tariff period/runtime/term extension after the minimum runtime. Will be automatically added when tariff was not canceled before period of notice. Default 1 month. E.g. 14D (14 days), 3M (three months), 1Y (one year)',
        'Name'                  => 'For Credits it is possible to assign a Type by adding the type name to the Name of the Credit - e.g.: \'Credit Device\'',
        'pod'                   => 'E.g. 14D (14 days), 3M (three months), 1Y (one year)',
        'proportional'          => 'Activate this checkbox when items that begin during the current settlement run shall be charged proportionately. E.g. if an monthly paid item starts in the middle of the month the customer would be charged only half of the full price in this settlement run.',
        'Type'                  => 'All fields besides Billing Cycle have to be cleared before a type change! Otherwise products can not be saved in most cases',
        ],
    'Product_Number_of_Cycles' 		=> 'Take Care!: for all repeatedly payed products the price stands for every charge, for Once payed products the Price is divided by the number of cycles',

    //SalesmanController
    'Salesman_ProductList'			=> 'Add all Product types he gets commission for - possible: ',

    // SepaMandate
    'sm_cc' 						=> 'If a cost center is assigned only products related to the same cost center will be charged of this account. Leave this field empty if all charges that can not be assigned to another SEPA-Mandate with specific cost center shall be debited of this account. Note: It is assumed that all emerging costs that can not be assigned to any SEPA-Mandate will be payed in cash!',
    'sm_recur' 						=> 'Activate if there already have been transactions of this account before the creation of this mandate. Sets the status to recurring. Note: This flag is only considered on first transaction!',

    //SepaAccountController
    'SepaAccount_InvoiceHeadline'	=> 'Replaces Headline in Invoices created for this Costcenter',
    'SepaAccount_InvoiceText'		=> 'The Text of the separate four \'Invoice Text\'-Fields is automatically chosen dependent on the total charge and SEPA Mandate and is set in the appropriate Invoice for the Customer. It is possible to use all data field keys of the Invoice Class as placeholder in the form of {fieldname} to build a kind of template. These are replaced by the actual value of the Invoice.',
    'tex_template' 					=> 'TeX Template',

    // SettlementrunController
    'settlement_verification' 		=> 'Customer Invoices are only visible when this checkbox is activated. The checkbox can only be activated if the last run was executed for ALL SEPA accounts (to not miss any changes). Info: If activated it\'s not possible to repeat the Settlement Run.',

 /*
  * MODULE: Dashboard
  */
    'next'							=> 'Next step: ',
    'set_isp_name'					=> 'Set internet service provider name',
    'create_cmts'					=> 'Create first CMTS',
    'create_cm_pool'				=> 'Create first cablemodem IP pool',
    'create_cpepriv_pool'			=> 'Create first private CPE IP pool',
    'create_qos'					=> 'Create first QoS profile',
    'create_product'				=> 'Create first billing product',
    'create_configfile'				=> 'Create first configfile',
    'create_sepa_account'			=> 'Create first SEPA account',
    'create_cost_center'			=> 'Create first cost center',
    'create_contract'				=> 'Create first contract',
    'create_nominatim'				=> 'Set an email address (OSM_NOMINATIM_EMAIL) in /etc/nmsprime/env/global.env to enable geocoding for modems',
    'create_nameserver'				=> 'Set your nameserver to 127.0.0.1 in /etc/resolv.conf and make sure it won\'t be overwritten via DHCP (see DNS and PEERDNS in /etc/sysconfig/network-scripts/ifcfg-*)',
    'create_modem'					=> 'Create first modem',

 /*
  *	MODULE: HfcReq
  */
    'netelementtype_reload' 		=> 'In Seconds. Zero to deactivate autoreload. Decimals possible.',
    'netelementtype_time_offset' 	=> 'In Seconds. Decimals possible.',
    'undeleteables' 				=> 'Net & Cluster can not be changed due to there relevance for all the Entity Relation Diagrams',

 /*
  *	MODULE: HfcSnmp
  */
    'mib_filename' 					=> 'The Filename is composed by MIB name & Revision. If there is already an existent identical File it\'s not possible to create it again.',
    'oid_link' 						=> 'Go to OID Settings',
    'oid_table' 					=> 'INFO: This Parameter belongs to a Table-OID. If you add/specify SubOIDs or/and indices, only these are considered for the snmpwalk. Besides the better Overview this can dramatically speed up the Creation of the Controlling View for the corresponding NetElement.',
    'parameter_3rd_dimension' 		=> 'Check this box if this Parameter belongs to an extra Controlling View behind an Element of the SnmpTable.',
    'parameter_diff' 				=> 'Check this if only the Difference of actual to last queried values shall be shown.',
    'parameter_divide_by' 			=> 'Make this ParameterValue percentual compared to the added values of the following OIDs that are queried by the actual snmpwalk, too. In a first step this only works on SubOIDs of exactly specified tables! The Calculation is also done after the Difference is calculated in case of Difference-Parameters.',
    'parameter_indices' 			=> 'Specify a comma separated List of all Table Rows we want the Snmp Values for.',
    'parameter_html_frame' 			=> 'Assign this parameter to a specific frame (part of the page). Doesn\'t have influences on SubOIDs in Tables (but on 3rd Dimensional Params!).',
    'parameter_html_id' 			=> 'By adding an ID you can order this parameter in sequence to other parameters. In tables you can change the column order by setting the Sub-Params html id.',

 /*
  *	MODULE: ProvBase
  */
    'contract' => [
        'valueDate' => 'Day of month for specific date of value. Overrides the requested collection date from global config for this contract in the SEPA XML.',
    ],
    'rate_coefficient'				=> 'MaxRateSustained will be multiplied by this value to grant the user more (> 1.0) throughput than subscribed.',
    'additional_modem_reset'		=> 'Check if an additional button should be displayed, which resets the modem via SNMP without querying the CMTS.',
    'openning_new_tab_for_modem' => 'Check the box to open the modem edit page in new tab in topography view.',
    //ModemController
    'Modem_InternetAccess'			=> 'Internet Access for CPEs. (MTAs are not considered and will always go online when all other configurations are correct). Take care: With Billing-Module this checkbox will be overwritten by daily check if tariff changes.',
    'Modem_InstallationAddressChangeDate'	=> 'In case of (physical) relocation of the modem: Add startdate for the new address here. If readonly there is a pending address change order at Envia.',
    'Modem_GeocodeOrigin'			=> 'Where does geocode data come from? If set to “n/a” address could not be geocoded against any API. Will be set to your name on manually changed geodata.',
    'contract_number' 				=> 'Attention - Customer login password is changed automatically on changing this field!',
    'mac_formats'					=> "Allowed formats (case-insensitive):\n\n1) AA:BB:CC:DD:EE:FF\n2) AABB.CCDD.EEFF\n3) AABBCCDDEEFF",
    'fixed_ip_warning'				=> 'Using fixed IP address is highly discouraged, as this breaks the ability to move modems and their CPEs freely among CMTSes. Instead of telling the customer a fixed IP address they should be supplied with the hostname, which will not change.',
    'modem_update_frequency'		=> 'This field is updated once a day.',
    'enable_agc'					=> 'Enable upstream automatic gain control.',
    'agc_offset'					=> 'Upstream automatic gain control offset in dB. (default: 0.0)',
    'configfile_count'              => 'The number in brackets indicates how often the respective configurationfile is already used.',
    'has_telephony'                 => 'Activate if customer shall have telephony but has no internet. This flag can actually not be used to disable telephony on contracts with internet. Please delete the MTA or disable the phonenumber for that. Info: The setting influences the modems configfile parameters NetworkAcess and MaxCPE - see modems analyses page tab \'Configfile\'',

 /*
  *	MODULE: ProvVoip
  */
    //PhonenumberManagementController
    'PhonenumberManagement_CarrierIn' => 'On incoming porting: set to previous Telco.',
    'PhonenumberManagement_CarrierInWithEnvia' => 'On incoming porting: set to previous Telco. In case of a new number set this to EnviaTEL',
    'PhonenumberManagement_EkpIn' => 'On incoming porting: set to previous Telco.',
    'PhonenumberManagement_EkpInWithEnvia' => 'On incoming porting: set to previous Telco. In case of a new number set this to EnviaTEL',
    'PhonenumberManagement_TRC' => 'This is for information only. Real changes have to be performed at your Telco.',
    'PhonenumberManagement_TRCWithEnvia' => 'If changed here this has to be sent to Envia, too (Update VoIP account).',
    'PhonenumberManagement_Autogenerated' => 'This management has been created automatically. Please verify/change values, then uncheck this box.',
/*
  * MODULE VoipMon
  */
    'mos_min_mult10' 				=> 'Minimal Mean Opionion Score experienced during call',
    'caller' 						=> 'Call direction from Caller to Callee',
    'a_mos_f1_min_mult10' 			=> 'Minimal Mean Opionion Score experienced during call for a fixed jitter buffer of 50ms',
    'a_mos_f2_min_mult10' 			=> 'Minimal Mean Opionion Score experienced during call for a fixed jitter buffer of 200ms',
    'a_mos_adapt_min_mult10' 		=> 'Minimal Mean Opionion Score experienced during call for an adaptive jitter buffer of 500ms',
    'a_mos_f1_mult10' 				=> 'Average Mean Opionion Score experienced during call for a fixed jitter buffer of 50ms',
    'a_mos_f2_mult10' 				=> 'Average Mean Opionion Score experienced during call for a fixed jitter buffer of 200ms',
    'a_mos_adapt_mult10' 			=> 'Average Mean Opionion Score experienced during call for an adaptive jitter buffer of 500ms',
    'a_sl1' => 'Number of packets experiencing one consecutive packet loss during call',
    'a_sl9' => 'Number of packets experiencing nine consecutive packet losses during call',
    'a_d50' => 'Number of packets experiencing a packet delay variation (i.e. jitter) between 50ms and 70ms',
    'a_d300' => 'Number of packets experiencing a packet delay variation (i.e. jitter) greater than 300ms',
    'called' => 'Call direction from Callee to Caller',
/*
 * Module Ticketsystem
 */
    'assign_user' => 'Allowed to assign an user to a ticket.',
    'mail_env'    => 'Next: Set your Host/Username/Password in /etc/nmsprime/env/global.env to enable receiving Emails concerning Tickets',
    'noReplyMail' => 'The E-mail address which should be displayed as the sender, while creating/editing tickets. This address does not have to exist. For example: example@example.com',
    'noReplyName' => 'The name which should be displayed as the sender, while creating/editing tickets. For example: NMS Prime',
    'ticket_settings' => 'Next: Set noreply name and address in Global Config Page.',
 ];
