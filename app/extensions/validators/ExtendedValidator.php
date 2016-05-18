<?php

namespace Acme\Validators;

use Models\Configfiles;
use File;
use Log;

use Modules\ProvVoip\Entities\PhonebookEntry;

/*
 * Our own ExtendedValidator Class
 *
 * IMPORTANT: add Validator::extend('xyz', 'ExtendedValidator@validateXyz'); to
 * ExtendedValidatorServiceProvider under app/Providers
 */
class ExtendedValidator
{
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
		dd($parameters);
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


	/**
	 * Check if given string (freetext) for phonebookentry is valid.
	 *
	 * @author Patrick Reichel
	 */
	public function validatePhonebookString($attribute, $value, $parameters) {

		// see: https://laracasts.com/discuss/channels/general-discussion/extending-validation-with-custom-message-attribute?page=1
		// when laravel calls the actual validation function (validate) they luckily pass "$this" that is the Validator instance as 4th argument - so we can get it here
		$validator = \func_get_arg(3);

		// for easier access and improved readability: get needed informations out of config
		$maxlen = PhonebookEntry::$config[$attribute]['maxlen'];
		$valid = str_split(PhonebookEntry::$config[$attribute]['valid']);

		// check if given value is to long
		if (strlen($value) > $maxlen) {
			$validator->setCustomMessages(array('phonebook_string' => $attribute.' to long (max. '.$maxlen.' characters allowed)'));
			return false;
		}

		// check all value's characters against the list of valid chars
		$invalids = array();
		foreach (str_split($value) as $char) {
			if (!in_array($char, $valid)) {
				array_push($invalids, $char);
			}
		}
		// input invalid if at least one invalid character has been found
		if (boolval($invalids)) {
			$validator->setCustomMessages(array('phonebook_string' => 'The following characters are not allowed in '.$attribute.': '.implode('', array_unique($invalids))));
			return false;
		}

		// all fine => valid
		return true;

	}


	/**
	 * Check if given string (predefined) for phonebookentry is valid.
	 * The valid values are defined by Telekom and delivered in several files.
	 *
	 * @author Patrick Reichel
	 */
	public function validatePhonebookPredefinedString($attribute, $value, $parameters) {

		// see: https://laracasts.com/discuss/channels/general-discussion/extending-validation-with-custom-message-attribute?page=1
		// when laravel calls the actual validation function (validate) they luckily pass "$this" that is the Validator instance as 4th argument - so we can get it here
		$validator = \func_get_arg(3);

		// attention: data coming into validators are still htmlencoded from view level!
		$search = html_entity_decode($value);

		// use the method that builds the array for the selects => that contains all valid values…
		if (!array_key_exists($search, PhonebookEntry::get_options_from_file($attribute))) {
			$validator->setCustomMessages(array('phonebook_predefined_string' => $value.' is not a valid value for '.$attribute));
			return false;
		}

		return true;
	}


	/**
	 * Check if given one character option is valid
	 *
	 * @author Patrick Reichel
	 */
	public function validatePhonebookOneCharacterOption($attribute, $value, $parameters) {

		// see: https://laracasts.com/discuss/channels/general-discussion/extending-validation-with-custom-message-attribute?page=1
		// when laravel calls the actual validation function (validate) they luckily pass "$this" that is the Validator instance as 4th argument - so we can get it here
		$validator = \func_get_arg(3);

		// get the allowed chars out of config
		$valid = PhonebookEntry::$config[$attribute]['in_list'];

		if (!in_array($value, $valid)) {
			$validator->setCustomMessages(array('phonebook_one_character_option' => $value.' is not valid for '.$attribute.' (have to be in ['.implode('', $valid).']).'));
			return false;
		}

		return true;

	}


}



