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


	/*
	 * return the rotated array ($a)
	 * Example: [1,2,3,4] -> [4,1,2,3]
	 *
	 * @param $a: The Array to rotate
	 * @return: The rotated/shifted array
	 *
	 * @author: Torsten Schmidt
	 */
	public static function array_rotate($a)
	{
		return array_merge(array(array_pop($a)), $a);
	}


	/*
	 * return nested array depth
	 *
	 * @param $a: The Array to check
	 * @return: the nested array depth as int
	 *
	 * @author: Torsten Schmidt
	 */
	public static function array_depth(array $array)
	{
		$max_depth = 1;

		foreach ($array as $value) {
			if (is_array($value)) {
				$depth = ArrayHelper::array_depth($value) + 1;

				if ($depth > $max_depth) {
					$max_depth = $depth;
				}
			}
		}

		return $max_depth;
	}

}