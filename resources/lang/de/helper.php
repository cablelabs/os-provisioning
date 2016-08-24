<?php

return [
 /**
  *	MODULE: BillingBase	
  */
	//BillingBaseController
	'BillingBase_MandateRef'		=> "A Template can be built with sql columns of contract or mandate table - possible fields: \n",
	'BillingBase_InvoiceNrStart' 	=> 'Invoice Number Counter starts every new year with this number',
	'BillingBase_SplitSEPA'			=> 'Sepa Transfers are split to different XML-Files dependent of their transfer type',
	'BillingBase_ItemTermination'	=> 'Allow Customers only to terminate booked products on last day of month',

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
	'Salesman_ProductList'			=> 'Add all Product types he gets provision for - possible: ',

	//SepaAccountController
	'SepaAccount_InvoiceHeadline'	=> 'Replaces Headline in Invoices created for this Costcenter',
	'SepaAccount_InvoiceText'		=> 'The Text of the separate four \'Invoice Text\'-Fields is automatically chosen dependent on the total charge and SEPA Mandate and is set in the appropriate Invoice for the Customer. It is possible to use all data field keys of the Invoice Class as placeholder in the form of {fieldname} to build a kind of template. These are replaced by the actual value of the Invoice.',

 /**
  *	MODULE: ProvBase	
  */
 	//ModemController
	'Modem_NetworkAccess'			=> 'Disable/Enable Network Access - Take Care: If Billing-Module is installed this Checkbox will be overwritten daily during check of valid Tariff Item',

 /**
  *	MODULE: ProvVoip	
  */
 	//PhonenumberManagementController
	'PhonenumberManagement_CarrierIn'=> 'In case of a new number set this to EnviaTEL',
	'PhonenumberManagement_EkpIn'	=> 'In case of a new number set this to EnviaTEL',
];