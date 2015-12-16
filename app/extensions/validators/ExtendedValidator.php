<?php 

namespace Acme\Validators;

use Models\Configfiles;


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
		/* Configfile */
        $dir     = '/tftpboot/cm/';
        $cf_file = $dir."dummy-validator.conf";

		/*
		 * Replace all {xyz} content  
		 */
		$rows = explode("\n", $value);
		$s = '';
		foreach ($rows as $row)
		{
			if (preg_match("/(string)/i", $row))
				$s .= "\n".preg_replace("/\\{[^\\{]*\\}/im", '"text"', $row);
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
         
        Log::info("/usr/local/bin/docsis -e $cf_file $dir/../keyfile $dir/cm-dummy-validator.cfg");   
        exec("rm -f $dir/dummy-validator.cfg && /usr/local/bin/docsis -e $cf_file $dir/../keyfile $dir/dummy-validator.cfg 2>&1", $outs, $ret);

        /*
         * Parse Errors
         */
        $report = '';
        foreach ($outs as $out)
        	$report .= "\n$out";

        if (!file_exists("$dir/dummy-validator.cfg"))
        {
        	$this->setCustomMessages(array('docsis' => $report));
        	return false;
        }
        
		return true;
	}
}



