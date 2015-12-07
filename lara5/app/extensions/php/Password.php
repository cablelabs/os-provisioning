<?php

namespace Acme\php;

/*
 * All Stuff of Array Helper Functions should be placed here ..
 */
class Password {

	public static function generate_password( $length = 10 )
	{
		return substr( str_shuffle( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789" ), 0, $length );
	}

}