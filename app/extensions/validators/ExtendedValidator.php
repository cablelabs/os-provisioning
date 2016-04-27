<?php 

namespace Acme\Validators;

use Models\Configfiles;
use File;
use Log;
use Modules\Billingbase\Entities\Product;

/*
 * Our own ExtendedValidator Class
 *
 * IMPORTANT: add Validator::extend('xyz', 'ExtendedValidator@validateXyz'); to 
 * ExtendedValidatorServiceProvider under app/Providers
 */
class ExtendedValidator
{
	
	public function notNull($attribute, $value, $parameters)
	{
		if ($value == '' || $value == '0' || $value == null || $value == '0000-00-00')
			return false;
		return true;
	}

	/*
	 * Checks if value of a data field is zero when another field has specific values (declared in parameters)
	 * NOTE: the fields name has to be replaced by prep_rules-function of the Controller with the value of the data of this field
	 */
	public function nullIf($attribute, $value, $parameters)
	{
		$data = $parameters[0];
		// dd($attribute, $value, $parameters);
		unset($parameters[0]);
		
		if (in_array($data, $parameters))
		{
			if ($value == 0 || $value == null)
				return true;
			return false;
		}

		return true;
	}


	/*
	 * MAC validation
	 *
	 * see: http://blog.manoharbhattarai.com.np/2012/02/17/regex-to-match-mac-address/
	 *      http://stackoverflow.com/questions/4260467/what-is-a-regular-expression-for-a-mac-address
	 */
	public function validateMac ($attribute, $value, $parameters)
	{
		return preg_match ('/^(([0-9A-Fa-f]{2}[-:]){5}[0-9A-Fa-f]{2})$|^(([0-9A-Fa-f]{4}[.]){2}[0-9A-Fa-f]{4})$|^([0-9A-Fa-f]{12})$/', $value);
	}

	/*
	 * IP validation
	 * see: http://www.mkyong.com/regular-expressions/how-to-validate-ip-address-with-regular-expression/
	 */
	public function validateIpaddr ($attribute, $value, $parameters)
	{
		return preg_match ('/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/', $value);
	}


	/**
	 * Check if ip ($value) is inside the ip range of a net
	 *
	 * @param 1 net_ip
	 * @param 2 netmask
	 * @return true if inside
	 *
	 * @author Nino Ryschawy
	 */
	public function validateIpInRange ($attribute, $value, $parameters)
	{
		// calculate netmask for cidr notation (e.g.: 10.0.0.0/21)
		$netmask = ip2long($parameters[1]);
		$base = ip2long('255.255.255.255');
		// $prefix = 32-log(($netmask ^ $base)+1,2);

		$ip = ip2long($value);
		$start = ip2long($parameters[0]);
		// $end = $start + (1 << (32 - $prefix)) -1;
		$end = $start + (1 << log(($netmask ^ $base)+1,2)) -1;

	    return ($ip >= $start && $ip <= $end);
	}


	/**
	 * Check if ip ($value) is larger than the one specified in $parameters
	 *
	 * @author Nino Ryschawy
	 */
	public function ipLarger ($attribute, $value, $parameters)
	{
		$ip = ip2long($value);
		$ip2 = ip2long($parameters[0]);

	    return ($ip > $ip2);
	}


	/**
	 * Check if ip ($value) is larger than the one specified in $parameters
	 *
	 * @author Nino Ryschawy
	 */
	public function netmask ($attribute, $value, $parameters)
	{
		$netmask = ip2long($value);
		$base = ip2long('255.255.255.255');
		$prefix = log(($netmask ^ $base)+1,2);

		$number = (int) (10000 * $prefix);
		return is_int($number /= 10000);
	}

	/*
	 * Validates Sepa Creditor Id
	 * see: https://github.com/AbcAeffchen/SepaUtilities/blob/master/src/SepaUtilities.php
	 */
	public function validateCreditorId($attribute, $value, $parameters)
	{
		$country_code = substr($value, 0, 2);

		// TODO: add more countries, improve by check against check digits
		$creditor_id_length = array(
			'AT' => 18,			// Austria
			'BE' => 20,			// Belgium
			'DE' => 18,			// Germany
			'EE' => 20,			// Estonia
			'ES' => 16,
			'FR' => 13,
			'IT' => 23,
			'LU' => 26,			// Luxembourg
			'NL' => 19,
			'PT' => 13,			// Portugal
			);

		if (strlen($value) != (isset($creditor_id_length[$country_code]) ? $creditor_id_length[$country_code] : 1000))
			return false;

		return preg_match('#^[a-zA-Z]{2,2}[0-9]{2,2}([A-Za-z0-9]|[\+|\?|/|\-|:|\(|\)|\.|,|\']){3,3}([A-Za-z0-9]|[\+|\?|/|\-|:|\(|\)|\.|,|\']){1,28}$#', $value);
	}


	/*
	 * Geoposition validation
	 * see: http://stackoverflow.com/questions/7113745/what-regex-expression-will-check-gps-values
	 */
	public function validateGeopos ($attribute, $value, $parameters)
	{
		return preg_match ('/(-?[\d]{1,3}\.[\d]{0,12},?){2}/', $value);
	}


	/*
	 * Date or Null
	 */
	public function validateDateOrNull ($attribute, $value, $parameters)
	{
		if ($value == '0000-00-00')
			return true;

		// See: http://stackoverflow.com/questions/13194322/php-regex-to-check-date-is-in-yyyy-mm-dd-format
		$dt = \DateTime::createFromFormat("Y-m-d", $value);
		return $dt !== false && !array_sum($dt->getLastErrors());
	}


	/*
	 * DOCSIS configfile validation
	 */
	public function validateDocsis ($attribute, $value, $parameters)
	{
		// dd($attribute, $value, $parameters);

		/* Configfile */
        $device	 = $parameters[0];
        if ($device == null)
        	return false;
        $dir     = "/tftpboot/$device";
        $cf_file = $dir."dummy-validator.conf";

		/*
		 * Replace all {xyz} content  
		 */
		$rows = explode("\n", $value);
		$s = '';
		foreach ($rows as $row)
		{
			if (preg_match("/(string)/i", $row))
				$s .= "\n".preg_replace("/\\{[^\\{]*\\}/im", 'text', $row);
			elseif (preg_match("/(ipaddress)/i", $row))
				$s .= "\n".preg_replace("/\\{[^\\{]*\\}/im", '1.1.1.1', $row);
			else
				$s .= "\n".preg_replace("/\\{[^\\{]*\\}/im", '1', $row);
		}
		
		/*
		 * Write Dummy File and try to encode
		 */
        $text = "Main\n{\n\t".$s."\n}";
        $ret  = File::put($cf_file, $text);

        if ($ret === false)
                die("Error writing to file");

        if ($device == 'cm')
        {
	        Log::info("Validation: /usr/local/bin/docsis -e $cf_file $dir/../keyfile $dir/dummy-validator.cfg");   
	        exec("rm -f $dir/dummy-validator.cfg && /usr/local/bin/docsis -e $cf_file $dir/../keyfile $dir/dummy-validator.cfg 2>&1", $outs);
        }
        elseif ($device == 'mta')
        {
	        Log::info("Validation: /usr/local/bin/docsis -p $cf_file $dir/dummy-validator.cfg");   
	        exec("rm -f $dir/dummy-validator.cfg && /usr/local/bin/docsis -p $cf_file $dir/dummy-validator.cfg 2>&1", $outs, $ret);	//return value is always 0
		}

        /*
         * Parse Errors - only one error is shown - subtract 3 from line nr
         */
        $report = $outs[0];
        preg_match('/[0-9]+$/', $report, $i);
        if (isset($i[0]))
        	$report = preg_replace('/[0-9]+$/', $i[0] - 3, $report);
        // foreach ($outs as $out)
        // {
        // 	$report .= "\n$out";
        // }

        if (!file_exists("$dir/dummy-validator.cfg"))
        {
        	// see: https://laracasts.com/discuss/channels/general-discussion/extending-validation-with-custom-message-attribute?page=1
        	// when laravel calls the actual validation function (validate) they luckily pass "$this" that is the Validator instance as 4th argument - so we can get it here
        	$validator = \func_get_arg(3);
        	$validator->setCustomMessages(array('docsis' => $report));
        	return false;
        }
        
		return true;
	}

	// $value (field value) must only contain strings of product type enums
	public function validateProductType($attribute, $value, $parameters)
	{
		$types = Product::getPossibleEnumValues('type');

		$tmp   = str_replace([',', '|', '/', ';'], ' ', $value);
		$prods = explode(' ', $tmp);

		foreach ($prods as $type)
		{
			// skip empty strings
			if (!$type)
				continue;
			if (!in_array($type, $types))
				return false;
		}

		return true;
	}

}



