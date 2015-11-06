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


	/**
	 *	Returns a array of all children objects of $this object
	 *  Note: - Must be called from object context
	 *        - this requires straight forward names of tables an
	 *          forgein key, like modem and modem_id.
	 *
	 *	@author Torsten Schmidt
	 *
	 *	@return array off all children objects
	 */
	public function get_all_children()
	{
		$relations = array();

		// Lookup all SQL Tables
		foreach (DB::select('SHOW TABLES') as $table)
		{
			// Lookup SQL Fields for current $table
			foreach (Schema::getColumnListing($table->Tables_in_db_lara) as $column)
			{
				// check if $coloumn is actual table name object added by '_id'
				if ($column == $this->table.'_id')
				{
					// get all objects with $column
					foreach (DB::select('SELECT id FROM '.$table->Tables_in_db_lara.' WHERE '.$column.'='.$this->id) as $child)
					{
						$class_child_name = 'Models\\'.ucfirst($table->Tables_in_db_lara);
						$class = new $class_child_name;
						array_push($relations, $class->find($child->id));
					}
				}
			}
		}

		return $relations;
	}


	/**
	 *	Recursive delete of all children objects
	 *
	 *	@author Torsten Schmidt
	 *
	 *	@return true if success
	 *  
	 *  TODO: return state should take care of deleted children
	 */
	public function delete()
	{
		dd( $this->getArrayableItems() );
		foreach ($this->get_all_children() as $child)
			$child->delete();

		return parent::delete();
	}


	/**
	 * 
	 */
	public static function destroy($ids)
	{
		$instance = new static;

		foreach ($ids as $id => $help)
			$instance->findOrFail($id)->delete();
	}
}
