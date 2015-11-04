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
	 * Get the names of all fulltext indexed database columns.
	 * They have to be passed as a param to a MATCH-AGAINST query
	 *
	 * @param $table database to get index columns from
	 * @return comma separated string of columns
	 * @author Patrick Reichel
	 */
	protected function _getFulltextIndexColumns($table) {

		$cols = array();
		$indexes = DB::select(DB::raw('SHOW INDEX FROM '.$table));
		foreach ($indexes as $index) {
			if (($index->Key_name == $table.'_fulltext_all') && $index->Index_type == 'FULLTEXT') {
				array_push($cols, $index->Column_name);
			}
		}

		$cols = implode(',', $cols);
		return $cols;
	}


	/**
	 * Get all database fields
	 *
	 * @param table database table to get structure from
	 * @return comma separated string of columns
	 * @author Patrick Reichel
	 */
	protected function _getTableColumns($table) {

		$fields = array();
		$cols = DB::select( DB::raw('SHOW COLUMNS FROM '.$table.' WHERE Field = "'.$name.'"'));
		foreach ($cols as $col) {
			array_push($result, $col->Field);
		}

		$fields = implode(',', $fields);
		return $fields;
	}


	/**
	 * Switch to decide with search algo shall be used
	 * Here we can add other conditions (e.g. to force mode simple on mac search or %truncation)
	 */
	protected function _chooseFulltextSearchAlgo($mode, $query) {

		// search query is left truncated => simple search
		if ((strpos($query, "%") === 0) || (strpos($query, "*") === 0)) {
			$mode = 'simple';
			// select * from modem where CONCAT(mac, description, id) LIKE "%100001%";
		}

		// query contains . or : => IP or MAC => simple search
		if ((strpos($query, ":") !== false) || (strpos($query, ".") !== false)) {
			$mode = 'simple';
		}

		return $mode;
	}


	/**
	 * Get results for a fulltext search
	 *
	 * @author Patrick Reichel
	 */
	public function getFulltextSearchResults($scope, $mode, $query) {

		$mode = $this->_chooseFulltextSearchAlgo($mode, $query);

		if ($mode == 'simple') {

			if ($scope == 'all') {
				echo "Implement searching over all database tables";
			}
			else {
				$result = 'to get';
				CRASH;
			}
		}
		elseif (strpos($mode, 'index_') === 0) {

			if ($scope == 'all') {
				echo "Implement searching over all database tables";
			}
			else {
				$indexed_cols = $this->_getFulltextIndexColumns($this->getTable());

				if ("index_natural" == $mode) {
					$mode = "IN NATURAL MODE";
				}
				elseif ("index_boolean" == $mode) {
					$mode = "IN BOOLEAN MODE";
				}
				else {
					$mode = "IN BOOLEAN MODE";
				}

				$result = $this->whereRaw("MATCH(".$indexed_cols.") AGAINST(? ".$mode.")", array($query))->get();
			}
		}
		else {
			$result = null;
		}

		echo "DEBUG<br><pre>";
		dd($result);
		return $result;

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
}
