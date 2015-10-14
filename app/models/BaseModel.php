<?php

/**
 *	Class to add functionality – use instead of Eloquent for your models
 */
class BaseModel extends \Eloquent
{

	/**
	 *	This returns an array with all possible enum values.
	 *	Use this instead of hardcoding it e.g. in your view (where it has to be
	 *		changed with changing/extending enum definition in database)
	 *	This is following an idea found on:
	 *		http://stackoverflow.com/questions/26991502/get-enum-options-in-laravels-eloquent
	 *	call this method via YourModel::getPossibleEnumValues('yourEnumCol')
	 *
	 *	@param column name of your database defined as enum
	 *	@author Patrick Reichel
	 */
	public static function getPossibleEnumValues($name)
	{
		// create an instance of the model to be able to get the table name
		$instance = new static;

		// get metadata for the given column and extract enum options
		$type = DB::select( DB::raw('SHOW COLUMNS FROM '.$instance->getTable().' WHERE Field = "'.$name.'"') )[0]->Type;
		preg_match('/^enum\((.*)\)$/', $type, $matches);
		$enum = array();
		foreach(explode(',', $matches[1]) as $value){
			$v = trim( $value, "'" );
			$enum[] = $v;
		}

		return $enum;
	}
}
