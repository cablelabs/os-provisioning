<?php

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 *	Class to add functionality – use instead of Eloquent for your models
 */
class BaseModel extends Eloquent
{
	use SoftDeletes;

	public $voip_enabled;
	public $billing_enabled;


	/**
	 * Constructor.
	 * Used to set some helper variables.
	 *
	 * @author Patrick Reichel
	 *
	 * @param $attributes pass through to Eloquent contstructor.
	 */
	public function __construct($attributes = array()) {

		// call Eloquent constructor
		// $attributes are needed! (or e.g. seeding and creating will not work)
		parent::__construct($attributes);

		// set helper variables
		$this->voip_enabled = $this->voip_enabled();
		$this->billing_enabled = $this->billing_enabled();

	}


	/**
	 * check if module exists
	 *
	 * Note: This function should be used in relational functions like hasMany() or view_has_many()
	 *
	 * @author Torsten Schmidt
	 *
	 * @param  Modulename
	 * @return true if module exists and is active otherwise false
	 */
	public function module_is_active($modulename)
	{
		$modules = \Module::enabled();

		foreach ($modules as $module)
			if ($module->getLowerName() == strtolower($modulename))
				return true;

        return false;
	}


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
	 * Basefunction for returning all objects that a model can have a one-to-one relation to
	 * Place this function in the model where the edit/create view shall show all related objects
	 *
	 * @author Patrick Reichel
	 *
	 * @return an array with the appropriate hasOne()-functions of the model
	 */
	public function view_has_one ()
	{
		return array();
	}


	/**
	 * Check if VoIP is enabled.
	 *
	 * @author Patrick Reichel
	 *
	 * @return true if one of the VoIP modules is enabled (currently only ProvVoipEnvia), else false
	 */
	public function voip_enabled() {

		$voip_modules = array(
			'ProvVoipEnvia',
		);

		foreach ($voip_modules as $module) {
			if ($this->module_is_active($module)) {
				return True;
			}
		}

		return False;
	}


	/**
	 * Check if billing is enabled.
	 *
	 * TODO currently this is a dummy (= we don't have a billing module yet!!)
	 *
	 * @author Patrick Reichel
	 *
	 * @return true if one of the billing modules is enabled, else false
	 */
	public function billing_enabled() {

		// TODO: delete next line to activate this method!!
		return True;

		$billing_modules = array(
		);

		foreach ($billing_modules as $module) {
			if ($this->module_is_active($module)) {
				return True;
			}
		}

		return False;
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
	 * @author Patrick Reichel,
	 *         Torsten Schmidt: add modules path
	 */
	public static function get_models() {

		// models to be excluded from search
		$exclude = array(
			'BaseModel',
			'Authmeta',
			'Authcore'
		);
		$result = array();

		/*
		 * Search all Models in /models Models Path
		 */
		$dir = app_path('Models');
		$models = glob($dir."/*.php");

		foreach ($models as $model) {
			$model = str_replace(app_path('Models')."/", "", $model);
			$model = str_replace(".php", "", $model);
			if (array_search($model, $exclude) === FALSE) {
				array_push($result, $model);
			}
		}

		/*
		 * Search all Models in /Modules/../Entities Path
		 */
		$path = base_path('modules');
		$dirs = array();
		$modules = Module::enabled();
		foreach ($modules as $module)
			array_push($dirs, $module->getPath().'/Entities');

		foreach ($dirs as $dir)
		{
			$models = glob($dir."/*.php");

			foreach ($models as $model) {
				preg_match ("|$path/(.*?)/Entities/|", $model, $module_array);
				$module = $module_array[1];
				$model = preg_replace("|$path/(.*?)/Entities/|", "", $model);
				$model = str_replace(".php", "", $model);
				if (array_search($model, $exclude) === FALSE) {
					array_push($result, "Modules\\$module\Entities\\".$model);
				}
			}
		}
		
		return $result;
	}


	protected function _guess_model_name ($s)
	{
		return current(preg_grep ('|.*?'.$s.'$|i', $this->get_models()));
	}

	/*
	 * Preselect a sql field while searching
	 *
	 * Note: If $field is 'net' or 'cluster' we perform a net and cluster specific search
	 * This requires the searched model to have a tree_id coloumn
	 *
	 * @param $field sql field for pre selection
	 * @param $field sql search value for pre selection
	 * @return sql search statement, could be included in a normal while()
	 * @author Torsten Schmidt
	 */
	private function __preselect_search($field, $value, $model)
	{
		$ret = '1';

		if ($field && $value)
		{
			$ret = $field.'='.$value;

			if($this->module_is_active('Hfcbase'))
			{
				if (($model[0] == 'Modules\ProvBase\Entities\Modem') && ($field == 'net' || $field == 'cluster'))
				{
					$ret = 'tree_id IN(-1';
					foreach (Modules\HfcBase\Entities\Tree::where($field, '=', $value)->get() as $tree) 
						$ret .= ','.$tree->id;
					$ret .= ')';
				}
			}
		}

		return $ret;
	}


	/**
	 * Performs a fulltext search in simple mode
	 *
	 * @param $array with models to search in
	 * @param $query query to search for
	 * @param $preselect_field sql field for pre selection
	 * @param $preselect_field sql search value for pre selection
	 * @return search result: array of whereRaw() results, this means array of class Illuminate\Database\Quer\Builder objects
	 * @author Patrick Reichel, 
	 *         Torsten Schmidt: add preselection, add Model checking
	 */
	protected function _doSimpleSearch($_models, $query, $preselect_field=null, $preselect_value=null) 
	{
		$preselect = $this->__preselect_search($preselect_field, $preselect_value, $_models);

		/*
		 * Model Checking: Prepare $models array: skip Models without a valid SQL table
		 */
		$models = [];
		foreach ($_models as $model)
		{
			if (!class_exists($model))
				continue;

			$tmp = new $model;

			if (!property_exists($tmp, 'table'))
				continue;

			if (!Schema::hasTable($tmp->table))
				continue;

			array_push ($models, $model);
		}

		/*
		 * Perform the search
		 */
		$result = [];
		foreach ($models as $model) 
		{
			// get the database table used for given model
			$tmp = new $model;
			$table = $tmp->getTable();
			$cols = $model::getTableColumns($table);

			$tmp_result = $model::whereRaw("($preselect) AND CONCAT_WS('|', ".$cols.") LIKE ?", array($query));
			if ($tmp_result) 
				array_push($result, $tmp_result);

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
	 * @return search result array of whereRaw() results, this means array of class Illuminate\Database\Quer\Builder objects
	 *
	 * @author Patrick Reichel
	 */
	public function getFulltextSearchResults($scope, $mode, $query, $preselect_field = null, $preselect_value = null) {

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
				$models = $this->get_models();
				$preselect_field = $preselect_value = null;
			}
			else {
				$models = array(get_class($this));
			}

			$result = $this->_doSimpleSearch($models, $query, $preselect_field, $preselect_value);
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
				$result = [$this->whereRaw("MATCH(".$indexed_cols.") AGAINST(? ".$mode.")", array($query))];
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
						$class_child_name = $this->_guess_model_name ($table->Tables_in_db_lara);
						$class = new $class_child_name;

						array_push($relations, $class->find($child->id));
					}
				}
			}
		}

		return array_filter ($relations);
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
		// dd( $this->get_all_children() );
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


/**
 * Base Observer Class
 * Handles changes on Model Gateways
 *
 * TODO: place it somewhere else ..
 *
 */
class SystemdObserver
{

	// insert all services that need to be restarted after a model changed there configuration in that array
	private $services = array('dhcpd');

    public function created($model)
    {
    	if (!is_dir(storage_path('systemd')))
    		mkdir(storage_path('systemd'));

    	foreach ($this->services as $service)
    	{
			touch(storage_path('systemd/'.$service));
    	}
	}

    public function updated($model)
    {
    	if (!is_dir(storage_path('systemd')))
    		mkdir(storage_path('systemd'));

    	foreach ($this->services as $service)
    	{
			touch(storage_path('systemd/'.$service));
    	}
    }

    public function deleted($model)
    {
    	if (!is_dir(storage_path('systemd')))
    		mkdir(storage_path('systemd'));

    	foreach ($this->services as $service)
    	{
			touch(storage_path('systemd/'.$service));
    	}
    }
}
