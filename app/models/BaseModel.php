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
	 * Get all models
	 *
	 * @return array of all models except base models
	 * @author Patrick Reichel
	 */
	protected function _getModels() {
		$exclude = array('BaseModel');

		$dir = app_path('models');
		$models = glob($dir."/*.php");

		$result = array();
		foreach ($models as $model) {
			$model = str_replace(app_path('models')."/", "", $model);
			$model = str_replace(".php", "", $model);
			if (array_search($model, $exclude) === FALSE) {
				array_push($result, "Models\\".$model);
			}
		}

		return $result;
	}


	/**
	 * Performs a fulltext search in simple mode
	 *
	 * @param $array with models to search in
	 * @param $query query to search for
	 * @return search result
	 * @author Patrick Reichel
	 */
	protected function _doSimpleSearch($models, $query) {

		foreach ($models as $model) {

			// get the database table used for given model
			$tmp = new $model;
			$table = $tmp->getTable();
			$cols = $model::getTableColumns($table);

			$tmp_result = $model::whereRaw("CONCAT_WS('|', ".$cols.") LIKE ?", array($query))->get();
			if (!isset($result)) {
				$result = $tmp_result;
			}
			else {
				$result = $result->merge($tmp_result);
			}
		}
		return $result;
	}

	/**
	 * Get all database fields
	 *
	 * @param table database table to get structure from
	 * @return comma separated string of columns
	 * @author Patrick Reichel
	 */
	public static function getTableColumns($table) {

		$tmp_res = array();
		$cols = DB::select( DB::raw('SHOW COLUMNS FROM '.$table));
		foreach ($cols as $col) {
			array_push($tmp_res, $table.".".$col->Field);
		}

		$fields = implode(',', $tmp_res);
		return $fields;
	}


	/**
	 * Switch to decide with search algo shall be used
	 * Here we can add other conditions (e.g. to force mode simple on mac search or %truncation)
	 */
	protected function _chooseFulltextSearchAlgo($mode, $query) {

		// search query is left truncated => simple search
		if ((Str::startsWith($query, "%")) || (Str::startsWith($query, "*"))) {
			$mode = 'simple';
		}

		// query contains . or : => IP or MAC => simple search
		if ((Str::contains($query, ":")) || (Str::contains($query, "."))) {
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

		// some searches cannot be performed against fulltext index
		$mode = $this->_chooseFulltextSearchAlgo($mode, $query);

		if ($mode == 'simple') {

			// replace wildcard chars
			$query = str_replace("*", "%", $query);
			// wrap with wildcards (if not given) => necessary because of the concatenation of all table rows
			if (!Str::startsWith($query, "%")) {
				$query = "%".$query;
			}
			if (!Str::endsWith($query, "%")) {
				$query = $query."%";
			}

			if ($scope == 'all') {
				$models = $this->_getModels();
			}
			else {
				$models = array(get_class($this));
			}

			$result = $this->_doSimpleSearch($models, $query);
		}
		elseif (Str::startsWith($mode, 'index_')) {

			if ($scope == 'all') {
				echo "Implement searching over all database tables";
			}
			else {
				$indexed_cols = $this->_getFulltextIndexColumns($this->getTable());

				# for a description of search modes check https://mariadb.com/kb/en/mariadb/fulltext-index-overview
				if ("index_natural" == $mode) {
					$mode = "IN NATURAL MODE";
				}
				elseif ("index_boolean" == $mode) {
					$mode = "IN BOOLEAN MODE";
				}
				else {
					$mode = "IN BOOLEAN MODE";
				}

				# search is against the fulltext index
				$result = $this->whereRaw("MATCH(".$indexed_cols.") AGAINST(? ".$mode.")", array($query))->get();
			}
		}
		else {
			$result = null;
		}

		/* echo "$query at $scope in mode $mode<br><pre>"; */
		/* dd($result); */
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
