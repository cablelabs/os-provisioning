<?php

return [
 /**
  *	MODULE: BillingBase
  */
	//BillingBaseController
	'BillingBase_cdr_offset' 		=> "TAKE CARE: incrementing this when having data from settlement runs leads to overwritten CDRs during next run - make sure to save/rename the history!\n\nExample: Set to 1 if Call Data Records from June belong to Invoices of July, Zero if it's the same month, 2 if CDRs of January belong to Invoices of March.",
 	'BillingBase_extra_charge' 		=> 'Additional mark-on to purchase price. Only when not calculated through provider!',
	'BillingBase_InvoiceNrStart' 	=> 'Invoice Number Counter starts every new year with this number',
	'BillingBase_ItemTermination'	=> 'Allow Customers only to terminate booked products on last day of month',
	'BillingBase_MandateRef'		=> "A Template can be built with sql columns of contract or mandate table - possible fields: \n",
	'BillingBase_SplitSEPA'			=> 'Sepa Transfers are split to different XML-Files dependent of their transfer type',

	//CompanyController
	'Company_Management'			=> 'Comma separated list of names',
	'Company_Directorate'			=> 'Comma separated list of names',
	'Company_TransferReason'		=> 'Template from all Invoice class data field keys - Contract Number and Invoice Nr is default',

	//CostCenterController
	'CostCenter_BillingMonth'		=> 'Default: 6 (June) - if not set. Has to be minimum current month on change to avoid missing payments',

	//ItemController
	'Item_ProductId'				=> 'All fields besides Billing Cycle have to be cleared before a type change! Otherwise items can not be saved in most cases',
	'Item_ValidFrom'				=> 'For One Time Payments the fields can be used to split payment - Only YYYY-MM is considered then!',
	'Item_ValidFromFixed'			=> 'Fixed dates are used for billing and not updated by external orders',
	'Item_ValidToFixed'				=> 'Fixed dates are used for billing and not updated by external orders',
	'Item_CreditAmount'				=> 'Gross price actualy - will be changed in future to Net price',

	//ProductController
  	'Product_Name' 					=> 'For Credits it is possible to assign a Type by adding the type name to the Name of the Credit - e.g.: \'Credit Device\'',
  	'Product_Type'					=> 'All fields besides Billing Cycle have to be cleared before a type change! Otherwise products can not be saved in most cases',
	'Product_Number_of_Cycles' 		=> 'Take Care!: for all repeatedly payed products the price stands for every charge, for Once payed products the Price is divided by the number of cycles',

	//SalesmanController
	'Salesman_ProductList'			=> 'Add all Product types he gets commission for - possible: ',

	//SepaAccountController
	'SepaAccount_InvoiceHeadline'	=> 'Replaces Headline in Invoices created for this Costcenter',
	'SepaAccount_InvoiceText'		=> 'The Text of the separate four \'Invoice Text\'-Fields is automatically chosen dependent on the total charge and SEPA Mandate and is set in the appropriate Invoice for the Customer. It is possible to use all data field keys of the Invoice Class as placeholder in the form of {fieldname} to build a kind of template. These are replaced by the actual value of the Invoice.',

	// SettlementrunController
	'settlement_verification' 		=> 'If activated it\'s not possible to repeat the Settlement Run. Customer Invoices are only visible when this checkbox is activated.',

 /**
  *	MODULE: ProvBase
  */
 	//ModemController
	'Modem_NetworkAccess'			=> 'Disable/Enable Network Access - Take Care: If Billing-Module is installed this Checkbox will be overwritten daily during check of valid Tariff Item when it was not enabled/checked manually',
	'contract_number' 				=> 'Attention - Customer login password is changed automatically on changing this field!',

 /**
  *	MODULE: ProvVoip
  */
 	//PhonenumberManagementController
	'PhonenumberManagement_CarrierIn' => 'On incoming porting: set to previous Telco.',
	'PhonenumberManagement_CarrierInWithEnvia' => 'On incoming porting: set to previous Telco. In case of a new number set this to EnviaTEL',
	'PhonenumberManagement_EkpIn' => 'On incoming porting: set to previous Telco.',
	'PhonenumberManagement_EkpInWithEnvia' => 'On incoming porting: set to previous Telco. In case of a new number set this to EnviaTEL',
	'PhonenumberManagement_TRC' => 'This is for information only. Real changes have to be performed at your Telco.',
	'PhonenumberManagement_TRCWithEnvia' => 'If changed here this has to be sent to Envia, too (Update VoIP account).',
/**
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
 ];
