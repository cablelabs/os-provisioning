<?php

namespace Tests;

/**
 * Base class to derive lifecycle tests for a model from
 *
 * This will reuse the static get_fake_data method in your model's seeder class (e.g. for create).
 * Assure that your seeder is up do date and running!
 *
 * @author Patrick Reichel
 */
/* class BaseLifecycleTest extends BaseTest { */
class BaseLifecycleTest extends TestCase
{
    // flag to show debug output from test runs
    /* protected $debug = True; */
    protected $debug = false;

    // flag to print currently executed test method to stdout
    protected $echo_running_test = true;

    // flag to delete created entries from database after testing
    // keeping them can be useful for debugging
    protected $clean_database_after_testing = true;

    // list of test method names to be run – can be used in development to work on single tests only
    // attention: the tests are executed in order of definition – not in order of this array
    protected $tests_to_be_run = [
        'testCreateTwiceUsingTheSameData',
        'testCreateWithFakeData',
        'testDeleteFromIndexView',
        'testEmptyCreate',
        'testIndexViewVisible',
        'testDatatableDataReturned',
        'testUpdate',
    ];

    // this blacklist defines tests that should not be run (overwrites $tests_to_be_run)
    // use this in your derived test classes to cut out single tests without the need
    // to keep the whitelist up-to-date
    protected $tests_to_be_excluded = [];

    // define how often the create, update and delete tests should be run
    // should be at least two to be able to test bulk delete, too
    protected $testrun_count = 2;

    // most models are created from another models context – if so set this in your derived class
    protected $create_from_model_context = null;

    // flag to indicate if creating without data should fail
    protected $creating_empty_should_fail = true;

    // flag to indicate if creating the same data entry twice should fail (usually the case if there are unique fields
    protected $creating_twice_should_fail = true;

    // fields to be updated with random data
    protected $update_fields = [];

    // array holding the edit form structure
    protected static $edit_field_structure = [];

    // container to collect all created entities
    protected static $created_entity_ids = [];

    // the following helpers define the stuff to use
    // we try to guess this from child class name (you can set it there explicitely if needed)
    // the derived classes are expected to be in following format: Modules\_modulename_\Tests\_modelname_LifeceycleTest
    // e.g.Modules\ProvBase\Tests\ContractLifecycleTest
    protected $module_path = null;
    protected $model_name = null;
    protected $model_path = null;
    protected $controller_path = null;
    protected $database_table = null;
    protected $seeder = null;

    /**
     * Constructor
     *
     * @author Patrick Reichel
     */
    public function __construct()
    {
        $this->class_name = get_called_class();

        if ($this->class_name == 'BaseLifecycleTest') {
            // this is a base class doing nothing – derive your own lifecycle tests classes
            exit(0);
        }

        $this->_set_helper_vars();

        return parent::__construct();
    }

    /**
     * Try to guess (from child class name) all helper vars that are set to null.
     * That means you can simply set these vars to correct values if this method makes problems.
     *
     * @author Patrick Reichel
     */
    protected function _set_helper_vars()
    {
        $parts = explode('\\Tests\\', $this->class_name);
        if (is_null($this->module_path)) {
            $this->module_path = $parts[0];
        }
        $class = $parts[1];

        // guess the model name
        if (is_null($this->model_name)) {
            $this->model_name = str_replace('LifecycleTest', '', $class);
        }

        // guess the controller path
        if (is_null($this->controller_path)) {
            $this->controller_path = '\\'.$this->module_path.'\\Http\\Controllers\\'.$this->model_name.'Controller';
        }

        // guess the database table
        if (is_null($this->database_table)) {
            $this->database_table = strtolower($this->model_name);
        }

        // guess the model name
        if (is_null($this->seeder)) {
            $this->seeder = '\\'.$this->module_path.'\\Database\\Seeders\\'.$this->model_name.'TableSeeder';
        }

        // guess the model path
        if (is_null($this->model_path)) {
            $this->model_path = '\\'.$this->module_path.'\\Entities\\'.$this->model_name;
        }
    }

    /**
     * Creates a Laravel application used for testing
     *
     * @author Patrick Reichel
     */
    public function createApplication()
    {

        // making $app global (and reusing it in every lifecycle test) prevents memory exhaustion
        // (laravel seems not to clean up $app properly – read https://stackoverflow.com/questions/39096658/laravel-testing-increasing-memory-use for details)
        // attention: can have ugly side effects if you change $app in your tests (no code isolation)
        // if needed: clean up in $this->tearDown()
        global $app;
        if (is_null($app)) {
            $app = parent::createApplication();
        }
        $this->_get_user();

        // get edit form structure for current model
        // has to be done here (and not in constructor as we need some laravel stuff)
        $this->_get_form_structure();

        return $app;
    }

    /**
     * Gets a user having the permissions needed for tests.
     *
     * @TODO: Switch from hardcoded user to dynamic getting from database or create a new one!
     *
     * @author Patrick Reichel
     */
    protected function _get_user()
    {

        // TODO: do not hard code any user class, instead fetch a user dynamically
        //       or add it only for testing (see Laravel factory stuff)
        $this->user = \App\User::findOrFail(1);
    }

    /**
     * Tries to extract the structure of the <form> in edit view from controller.
     * Should be called within each test visiting and filling create/edit form.
     *
     * If this fails: overwrite (hardcode?) in your derived class.
     *
     * @author Patrick Reichel
     *
     * @return array containing form field names as keys and testing methods as values
     */
    protected function _get_form_structure($model = null)
    {

        // check if data exists – nothing to do
        if (array_key_exists($this->class_name, self::$edit_field_structure)) {
            return;
        }

        /* require_once */
        $controller = new $this->controller_path();

        $form_raw = $controller->view_form_fields($model);

        $structure = [];

        foreach ($form_raw as $form_raw_field) {

            // hint: the operator “@” forces PHP to ignore error messages
            // check if field is hidden – in which case we don't want to fill it
            if (@$form_raw_field['hidden']) {
                continue;
            }

            // if there is no name set we cannot fill the field
            if (! $name = @$form_raw_field['name']) {
                continue;
            }

            // get the HTML type
            if (! $type = @$form_raw_field['form_type']) {
                continue;
            }

            // get possible (select) or default (others) values
            if (! @$form_raw_field['value']) {
                $value = null;
            } else {
                if (in_array($type, ['select', 'radio'])) {
                    $value = array_keys($form_raw_field['value']);
                } else {
                    $value = $form_raw_field['value'];
                }
            }

            self::$edit_field_structure[$this->class_name][$name]['type'] = $type;
            self::$edit_field_structure[$this->class_name][$name]['values'] = $value;
        }
    }

    /**
     * Get fake data for one instance from seeder.
     * This method guaranties that no unique fields are filled with data existing in our database.
     * The only exception is NULL – we assume that this is allowed to be used multiple times.
     *
     * If a model_id is given data can be the same for this ID (e.g. on updating a model)
     *
     *
     * @author Patrick Reichel
     */
    protected function _get_fake_data($related_to, $model_id = -1)
    {

        // get the rules defined in model and extract unique fields
        $rules = call_user_func([$this->module_path.'\\Entities\\'.$this->model_name, 'rules']);
        $unique_fields = [];

        // check for unique fields
        foreach ($rules as $field => $rule) {
            if (strpos($rule, 'unique') !== false) {
                array_push($unique_fields, $field);
            }
        }

        // get data until all critical fields are unique
        $data_is_unique = false;
        $tries = 0;
        while (! $data_is_unique) {
            $tries++;
            // throw exception to avoid endless loop
            if ($tries > 100) {
                throw new \Exception('Unable to create unique data.');
            }

            $data_is_unique = true;
            $data = call_user_func($this->seeder.'::get_fake_data', 'test', $related_to);

            // check if data that has to be unique already exists in our database
            foreach ($unique_fields as $unique_field) {

                // if seeder returns null: skip unique test – we assume that NULL can be used multiple times
                if (is_null($data[$unique_field])) {
                    continue;
                }

                $ids = \DB::table($this->database_table)
                    ->select('id')
                    ->where($unique_field, '=', $data[$unique_field])
                    ->where('id', '!=', $model_id)
                    ->get()
                    ->toArray();

                // if there is duplicate data for an unique field: get new seeder data
                if ($ids) {
                    $data_is_unique = false;
                    break;
                }
            }
        }

        return $data;
    }

    /**
     * Wrapper to fill a create form.
     *
     * @author Patrick Reichel
     */
    protected function _fill_create_form($data)
    {
        $this->_fill_form($data, 'create');
    }

    /**
     * Wrapper to fill a create form.
     *
     * @author Patrick Reichel
     */
    protected function _fill_edit_form($data)
    {
        $this->_fill_form($data, 'update');
    }

    /**
     * Fills a form with given data depending on form structure.
     *
     * @author Patrick Reichel
     */
    protected function _fill_form($data, $method)
    {
        if ($this->debug) {
            echo "Filling form\n";
        }

        foreach (self::$edit_field_structure[$this->class_name] as $field_name => $structure) {

            // on update: only update defined fields
            if ($method == 'update') {
                if (! in_array($field_name, $this->update_fields)) {
                    continue;
                }
            }

            // if the faker gave no data for the name => this field seems not to be required
            if (! $faked_data = @$data[$field_name]) {
                continue;
            }

            // convert DateTime objects to string
            if ($faked_data instanceof DateTime) {
                $_ = (array) $faked_data;
                $faked_data = explode(' ', $_['date'])[0];
            }
            // fill depending on field type
            switch ($structure['type']) {

            case 'select':
            case 'radio':
                // use faker data only if available as option; choose random value out of available randomly
                if (! in_array($faked_data, $structure['values'])) {
                    $faked_data = $structure['values'][array_rand($structure['values'])];
                }

                if ($this->debug) {
                    echo 'Selecting “'.$faked_data."” in $field_name\n";
                }
                $this->select($faked_data, $field_name);
                break;

            case 'checkbox':
                if (boolval($faked_data)) {
                    if ($this->debug) {
                        echo "Checking $field_name\n";
                    }
                    $this->check($field_name);
                } else {
                    if ($this->debug) {
                        echo "Unchecking $field_name\n";
                    }
                    $this->uncheck($field_name);
                }
                break;

            default:
                // simply put faked data into the field (should be text or textarea)
                if ($this->debug) {
                    echo 'Typing “'.$faked_data."” in $field_name\n";
                }
                $this->type($faked_data, $field_name);
                break;

            }
        }
    }

    /**
     * Checks if a test method shall be executed
     *
     * @author Patrick Reichel
     */
    protected function _test_shall_be_run($test_method)
    {
        if ($this->echo_running_test) {
            echo "\n".$this->class_name.'->'.$test_method.'()';
        }

        // check against the whitelist
        if (! in_array($test_method, $this->tests_to_be_run)) {
            echo "\n	WARNING: Skipping ".$this->class_name.'->'.$test_method.'() (not found in tests_to_be_run)';

            return false;
        }

        // check against the blacklist
        if (in_array($test_method, $this->tests_to_be_excluded)) {
            echo "\n	WARNING: Skipping ".$this->class_name.'->'.$test_method.'() (found in tests_to_be_excluded)';

            return false;
        }

        // all checks passed: run the test
        return true;
    }

    /**
     * Helper to add ID of a currently created entity to the static array
     *
     * @param $uri The URI to be used; in most cases you will us $this->currentUri
     *
     * @return $id if one has been created else null
     *
     * @author Patrick Reichel
     */
    protected function _addToCreatedEntityIdsArray($uri)
    {
        $_ = explode('/', $uri);
        // if we end up in edit view: generated successfully (otherwise we keep staying in create view)
        if ((array_pop($_) == 'edit')) {
            $id = array_pop($_);
            if (is_numeric($id) && ($id != '0')) {
                array_push(self::$created_entity_ids, $id);

                return $id;
            }
        }
    }

    /**
     * There are models that can only be created from another model.
     * This method gets a parent model to create on.
     *
     * @author Patrick Reichel
     */
    protected function _get_create_context()
    {
        $ret = [];

        if (is_null($this->create_from_model_context)) {
            $ret['instance'] = null;
            $ret['params'] = [];
        } else {
            $model = $this->create_from_model_context;
            $instance = $model::all()->random(1);
            $ret['instance'] = $instance;
            $ret['params'] = [$instance->table.'_id' => $instance->id];
        }

        return $ret;
    }

    /**
     * Try to create without data – we expect this to fail.
     *
     * @author Patrick Reichel
     */
    public function testEmptyCreate()
    {
        if (! $this->_test_shall_be_run(__FUNCTION__)) {
            return;
        }

        if ($this->creating_empty_should_fail) {
            $msg_expected = 'please correct the following errors';
        } else {
            $msg_expected = 'Created!';
        }

        $context = $this->_get_create_context();
        $this->actingAs($this->user)
            ->visit(route("$this->model_name.create", $context['params']))
            ->press('Save')
            ->see($msg_expected);

        if (! $this->creating_empty_should_fail) {

            // add to created ids array (check if there is a created entity is performed in helper method)
            $id = $this->_addToCreatedEntityIdsArray($this->currentUri);

            if (! is_null($id)) {
                // check for correct entry in guilog
                $this->seeInDatabase('guilog', ['method' => 'created', 'model' => $this->model_name, 'model_id' => $id]);
            }
        }
    }

    /**
     * Check if index view is visible.
     *
     * @author Patrick Reichel
     */
    public function testIndexViewVisible()
    {
        if (! $this->_test_shall_be_run(__FUNCTION__)) {
            return;
        }

        // get the raw headlines for datatables table from model
        $model = new $this->model_path();
        $index_header_raw = $model->view_index_label()['index_header'];

        // get the (english) translation array
        $index_header_translations_en = include 'resources/lang/en/dt_header.php';

        // create the header as should be shown on index view
        $index_header = [];
        foreach ($index_header_raw as $raw) {
            array_push($index_header, $index_header_translations_en[$raw]);
        }

        // visit index page and check for some basic output
        $this->actingAs($this->user)
            ->visit(route("$this->model_name.index"))
            ->see('NMS Prime')
            ->see('Next Generation NMS')
            ->see($this->model_name)
            ->see('/admin/'.$this->model_name.'/0');	// part of the delete form

        // check if all index table headers are visible
        foreach ($index_header as $header) {
            $this->see($header);
        }
    }

    /**
     * Try to create.
     *
     * @author Patrick Reichel
     */
    public function testCreateWithFakeData()
    {
        if (! $this->_test_shall_be_run(__FUNCTION__)) {
            return;
        }

        for ($i = 0; $i < $this->testrun_count; $i++) {
            $context = $this->_get_create_context();
            $data = $this->_get_fake_data($context['instance']);

            $this->actingAs($this->user)
                ->visit(route("$this->model_name.create", $context['params']));

            $this->_fill_create_form($data);

            $this->press('_save')
                ->see('Created!');

            // add to created ids array (check if there is a created entity is performed in helper method)
            $id = $this->_addToCreatedEntityIdsArray($this->currentUri);

            if (! is_null($id)) {
                // check for correct entry in guilog
                $this->seeInDatabase('guilog', ['method' => 'created', 'model' => $this->model_name, 'model_id' => $id]);
            }
        }
    }

    /**
     * Try to create the same data twice.
     *
     * @author Patrick Reichel
     */
    public function testCreateTwiceUsingTheSameData()
    {
        if (! $this->_test_shall_be_run(__FUNCTION__)) {
            return;
        }

        $context = $this->_get_create_context();
        $data = $this->_get_fake_data($context['instance']);

        // this is the first create which should run
        $this->actingAs($this->user)
            ->visit(route("$this->model_name.create", $context['params']));
        $this->_fill_create_form($data);
        $this->press('_save')
            ->see('Created!');

        // add to created ids array (check if there is a created entity is performed in helper method)
        $this->_addToCreatedEntityIdsArray($this->currentUri);

        // this is the second create (using the same data) which usually should fail
        if ($this->creating_twice_should_fail) {
            $msg_expected = 'please correct the following errors';
        } else {
            $msg_expected = 'Created!';
        }
        $this->actingAs($this->user)
            ->visit(route("$this->model_name.create"));
        $this->_fill_create_form($data);
        $this->press('_save')
            ->see($msg_expected);

        // add to created ids array (check if there is a created entity is performed in helper method)
        $this->_addToCreatedEntityIdsArray($this->currentUri);
    }

    /**
     * Try to update a database entry.
     *
     * @author Patrick Reichel
     */
    public function testUpdate()
    {
        if (! $this->_test_shall_be_run(__FUNCTION__)) {
            return;
        }

        if (! $this->update_fields) {
            echo "	WARNING: No entries in update_fields – cannot test!\n";

            return;
        }

        foreach (self::$created_entity_ids as $id) {
            if ($this->debug) {
                echo "\nUpdating $this->model_name $id";
            }

            $context = $this->_get_create_context();
            $data = $this->_get_fake_data($context['instance'], $id);

            $this->actingAs($this->user)
                ->visit(route("$this->model_name.edit", $id));

            $this->_fill_edit_form($data);
            $this->press('_save')
                ->see('Updated!');

            // check for correct entry in guilog
            $this->seeInDatabase('guilog', ['method' => 'updated', 'model' => $this->model_name, 'model_id' => $id]);
        }
    }

    /**
     * Check if associated datatable returns data.
     *
     * @author Patrick Reichel
     */
    public function testDatatableDataReturned()
    {
        if (! $this->_test_shall_be_run(__FUNCTION__)) {
            return;
        }

        $this->actingAs($this->user)
            ->get(route("$this->model_name.index").'/datatables');

        // count all not deleted database entries and check if this is returned by JSON
        $model_count = \DB::table($this->database_table)->whereNull('deleted_at')->count();
        $this->seeJson(['recordsTotal' => $model_count]);

        // check if there are links to the recently created model instances
        $ids = self::$created_entity_ids;
        foreach ($ids as $id) {
            $route = route($this->model_name.'.edit', $id);
            // the href are specially prepared in JSON return – some characters are escaped
            $_ = str_replace('/', '\/', $route);
            // the route command returns localhost while JSON has links to 127.0.0.1
            // as I don't know if this everytime is the case I only check beginning with port number
            $_ = explode(':', $_);
            $search = array_pop($_);
            $this->see($search);
        }
    }

    /**
     * Try to delete a database entry.
     * This cannot be executed directly by checking in and submitting the form.
     * The use of datatables causes empty form table in returned HTML – so we have to simulate the process
     * in sending a POST request directly.
     *
     * @author Patrick Reichel
     */
    public function testDeleteFromIndexView()
    {
        if (! $this->_test_shall_be_run(__FUNCTION__)) {
            return;
        }

        // delete only freshly created model entities
        // others could have children that disallow deletion!
        // therefore: prepare an array holding a single ID to be deleted and an array holding all other
        // IDs to be bulk deleted
        $ids_to_delete = [];
        array_push($ids_to_delete, [array_pop(self::$created_entity_ids)]);
        array_push($ids_to_delete, self::$created_entity_ids);

        // clear array (else this data will be used by other models, too)
        self::$created_entity_ids = [];

        foreach ($ids_to_delete as $ids) {

            // first: visit index view to get CSRF token
            $this->actingAs($this->user);
            $this->visit(route("$this->model_name.index"));

            $post_ids = [];
            foreach ($ids as $id) {
                $post_ids[$id] = '1';
                if ($this->debug) {
                    echo "\nDeleting $this->model_name $id";
                }
            }
            // then prepare the data to be send via POST
            // this is necessary because of the datables there is no HTML content to be clicked
            $form_data = [
                '_delete' => '',
                '_token' => Session::token(),
                '_method' => 'DELETE',
                'ids' => $post_ids,
                ];

            // the url to send the POST request to
            $url = '/admin/'.$this->model_name.'/0';

            // and here we go!
            $this->call('POST', $url, $form_data);

            $this->followRedirects();

            foreach ($ids as $id) {
                // we should end up in index view telling us about successful delete
                $this->see("Deleted $this->model_name $id");
                // and of course: deleted at should not longer be NULL in database
                $this->notSeeInDatabase($this->database_table, ['deleted_at' => null, 'id' => $id]);
                // check for correct entry in guilog
                $this->seeInDatabase('guilog', ['method' => 'deleted', 'model' => $this->model_name, 'model_id' => $id]);

                // remove testing data from database
                if ($this->clean_database_after_testing) {
                    \DB::table($this->database_table)->where('id', '=', $id)->delete();
                }
            }
        }
    }

    /**
     * Clean up to free RAM and speedup PHPUnit
     * see http://kriswallsmith.net/post/18029585104/faster-phpunit
     *
     * @author Patrick Reichel
     */
    protected function tearDown()
    {
        $refl = new \ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (! $prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }

        parent::tearDown();
    }
}
