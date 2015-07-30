<?php 

use Illuminate\Validation\Validator;
use Models\Configfiles;


/*
 * Our own ExtendedValidator Class
 *
 *
 * Extended Validator is loaded in start/global.php
 * and will be used as normal Validator.
 * TODO: Maybe we should use a service provider instead ?
 */
class ExtendedValidator extends Validator
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
	 * DOCSIS configfile validation
	 * TODO: quick & dirty implementation
	 *       use sub-functions & stuff
	 */
	public function validateDocsis ($attribute, $value, $parameters)
	{
		/* Configfile */
        $dir     = '/tftpboot/cm/';
        $cf_file = $dir."dummy-validator.conf";

		/*
		 * Delete all {xyz} content - 
		 * TODO: will not be tested !
		 *       better way will be to replace with default values.
		 */
		$rows = explode("\n", $value);
		$s = '';
		foreach ($rows as $row)
			if (!preg_match("/\\{[^\\{]*\\}/im", $row))
				$s .= "\n\t".$row;
		
        $text = "Main\n{\n\t".$s."\n}";
        $ret  = File::put($cf_file, $text);
        
        if ($ret === false)
                die("Error writing to file");
         
        Log::info("/usr/local/bin/docsis -e $cf_file $dir/../keyfile $dir/cm-dummy-validator.cfg");   
        exec("rm -f $dir/dummy-validator.cfg && /usr/local/bin/docsis -e $cf_file $dir/../keyfile $dir/dummy-validator.cfg 2>&1", $outs, $ret);

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



