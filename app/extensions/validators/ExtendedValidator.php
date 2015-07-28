<?php 

namespace app\extensions\validators;

use Illuminate\Support\Facades\Validator;


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
}


/*
 * Extend each needet function
 * Note: add custom error message to lang/xy/validation.php file
 */
Validator::extend('mac', 'app\extensions\validators\ExtendedValidator@mac');
