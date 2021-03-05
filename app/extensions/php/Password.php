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
     * @param $except characters to exclude for default target
     */
    public static function generatePassword(int $length = 10, string $target = '', string $except = 'IOlo01'): string
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
            $first_chars = str_replace(str_split($except), '', $letters.$numbers);
            $other_chars = $first_chars;
        }

        // add first char
        $char_len = strlen($first_chars);
        $char_idx = random_int(0, $char_len - 1);
        $password = substr($first_chars, $char_idx, 1);

        // add other chars
        $char_len = strlen($other_chars);
        for ($i = 1; $i < $length; $i++) {
            $char_idx = random_int(0, $char_len - 1);
            $password .= substr($other_chars, $char_idx, 1);
        }

        return $password;
    }
}
