<?php 

namespace app\extensions\validators;

use Illuminate\Support\Facades\Validator;

use Models\Configfiles;
use File;
use Log;

/*
 * Our own Validator Class
 */
class ExtendedValidator 
{
	/*
	 * MAC validation
	 *
	 * see: http://blog.manoharbhattarai.com.np/2012/02/17/regex-to-match-mac-address/
	 *      http://stackoverflow.com/questions/4260467/what-is-a-regular-expression-for-a-mac-address
	 */
	public function mac ($attribute, $value, $parameters)
	{
		return preg_match ('/^(([0-9A-Fa-f]{2}[-:]){5}[0-9A-Fa-f]{2})$|^(([0-9A-Fa-f]{4}[.]){2}[0-9A-Fa-f]{4})$|^([0-9A-Fa-f]{12})$/', $value);
	}

	/*
	 * IP validation
	 * see: http://www.mkyong.com/regular-expressions/how-to-validate-ip-address-with-regular-expression/
	 */
	public function ip ($attribute, $value, $parameters)
	{
		return preg_match ('/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/', $value);
	}

	/*
	 * DOCSIS configfile validation
	 * TODO: quick & dirty implementation
	 *       use sub-functions & stuff
	 */
	public function docsis ($attribute, $value, $parameters)
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
        exec("/usr/local/bin/docsis -e $cf_file $dir/../keyfile $dir/dummy-validator.cfg", $out, $ret);

		return ($ret == 0 ? true : false);
	}
}


/*
 * Extend each needet function
 * Note: add custom error message to lang/xy/validation.php file
 */
Validator::extend('mac', 'app\extensions\validators\ExtendedValidator@mac');
Validator::extend('ip', 'app\extensions\validators\ExtendedValidator@ip');
Validator::extend('docsis', 'app\extensions\validators\ExtendedValidator@docsis');
