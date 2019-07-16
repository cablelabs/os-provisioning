<?php

namespace App;

use DB;
use Str;
use Auth;
use Module;
use Schema;
use Bouncer;
use Session;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Extensions\Database\EmptyRelation as EmptyRelation;

/**
 *	Class to add functionality – use instead of Eloquent for your models
 */
class BaseModel extends Eloquent
{
    use SoftDeletes;

    // use to enable force delete for inherit models
    protected $force_delete = 0;

    // flag showing if children also shall be deleted on Model::delete()
    protected $delete_children = true;

    public $external_voip_enabled;
    public $billing_enabled;
    protected $fillable = [];

    public $observer_enabled = true;

    /**
     * View specific stuff
     */
    // set this variable in a function model to true and implement into view_index_label() if it shall not be deletable on index page
    public $index_delete_disabled = false;

    // Add Comment here. ..
    protected $guarded = ['id'];

    /**
     * Constructor.
     * Used to set some helper variables.
     *
     * @author Patrick Reichel
     *
     * @param $attributes pass through to Eloquent contstructor.
     */
    public function __construct($attributes = [])
    {
        // Config Host Setup
        // @note: This could be used to fetch configuration tables
        //        (like configfiles) from a global NMS Prime system
        // @author: Torsten Schmidt
        $env = env('DB_CONFIG_TABLES', false);

        if ($env && strpos($env, $this->table) !== false) {
            $this->connection = 'mysql-config';
            \Log::debug('Use mysql-config connection to access '.$this->table.' table');
        }

        // call Eloquent constructor
        // $attributes are needed! (or e.g. seeding and creating will not work)
        parent::__construct($attributes);

        // set helper variables
        $this->external_voip_enabled = $this->external_voip_enabled();
        $this->billing_enabled = $this->billing_enabled();
    }

    /**
     * Helper to get the model name.
     *
     * @author Patrick Reichel
     */
    public function get_model_name()
    {
        $model_name = get_class($this);
        $model_name = explode('\\', $model_name);

        return array_pop($model_name);
    }

    /**
     * Init Observer
     */
    public static function boot()
    {
        parent::boot();

        $model_name = static::class;

        // App\Auth is booted during authentication and doesnt need/have an observe method
        // GuiLog has to be excluded to prevent an infinite loop log entry creation
        if ($model_name == 'App\Auth' || $model_name == 'App\GuiLog') {
            return;
        }

        // we simply add BaseObserver to each model
        // the real database writing part is in singleton that prevents duplicat log entries
        $model_name::observe(new BaseObserver);
    }

    /**
     * Placeholder if specific Model does not have any rules
     */
    public static function rules($id = null)
    {
        return [];
    }

    public function set_index_delete_disabled()
    {
        $this->index_delete_disabled = true;
    }

    /**
     * Basefunction for generic use - is needed to place the related html links generically in the edit & create views
     * Place this function in the appropriate model and return the relation to the model it belongs
     *
     * NOTE: this function will return null in all create contexts, because at this time no relation exists!
     */
    public function view_belongs_to()
    {
    }

    /**
     * Checks if the requested relation is installed and enabled.
     * If so all is fine – otherwise we return flag to create special empty eloquent relation.
     *
     * @return bool
     *
     * @author Patrick Reichel
     */
    protected function _relationAvailable($related)
    {

        // remove all leading backslashes
        $related = ltrim($related, '\\');

        $parts = explode('\\', $related);
        $context = $parts[0];

        // check if requested relation is in module context
        if (Str::lower($context) == 'modules') {

            // check if requested module is active
            $module = $parts[1];
            if (! \Module::collections()->has($module)) {
                return false;
            }
        }

        // in all other cases: no special handling needed – we can return the standard eloquent relation
        return true;
    }

    /**
     * Extension to original hasMany – returns an empty relation if the related module is not available.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany or \App\Extensions\Database\EmptyRelation
     * @author Patrick Reichel
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        if ($this->_relationAvailable($related)) {
            return parent::hasMany($related, $foreignKey, $localKey);
        } else {
            return new EmptyRelation();
        }
    }

    /**
     * Extension to original hasOne – returns an empty relation if the related module is not available.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne or \App\Extensions\Database\EmptyRelation
     * @author Patrick Reichel
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        if ($this->_relationAvailable($related)) {
            return parent::hasOne($related, $foreignKey, $localKey);
        } else {
            return new EmptyRelation();
        }
    }

    /**
     * Extension to original belongsTo – returns an empty relation if the related module is not available.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo or \App\Extensions\Database\EmptyRelation
     * @author Patrick Reichel
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if ($this->_relationAvailable($related)) {

            // Patrick Reichel: get $relation if not given (copied from Eloquent/Model.php to get proper backtrace)
            // If no relation name was given, we will use this debug backtrace to extract
            // the calling method's name and use that as the relationship name as most
            // of the time this will be what we desire to use for the relationships.
            if (is_null($relation)) {
                [$current, $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

                $relation = $caller['function'];
            }

            return parent::belongsTo($related, $foreignKey, $otherKey, $relation);
        } else {
            return new EmptyRelation();
        }
    }

    /**
     * Extension to original belongsToMany – returns an empty relation if the related module is not available.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany or \App\Extensions\Database\EmptyRelation
     * @author Patrick Reichel
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {
        if ($this->_relationAvailable($related)) {
            return parent::belongsToMany($related, $table, $foreignKey, $otherKey, $relation);
        } else {
            return new EmptyRelation();
        }
    }

    /**
     * Basefunction to define tabs with associated panels (relation or view) for the models edit page
     * E.g. Add relation panel 'modems' on the right side of the contract edit page - see ContractController::view_has_many()
     * Note: Use Controller::editTabs() to define tabs refering to new pages
     *
     * @return array
     */
    public function view_has_many()
    {
        return [];
    }

    /**
     * Basefunction for returning all objects that a model can have a one-to-one relation to
     * Place this function in the model where the edit/create view shall show all related objects
     *
     * @author Patrick Reichel
     *
     * @return an array with the appropriate hasOne()-functions of the model
     */
    public function view_has_one()
    {
        return [];
    }

    /**
     * Check if VoIP is enabled.
     *
     * TODO: - move to Contract/ContractController or use directly,
     *         ore use fucntion directly instead of helpers variable
     *
     * @author Patrick Reichel
     *
     * @return true if one of the VoIP modules is enabled (currently only ProvVoipEnvia), else false
     */
    public function external_voip_enabled()
    {
        $voip_modules = [
            'ProvVoipEnvia',
        ];

        foreach ($voip_modules as $module) {
            if (\Module::collections()->has($module)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if billing is enabled.
     *
     * TODO: - currently this is a dummy (= we don't have a billing module yet!!)
     *       - move to Contract/ContractController or use directly,
     *         ore use fucntion directly instead of helpers variable
     *
     * @author Patrick Reichel
     *
     * @return true if one of the billing modules is enabled, else false
     */
    public function billing_enabled()
    {
        $billing_modules = [
            'BillingBase',
        ];

        foreach ($billing_modules as $module) {
            if (\Module::collections()->has($module)) {
                return true;
            }
        }

        return false;
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
    public static function getPossibleEnumValues($name, $with_empty_option = false)
    {
        // create an instance of the model to be able to get the table name
        $instance = new static;

        // get metadata for the given column and extract enum options
        $type = DB::select(DB::raw('SHOW COLUMNS FROM '.$instance->getTable().' WHERE Field = "'.$name.'"'))[0]->Type;

        // create array with enum values (all values in brackets after “enum”)
        preg_match('/^enum\((.*)\)$/', $type, $matches);

        $enum_values = [];

        // add an empty option if wanted
        if ($with_empty_option) {
            $enum_values[0] = '';
        }

        // add options extracted from database
        foreach (explode(',', $matches[1]) as $value) {
            $v = trim($value, "'");
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
    protected function _getFulltextIndexColumns($table)
    {
        $cols = [];
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
     * Attention: The array is cached in the session - so if modules are enabled/disabled
     *	you have to logout & login to rebuild the array again
     *
     * @return array of all models except base models
     * @author Patrick Reichel,
     *         Torsten Schmidt: add modules path
     */
    public static function get_models()
    {
        if (Session::has('models')) {
            return Session::get('models');
        }

        // models to be excluded from search
        $exclude = [
            'AddressFunctionsTrait',
            'Ability',
            'BaseModel',
            'helpers',
            'BillingLogger',
            'BillingAnalysis',
            'TRCClass',	// static data; not for standalone use
            'CarrierCode', // cron updated data; not for standalone use
            'EkpCode', // cron updated data; not for standalone use
            'ProvVoipEnviaHelpers',
        ];
        $result = [];

        /*
         * Search all Models in /models Models Path
         */
        $models = glob(app_path().'/*.php');

        foreach ($models as $model) {
            $model = str_replace(app_path().'/', '', $model);
            $model = str_replace('.php', '', $model);
            if (array_search($model, $exclude) === false) {
                $result[$model] = 'App\\'.$model;
            }
        }

        /*
         * Search all Models in /Modules/../Entities Path
         */
        $path = base_path('modules');
        $dirs = [];
        $modules = \Module::enabled();
        foreach ($modules as $module) {
            array_push($dirs, $module->getPath().'/Entities');
        }

        foreach ($dirs as $dir) {
            $models = glob($dir.'/*.php');

            foreach ($models as $model) {
                preg_match("|$path/(.*?)/Entities/|", $model, $module_array);
                $module = $module_array[1];
                $model = preg_replace("|$path/(.*?)/Entities/|", '', $model);
                $model = str_replace('.php', '', $model);
                if (array_search($model, $exclude) === false) {
                    $result[$model] = "Modules\\$module\Entities\\".$model;
                }
            }
        }

        Session::put('models', $result);

        return $result;
    }

    protected function _guess_model_name($s)
    {
        return current(preg_grep('|.*?'.$s.'$|i', $this->get_models()));
    }

    protected function onlyAllowedModels()
    {
        return collect($this->get_models())->reject(function ($class) {
            return Bouncer::cannot('view', $class);
        });
    }

    /**
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

        if ($field && $value) {
            $ret = $field.'='.$value;

            if (\Module::collections()->has('HfcBase')) {
                if (($model[0] == 'Modules\ProvBase\Entities\Modem') && ($field == 'net' || $field == 'cluster')) {
                    $ret = 'tree_id IN(-1';
                    foreach (Modules\HfcReq\Entities\NetElement::where($field, '=', $value)->get() as $tree) {
                        $ret .= ','.$tree->id;
                    }
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
    protected function _doSimpleSearch($_models, $query, $preselect_field = null, $preselect_value = null)
    {
        $preselect = $this->__preselect_search($preselect_field, $preselect_value, $_models);

        /*
         * Model Checking: Prepare $models array: skip Models without a valid SQL table
         */
        $models = [];
        foreach ($_models as $model) {
            if (! class_exists($model)) {
                continue;
            }

            $tmp = new $model;

            if (! property_exists($tmp, 'table')) {
                continue;
            }

            if (! Schema::hasTable($tmp->table)) {
                continue;
            }

            array_push($models, $model);
        }

        /*
         * Perform the search
         */
        $result = [];
        foreach ($models as $model) {
            // get the database table used for given model
            $tmp = new $model;
            $table = $tmp->getTable();
            $cols = $model::getTableColumns($table);

            $tmp_result = $model::whereRaw("($preselect) AND CONCAT_WS('|', ".$cols.') LIKE ?', [$query]);
            if ($tmp_result) {
                array_push($result, $tmp_result);
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
    public static function getTableColumns($table)
    {
        $tmp_res = [];
        $cols = DB::select(DB::raw('SHOW COLUMNS FROM '.$table));
        foreach ($cols as $col) {
            array_push($tmp_res, $table.'.'.$col->Field);
        }

        $fields = implode(',', $tmp_res);

        return $fields;
    }

    /**
     * Switch to decide with search algo shall be used
     * Here we can add other conditions (e.g. to force mode simple on mac search or %truncation)
     */
    protected function _chooseFulltextSearchAlgo($mode, $query)
    {

        // search query is left truncated => simple search
        if ((Str::startsWith($query, '%')) || (Str::startsWith($query, '*'))) {
            $mode = 'simple';
        }

        // query contains . or : => IP or MAC => simple search
        if ((Str::contains($query, ':')) || (Str::contains($query, '.'))) {
            $mode = 'simple';
        }

        return $mode;
    }

    /**
     * Get results for a fulltext search
     *
     * @return search result array of whereRaw() results, this means array of Illuminate\Database\Quer\Builder objects
     *
     * @author Patrick Reichel
     */
    public function getFulltextSearchResults($scope, $mode, $query, $preselect_field = null, $preselect_value = null)
    {

        // some searches cannot be performed against fulltext index
        $mode = $this->_chooseFulltextSearchAlgo($mode, $query);

        if ($mode == 'simple') {

            // replace wildcard chars
            $query = str_replace('*', '%', $query);
            // wrap with wildcards (if not given) => necessary because of the concatenation of all table rows
            if (! Str::startsWith($query, '%')) {
                $query = '%'.$query;
            }
            if (! Str::endsWith($query, '%')) {
                $query = $query.'%';
            }

            if ($scope == 'all') {
                $models = $this->onlyAllowedModels();

                $preselect_field = $preselect_value = null;
            } else {
                $models = [get_class($this)];
            }

            $result = $this->_doSimpleSearch($models, $query, $preselect_field, $preselect_value);
        } elseif (Str::startsWith($mode, 'index_')) {
            if ($scope == 'all') {
                echo 'Implement searching over all database tables';
            } else {
                $indexed_cols = $this->_getFulltextIndexColumns($this->getTable());

                // for a description of search modes check https://mariadb.com/kb/en/mariadb/fulltext-index-overview
                if ('index_natural' == $mode) {
                    $mode = 'IN NATURAL MODE';
                } elseif ('index_boolean' == $mode) {
                    $mode = 'IN BOOLEAN MODE';
                } else {
                    $mode = 'IN BOOLEAN MODE';
                }

                // search is against the fulltext index
                $result = [$this->whereRaw('MATCH('.$indexed_cols.') AGAINST(? '.$mode.')', [$query])];
            }
        } else {
            $result = null;
        }

        /* echo "$query at $scope in mode $mode<br><pre>"; */
        /* dd($result); */
        return $result;
    }

    /**
     * Generic function to build a list with key of id
     * @param 	array 			$array 	 		list of Models/Objects
     * @param 	String/Array 	$column 		sql column name(s) that contain(s) the description of the entry
     * @param 	bool 			$empty_option 	true it first entry shall be empty
     * @return  array 			$ret 			list
     */
    public function html_list($array, $columns, $empty_option = false, $separator = '--')
    {
        $ret = $empty_option ? [0 => null] : [];

        if (is_string($columns)) {
            foreach ($array as $a) {
                $ret[$a->id] = $a->{$columns};
            }

            return $ret;
        }

        // column is array
        foreach ($array as $a) {
            foreach ($columns as $key => $c) {
                $desc[$key] = $a->{$c};
            }

            $ret[$a->id] = implode($separator, $desc);
        }

        return $ret;
    }

    /**
     * Generic function to build a list with key of id and usage count.
     *
     * @param array         $array          list of Models/Objects
     * @param String/Array  $column         sql column name(s) that contain(s) the description of the entry
     * @param bool          $empty_option   true it first entry shall be empty
     * @param string        $colname        the column to count
     * @param string        $count_at       the database table to count at
     * @return array        $ret            list
     *
     * @author Patrick Reichel
     */
    public function html_list_with_count($array, $columns, $empty_option = false, $separator = '--', $colname = '', $count_at = '')
    {
        $tmp = $this->html_list($array, $columns, $empty_option, $separator);
        if (! $colname || ! $count_at) {
            return $tmp;
        }

        $counts_raw = \DB::select("SELECT $colname AS value, COUNT($colname) AS count FROM $count_at WHERE deleted_at IS NULL GROUP BY $colname");
        $counts = [];
        foreach ($counts_raw as $entry) {
            $counts[$entry->value] = $entry->count;
        }

        $ret = [];
        foreach ($tmp as $id => $value) {
            $ret[$id] = array_key_exists($id, $counts) ? $value.' ('.$counts[$id].')' : $value.' (0)';
        }

        return $ret;
    }

    // Placeholder
    public static function view_headline()
    {
        return 'Need to be Set !';
    }

    // Placeholder
    public static function view_icon()
    {
        return '<i class="fa fa-circle-thin"></i>';
    }

    // Placeholder
    public static function view_no_entries()
    {
        return 'No entries found!';
    }

    // Placeholder
    public function view_index_label()
    {
        return 'Need to be Set !';
    }

    /**
     *	Returns a array of all children objects of $this object
     *  Note: - Must be called from object context
     *        - this requires straight forward names of tables an
     *          forgein key, like modem and modem_id.
     *
     *  NOTE: we define exceptions in an array where recursive deletion is disabled
     *  NOTE: we have to distinct between 1:n and n:m relations
     *
     *	@author Torsten Schmidt, Patrick Reichel
     *
     *	@return array of all children objects
     */
    public function get_all_children()
    {
        $relations = [
            '1:n' => [],
            'n:m' => [],
        ];
        // exceptions – the children (=their database ID fields) that never should be deleted
        $exceptions = [
            'company_id',
            'configfile_id',
            'costcenter_id',
            'country_id',	// not used yet
            //'mibfile_id',
            //'oid_id',
            'product_id',
            'qos_id',
            'salesman_id',
            'sepaaccount_id',
            'voip_id',
        ];

        // this is the variable that holds table names in $table returned by DB::select('SHOW TABLES')
        // named dynamically containing the database name
        $tables_var_name = 'Tables_in_'.ENV('DB_DATABASE');

        // Lookup all SQL Tables
        foreach (DB::select('SHOW TABLES') as $table) {
            // Lookup SQL Fields for current $table
            foreach (Schema::getColumnListing($table->$tables_var_name) as $column) {
                // check if $column is actual table name object added by '_id'
                if ($column == $this->table.'_id') {
                    if (in_array($column, $exceptions)) {
                        continue;
                    }

                    // get all objects with $column
                    $query = 'SELECT * FROM '.$table->$tables_var_name.' WHERE '.$column.'='.$this->id;
                    foreach (DB::select($query) as $child) {
                        $class_child_name = $this->_guess_model_name($table->$tables_var_name);
                        // check if we got a model name
                        if ($class_child_name) {
                            // yes! 1:n relation
                            $class = new $class_child_name;
                            $rel = $class->find($child->id);
                            if (! is_null($rel)) {
                                array_push($relations['1:n'], $rel);
                            }
                        } else {
                            // seems to be a n:m relation
                            $parts = explode('_', $table->$tables_var_name);
                            foreach ($parts as $part) {
                                $class_child_name = $this->_guess_model_name($part);

                                // one of the models in pivot tables is the current model – skip
                                if ($class_child_name == get_class($this)) {
                                    continue;
                                }

                                // add other model instances to relation array if existing
                                $class = new $class_child_name;
                                $id_col = $part.'_id';
                                $rel = $class->find($child->{$id_col});
                                if (! is_null($rel)) {
                                    array_push($relations['n:m'], $rel);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $relations;
    }

    /**
     * Local Helper to differ between soft- and force-deletes
     * @return type mixed
     */
    protected function _delete()
    {
        if ($this->force_delete) {
            return parent::performDeleteOnModel();
        }

        return parent::delete();
    }

    /**
     * Recursive delete of all children objects
     *
     * @author Torsten Schmidt, Patrick Reichel
     *
     * @return bool
     *
     * @todo return state on success, should also take care of deleted children
     */
    public function delete()
    {
        // check from where the deletion request has been triggered and set the correct var to show information
        $prev = explode('/', explode('?', \URL::previous())[0]);
        $target = preg_match('/[a-z]/i', end($prev)) ? 'above_index_list' : 'above_relations';

        if ($this->delete_children) {
            $children = $this->get_all_children();
            // find and delete all children

            // deletion of 1:n related children is straight forward
            foreach ($children['1:n'] as $child) {

                // if one direct or indirect child cannot be deleted:
                // do not delete anything
                if (! $child->delete()) {
                    $msg = 'Cannot delete '.$this->get_model_name()." $this->id: ".$child->get_model_name()." $child->id cannot be deleted";
                    Session::push("tmp_error_$target", $msg);

                    return false;
                }
            }

            // in n:m relations we have to detach instead of deleting if
            // child is related to others, too
            // this should be handled in class methods because BaseModel cannot know the possible problems
            foreach ($children['n:m'] as $child) {
                $delete_method = 'deleteNtoM'.$child->get_model_name();

                if (! method_exists($this, $delete_method)) {
                    // Keep Pivot Entries and children if method is not specified and just log a warning message
                    \Log::warning($this->get_model_name().' - N:M pivot entry deletion handling not implemented for '.$child->get_model_name());
                } elseif (! $this->{$delete_method}($child)) {
                    $msg = 'Cannot delete '.$this->get_model_name()." $this->id: n:m relation with ".$child->get_model_name()." $child->id. cannot be deleted";
                    Session::push("tmp_error_$target", $msg);

                    return false;
                }
            }
        }

        // always return this value (also in your derived classes!)
        $deleted = $this->_delete();
        if ($deleted) {
            $msg = trans('messages.base.delete.success', ['model' => $this->get_model_name(), 'id' => $this->id]);
            Session::push("tmp_success_$target", $msg);
        } else {
            $msg = trans('messages.base.delete.fail', ['model' => $this->get_model_name(), 'id' => $this->id]);
            Session::push("tmp_error_$target", $msg);
        }

        return $deleted;
    }

    public static function destroy($ids)
    {
        $instance = new static;

        foreach ($ids as $id => $help) {
            $instance->findOrFail($id)->delete();
        }
    }

    /**
     * Placeholder for undeletable Elements of index tree view
     */
    public static function undeletables()
    {
        return [0 => 0];
    }

    /**
     * Checks if model is valid in specific timespan
     * (used for Billing or to calculate income for dashboard)
     *
     * Note: if param start_end_ts is not set the model must have a get_start_time- & get_end_time-Function defined
     *
     * @param 	timespan 		String		Yearly|Quarterly|Monthly|Now => Enum of Product->billing_cycle
     * @param 	time 			Integer 	Seconds since 1970 - check for timespan of specific point of time
     * @param 	start_end_ts 	Array 		UTC Timestamps [start, end] (in sec)
     *
     * @return 	bool  			true, if model had valid dates during last month / year or is actually valid (now)
     *
     * @author Nino Ryschawy
     */
    public function check_validity($timespan = 'monthly', $time = null, $start_end_ts = [])
    {
        $start = $start_end_ts ? $start_end_ts[0] : $this->get_start_time();
        $end = $start_end_ts ? $start_end_ts[1] : $this->get_end_time();

        // default - billing settlementruns/charges are calculated for last month
        $time = $time ?: strtotime('midnight first day of last month');

        switch (strtolower($timespan)) {
            case 'once':
                // E.g. one time or splitted payments of items - no open end! With end date: only on months from start to end
                return $end ? $start < strtotime('midnight first day of next month', $time) && $end >= $time : date('Y-m', $start) == date('Y-m', $time);

            case 'monthly':
                // has valid dates in last month - open end possible
                return $start < strtotime('midnight first day of next month', $time) && (! $end || $end >= $time);

            case 'quarterly':
                // TODO: implement
                break;

            case 'yearly':
                return $start < strtotime('midnight first day of january next year', $time) && (! $end || $end >= strtotime('midnight first day of January this year', $time));

            case 'now':
                $time = strtotime('today');

                return $start <= $time && (! $end || $end >= $time);

            default:
                \Log::error('Bad timespan param used in function '.__FUNCTION__);
                break;
        }

        return true;
    }
}

/**
 * Base Observer Class - Logging of all User Interaction
 *
 * @author Nino Ryschawy
 */
class BaseObserver
{
    public function created($model)
    {
        if (! $model->observer_enabled) {
            return;
        }

        $this->add_log_entry($model, __FUNCTION__);

        // TODO: analyze impacts of different return values
        //		without return (= return null): all is running, but multiple log entries are created
        //		return false: only one log entry per change, but created of e.g. PhonenumberObserver is never called (checked this using dd())
        //		return true: one log entry, other observers are called
        // that are our observations so far – we definitely should check if there are other side effects!!
        // possible hint: the BaseObserver is registered before the model's observers
        return true;
    }

    public function updated($model)
    {
        if (! $model->observer_enabled) {
            return;
        }

        $this->add_log_entry($model, __FUNCTION__);

        // TODO: analyze impacts of different return values
        //		⇒ see comment at created
        return true;
    }

    public function deleted($model)
    {
        if (! $model->observer_enabled) {
            return;
        }

        $this->add_log_entry($model, __FUNCTION__);

        // TODO: analyze impacts of different return values
        //		⇒ see comment at created
        return true;
    }

    /**
     * Create Log Entry on fired Event
     */
    private function add_log_entry($model, $action)
    {
        $user = Auth::user();

        $model_name = $model->get_model_name();

        $text = '';

        $attributes = $model->getDirty();

        if (array_key_exists('remember_token', $attributes)) {
            unset($attributes['remember_token']);
            unset($attributes['updated_at']);
        }

        if (empty($attributes)) {
            return;
        }

        // if really updated (and not updated by model->save() in observer->created() like in contract)
        if (($action == 'updated') && (! $model->wasRecentlyCreated)) {

            // skip following attributes - TODO:
            $ignore = [
                'updated_at',
            ];

            // hide the changed data (but log the fact of change)
            $hide = [
                'password',
            ];

            // get changed attributes
            $arr = [];

            foreach ($model['attributes'] as $key => $value) {
                if (in_array($key, $ignore)) {
                    continue;
                }

                $original = $model['original'][$key];
                if ($original != $value) {
                    if (in_array($key, $hide)) {
                        $arr[] = $key;
                    } elseif (array_key_exists('deleted_at', $attributes) && $attributes['deleted_at'] == null) {
                        $arr[] = $key.': '.$original.'-> restored';
                        $action = 'restored';
                    } else {
                        $arr[] = $key.': '.$original.'->'.$value;
                    }
                }
            }
            $text = implode(', ', $arr);
        }

        $data = [
            'user_id' 	=> $user ? $user->id : 0,
            'username' 	=> $user ? $user->first_name.' '.$user->last_name : 'cronjob',
            'method' 	=> $action,
            'model' 	=> $model_name,
            'model_id'  => $model->id,
            'text' 		=> $text,
        ];

        GuiLog::log_changes($data);
    }
}

/**
 * Systemd Observer Class - Handles changes on Model Gateways - restarts system services
 *
 * TODO:
 * place it somewhere else ...
 * Calling this Observer is practically very bad in case there are more services inserted - then all services will restart even
 *		if Config didn't change - therefore a distinction is necessary - or more Observers,
 * another Suggestion:
 * place the restart file creation in the appropriate observer itself
 * only place a static function restart_dhcpd here that creates the file
 */
class SystemdObserver
{
    // insert all services that need to be restarted after a model changed there configuration in that array
    private $services = ['dhcpd'];

    public function created($model)
    {
        \Log::debug('systemd: observer called from create context');

        if (! is_dir(storage_path('systemd'))) {
            mkdir(storage_path('systemd'));
        }

        foreach ($this->services as $service) {
            touch(storage_path('systemd/'.$service));
        }
    }

    public function updated($model)
    {
        if (! $model->observer_enabled) {
            return;
        }

        // Exception - Dont restart dhcp server for modems where no relevant changes where made
        $model_name = new \ReflectionClass(get_class($model));
        $model_name = $model_name->getShortName();

        if ($model_name == 'Modem' && ! $model->needs_restart()) {
            return;
        }

        \Log::debug('systemd: observer called from update context', [$model_name, $model->id]);

        if (! is_dir(storage_path('systemd'))) {
            mkdir(storage_path('systemd'));
        }

        foreach ($this->services as $service) {
            touch(storage_path('systemd/'.$service));
        }
    }

    public function deleted($model)
    {
        \Log::debug('systemd: observer called from delete context');

        if (! is_dir(storage_path('systemd'))) {
            mkdir(storage_path('systemd'));
        }

        foreach ($this->services as $service) {
            touch(storage_path('systemd/'.$service));
        }
    }
}
