<?php

/**
 *	Class to add functionality – use instead of Eloquent for your models
 */
class BaseModel extends \Eloquent
{

	/**
	 * Basefunction for generic use - is needed to place the related html links generically in the edit & create views
	 * Place this function in the appropriate model and return the relation to the model it belongs
	 */
	public function view_belongs_to ()
	{
		return null;
	}

	/**
	 * Basefunction for returning all objects that a model can have a relation to
	 * Place this function in the model where the edit/create view shall show all related objects
	 *
	 * @author Nino Ryschawy 
	 *
	 * @return an array with the appropriate hasMany()-functions of the model
	 */
	public function view_has_many ()
	{
		return array();
	}
	

	/**
	 *	This returns an array with all possible enum values.
	 *	Use this instead of hardcoding it e.g. in your view (where it has to be
	 *		changed with changing/extending enum definition in database)
	 *	You can also get an array with a first empty option – use this in create forms to
	 *		show that this value is still not set
	 *	call this method via YourModel::getPossibleEnumValues('yourEnumCol')
	 *
	 *	This method is following an idea found on:
	 *		http://stackoverflow.com/questions/26991502/get-enum-options-in-laravels-eloquent
	 *
	 *	@author Patrick Reichel
	 *
	 *	@param name column name of your database defined as enum
	 *	@param with_empty_option should an empty option be added?
	 *
	 *	@return array with available enum options
	 */
	public static function getPossibleEnumValues($name, $with_empty_option=false)
	{
		// create an instance of the model to be able to get the table name
		$instance = new static;

		// get metadata for the given column and extract enum options
		$type = DB::select( DB::raw('SHOW COLUMNS FROM '.$instance->getTable().' WHERE Field = "'.$name.'"') )[0]->Type;
		preg_match('/^enum\((.*)\)$/', $type, $matches);

		$enum_values = array();

		// add an empty option if wanted
		if ($with_empty_option) {
			$enum_values[0] = '';
		}

		// add options extracted from database
		foreach(explode(',', $matches[1]) as $value){
			$v = trim( $value, "'" );
			$enum_values[$v] = $v;
		}

		return $enum_values;
	}


	/**
	 * Generic function to build a list with key of id
	 * @param $array 	
	 * @return $ret 	list
	 */
	public function html_list ($array, $column)
	{
		$ret = array();

		foreach ($array as $a)
		{
			$ret[$a->id] = $a->{$column};	
		}

		return $ret;
	}	


	// Placeholder
	public static function get_view_header()
	{
		return 'Need to be Set !';
	}

	// Placeholder
	public function get_view_link_title()
	{
		return 'Need to be Set !';
	}
}
