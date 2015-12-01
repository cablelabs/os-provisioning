<?php

namespace Modules\HfcCustomer\Entities;

use Modules\ProvBase\Entities\Modem;

class ModemHelper extends \BaseModel {

	// TODO: use should be from a global config api
	private static $single_critical_us = 55;
	private static $avg_critical_percentage = 50;
	private static $avg_warning_percentage = 70;
	private static $avg_critical_us = 52;
	private static $avg_warning_us = 45;


	public static function ms_num ( $s )
	{
		return Modem::whereRaw("$s AND status > 0")->get()->count();
	}

	public static function ms_num_all ( $s )
	{
		return Modem::whereRaw("$s")->get()->count();
	}

	public static function ms_avg ( $s )
	{
	    return round(Modem::whereRaw("$s AND status > 0")->avg('status')/10,1);
	}

	public static function ms_cri ( $s )
	{
		$c = 10*self::$single_critical_us;
		return ( Modem::whereRaw("(($s) AND status > $c)")->get()->count() );
	}

	public static function ms_state ( $s )
	{
	        $all = self::ms_num_all ($s);
	        if ($all == 0)
	                return -1;

	        $onl = self::ms_num ($s);
	        $avg = self::ms_avg ($s);

	        if ($onl / $all * 100 < self::$avg_critical_percentage)
	                return 'CRITICAL';
	        if ($onl / $all * 100 < self::$avg_warning_percentage)
	                return 'WARNING';

	        if ($avg > self::$avg_critical_us)
	                return 'CRITICAL';
	        if ($avg > self::$avg_warning_us)
	                return 'WARNING';

	        return 'OK';
	}

	public static function ms_state_to_color ($s)
	{
		if ($s == 'OK')       return 'green';
		if ($s == 'WARNING')  return 'yellow';
		if ($s == 'CRITICAL') return 'red';

		return -1;
	}


	public static function ms_avg_pos ( $s )
	{
	    $q = Modem::whereRaw("$s AND status > 0");

	    return [ 'x' => round($q->avg('x'),4), 'y' => round($q->avg('y'),4) ];
	}

}
