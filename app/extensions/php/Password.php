<?php

namespace Acme\php;

/*
 * All Stuff of Array Helper Functions should be placed here ..
 */
class Password
{
    /**
     * Generates a password.
     *
     * @author Torsten Schmidt, Patrick Reichel
     * @param $length length of the password
     * @param $target used for example to use different character sets. (implemented is envia).
     */
    public static function generate_password($length = 10, $target = '')
    {
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';

        // define the characters to be used in passwords
        if ($target == 'envia') {
            // envia TEL accepts alphanumericals and ._-
            // we use letter as first character to avoid possible problems with some exotic clients…
            $specials = '._-';

            $first_chars = $letters;
            $other_chars = $first_chars.$numbers.$specials;
        } else {
            // this creates alphanumerical passwords only
            $first_chars = $letters.$numbers;
            $other_chars = $first_chars;
        }

        // add first char
        $char_len = strlen($first_chars);
        $char_idx = mt_rand(0, $char_len - 1);
        $password = substr($first_chars, $char_idx, 1);

        // add other chars
        $char_len = strlen($other_chars);
        for ($i = 1; $i < $length; $i++) {
            $char_idx = mt_rand(0, $char_len - 1);
            $password .= substr($other_chars, $char_idx, 1);
        }

        return $password;
    }
}
