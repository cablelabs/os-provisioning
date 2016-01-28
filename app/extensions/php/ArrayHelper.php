<?php

namespace Acme\php;

/*
 * All Stuff of Array Helper Functions should be placed here ..
 */
class ArrayHelper {

	/*
	 * Search if $value is in $array field $index
	 *
	 * @param: array: array to search
	 * @param: array: the array[].index field to search in
	 * @param: array: search pattern
	 * @return: the found element, otherwise null
	 *
	 * @author: Torsten Schmidt
	 */
    public static function objArraySearch($array, $index, $value)
    {
        foreach($array as $arrayInf) {
            if($arrayInf->{$index} == $value) {
                return $arrayInf;
            }
        }
        return null;
    }


    /* 
     * Device all entrys of an Array by $div
     *
     * @param $array: The Array to split
     * @param $div: device by $div
     * @return: The devided array
     *
     * @author: Torsten Schmidt
     */
    public static function ArrayDiv($array, $div=10)
    {
        $ret = [];

        foreach ($array as $a) 
        {
            array_push($ret, $a/$div);
        }

        return $ret;
    }

}