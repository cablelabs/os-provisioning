<?php

namespace Acme\pingpong;

/**
 *	Our own simple helper for Ping Pong Modules
 */
class PPModule
{

	/**
	 * check if Ping Pong module is active
	 *
	 * Note: This function should be used in relational functions like hasMany() or view_has_many()
	 *
	 * @author Torsten Schmidt
	 *
	 * @param  Modulename
	 * @return true if module exists and is active otherwise false
	 */
	public static function is_active($modulename)
	{
		return \Module::find($modulename)->active();
	}

	/**
	 * disable Ping Pong module
	 *
	 * @author Ole Ernst
	 *
	 * @param  Modulename
	 */
	public static function disable($modulename)
	{
		\Module::find($modulename)->disable();
	}

}