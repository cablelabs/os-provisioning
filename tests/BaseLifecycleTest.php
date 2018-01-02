<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Base class to derive lifecycle tests for a model from
 *
 * This will reuse the static get_fake_data method in your model's seeder class (e.g. for create).
 * Assure that your seeder is up do date and running!
 *
 * @author Patrick Reichel
 */
/* class BaseLifecycleTest extends BaseTest { */
class BaseLifecycleTest extends TestCase {

	// flag to show debug output from test runs
	/* protected $debug = True; */
	protected $debug = False;

	// list of test method names to be run – can be used in development to work on single tests only
	protected $tests_to_be_run = [
		'testCreateTwiceUsingTheSameData',
		'testCreateWithFakeData',
		'testDeleteFromIndexView',
		'testEmptyCreate',
		'testIndexViewVisible',
		'testIndexViewDatatablesDataAvailable',
		'testUpdate',
	];

	// define how often the create, update and delete tests should be run
	protected $testrun_count = 2;

	// most models are created from another models context – if so set this in your derived class
	protected $create_from_model_context = null;

	// flag to indicate if creating without data should fail
	protected $creating_empty_should_fail = True;

	// flag to indicate if creating the same data entry twice should fail (usually the case if there are unique fields
	protected $creating_twice_should_fail = True;

	// fields to be updated with random data
	protected $update_fields = [];

	// array holding the edit form structure
	protected static $edit_field_structure = [];

	// container to collect all created entities
	protected static $created_entity_ids = [];

	// because of the datatables there are not all entries visible in index view
	// so to test deletion we have to choose an ID from the first list
	// this variable defines which field is used for inital sort
	protected $index_view_order_field = 'id';
	protected $index_view_order_order = 'asc';

	// the following helpers define the stuff to use
	// we try to guess this from child class name (you can set it there explicitely if needed)
	// the derived classes are expected to be in following format: Modules\_modulename_\Tests\_modelname_LifeceycleTest
	// e.g.Modules\ProvBase\Tests\ContractLifecycleTest
	protected $module_path = null;
	protected $model_name = null;
	protected $controller = null;
	protected $database_table = null;
	protected $seeder = null;


	/**
	 * Constructor
	 *
	 * @author Patrick Reichel
	 */
	public function __construct() {

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
	protected function _set_helper_vars() {

		$parts = explode("\\Tests\\", $this->class_name);
		if (is_null($this->module_path)) {
			$this->module_path = $parts[0];
		}
		$class = $parts[1];

		// guess the model name
		if (is_null($this->model_name)) {
			$this->model_name = str_replace('LifecycleTest', '', $class);
		}

		// guess the controller name
		if (is_null($this->controller)) {
			$this->controller = "\\".$this->module_path."\\Http\\Controllers\\".$this->model_name."Controller";
		}

		// guess the database table
		if (is_null($this->database_table)) {
			$this->database_table = strtolower($this->model_name);
		}

		// guess the model name
		if (is_null($this->seeder)) {
			$this->seeder = "\\".$this->module_path."\\Database\\Seeders\\".$this->model_name."TableSeeder";
		}

	}


	public function createApplication() {

		$app = parent::createApplication();
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
	protected function _get_user() {

		// TODO: do not hard code any user class, instead fetch a user dynamically
		//       ore add it only for testing (see Laravel factory stuff)
		$this->user = App\Authuser::findOrFail(1);
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
	protected function _get_form_structure($model = null) {

		// check if data exists – nothing to do
		if (array_key_exists($this->class_name, self::$edit_field_structure)) {
			return;
		}

		/* require_once */
		$controller = new $this->controller();

		$form_raw = $controller->view_form_fields($model);

		$structure = array();

		foreach ($form_raw as $form_raw_field) {

			// check if field is hidden – in which case we don't want to fill it
			if (@$form_raw_field['hidden']) {
				continue;
			}

			// if there is no name set we cannot fill the field
			if (!$name = @$form_raw_field['name']) {
				continue;
			}

			// get the HTML type
			if (!$type = @$form_raw_field['form_type']) {
				continue;
			}

			// get possible (select) or default (others) values
			if (!@$form_raw_field['value']) {
				$value = null;
			}
			else {
				if (in_array($type, ['select', 'radio'])) {
					$value = array_keys($form_raw_field['value']);
				}
				else {
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
	 * If a model_id is given data can be the same for this ID (e.g. on updating a model)
	 *
	 * @author Patrick Reichel
	 */
	protected function _get_fake_data($related_to, $model_id=-1) {

		// get the rules defined in model and extract unique fields
		$rules = call_user_func([$this->module_path."\\Entities\\".$this->model_name, 'rules']);
		$unique_fields = array();

		// check for unique fields
		foreach ($rules as $field => $rule) {
			if (strpos($rule, 'unique') !== false) {
				array_push($unique_fields, $field);
			}
		}

		// get data until all critical fields are unique
		$data_is_unique = False;
		while (!$data_is_unique) {
			$data_is_unique = True;
			$data = call_user_func($this->seeder."::get_fake_data", 'test', $related_to);

			// check if data that has to be unique already exists in our database
			foreach ($unique_fields as $unique_field) {
				$ids = DB::table($this->database_table)
					->select('id')
					->where($unique_field, '=', $data[$unique_field])
					->where('id', '!=', $model_id)
					->get();

				// if there is duplicate data for an unique field: get new seeder data
				if ($ids) {
					$data_is_unique = False;
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
	protected function _fill_create_form($data) {

		$this->_fill_form($data, 'create');
	}


	/**
	 * Wrapper to fill a create form.
	 *
	 * @author Patrick Reichel
	 */
	protected function _fill_edit_form($data) {

		$this->_fill_form($data, 'update');
	}


	/**
	 * Fills a form with given data depending on form structure.
	 *
	 * @author Patrick Reichel
	 */
	protected function _fill_form($data, $method) {

		if ($this->debug) echo "Filling form\n";

		foreach (self::$edit_field_structure[$this->class_name] as $field_name => $structure) {

			// on update: only update defined fields
			if ($method == 'update') {
				if (!in_array($field_name, $this->update_fields)) {
					continue;
				}
			}

			// if the faker gave no data for the name => this field seems not to be required
			if (!$faked_data = @$data[$field_name]) {
				continue;
			}

			// convert DateTime objects to string
			if ($faked_data instanceof DateTime) {
				$_ = (array) $faked_data;
				$faked_data = explode(" ", $_['date'])[0];
			}
			// fill depending on field type
			switch ($structure['type']) {

			case 'select':
			case 'radio':
				// use faker data only if available as option; choose random value out of available randomly
				if (!in_array($faked_data, $structure['values'])) {
					$faked_data = $structure['values'][array_rand($structure['values'])];
				}

				if ($this->debug) echo "Selecting “".$faked_data."” in $field_name\n";
				$this->select($faked_data, $field_name);
				break;

			case 'checkbox':
				if (boolval($faked_data)) {
					if ($this->debug) echo "Checking $field_name\n";
					$this->check($field_name);
				}
				else {
					if ($this->debug) echo "Unchecking $field_name\n";
					$this->uncheck($field_name);
				}
				break;

			default:
				// simply put faked data into the field (should be text or textarea)
				if ($this->debug) echo "Typing “".$faked_data."” in $field_name\n";
				$this->type($faked_data, $field_name);
				break;

			}
		}

	}


	/**
	 * Try to create without data – we expect this to fail.
	 *
	 * @author Patrick Reichel
	 */
	public function testEmptyCreate() {

		if (!in_array(__FUNCTION__, $this->tests_to_be_run)) {
			echo "	WARNING: Skipping ".$this->class_name."->".__FUNCTION__."() (not in in tests_to_be_run)\n";
			return;
		}

		if ($this->creating_empty_should_fail) {
			$msg_expected = "please correct the following errors";
		}
		else {
			$msg_expected = "Created!";
		};

		$this->actingAs($this->user)
			->visit(route("$this->model_name.create"))
			->press("Save")
			->see($msg_expected)
		;
	}


	protected function _get_create_context() {

		$ret = [];

		if (is_null($this->create_from_model_context)) {
			$ret['instance'] = null;
			$ret['params'] = [];
		}
		else {
			$model = $this->create_from_model_context;
			$instance = $model::all()->random(1);
			$ret['instance'] = $instance;
			$ret['params'] = [$instance->table."_id" => $instance->id];
		}

		return $ret;
	}


	/**
	 * Get model IDs that should be visible on index view.
	 *
	 * @author Patrick Reichel
	 */
	protected function _get_ids_visible_on_index_view() {

		$ids = DB::table($this->database_table)
			->select($this->index_view_order_field)
			->whereNull('deleted_at')
			->orderBy($this->index_view_order_field, $this->index_view_order_order)
			->limit($this->testrun_count)
			->get();

		return $ids;
	}


	/**
	 * Check if index view is visible.
	 *
	 * @author Patrick Reichel
	 */
	public function testIndexViewVisible() {

		if (!in_array(__FUNCTION__, $this->tests_to_be_run)) {
			echo "	WARNING: Skipping ".$this->class_name."->".__FUNCTION__."() (not in in tests_to_be_run)\n";
			return;
		}

		$this->actingAs($this->user)
			->visit(route("$this->model_name.index"))
			->see("NMS Prime")
			->see("The next Generation NMS")
			->see($this->model_name);
	}


	/**
	 * Check if datatables return data in index view.
	 *
	 * @author Patrick Reichel
	 */
	public function testIndexViewDatatablesDataAvailable() {

		if (!in_array(__FUNCTION__, $this->tests_to_be_run)) {
			echo "	WARNING: Skipping ".$this->class_name."->".__FUNCTION__."() (not in in tests_to_be_run)\n";
			return;
		}

		$route = $this->model_name.".data";
		if (!Route::has($route)) {
			echo "	WARNING: Skipping ".$this->class_name."->".__FUNCTION__."() (no datatables route ($route) found)\n";
			return;
		}

		$ids = $this->_get_ids_visible_on_index_view();

		foreach ($ids as $id) {
			$id = $id->id;

			$this->actingAs($this->user);
			$this->visit(route("$this->model_name.index"));
			$this->json('GET', route($route), [],  ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
			$this->seeJson([
				/* "ids\[".$id."\]", */
				/* "id" => "100000", */
				/* 'id' =>$id, */
				/* '100000', */
				"checkbox" => "<input style='simple' align='center' class='' name='ids[$id]' type='checkbox' value='1' >",
			]);
		}
	}


	/**
	 * Try to create.
	 *
	 * @author Patrick Reichel
	 */
	public function testCreateWithFakeData() {

		if (!in_array(__FUNCTION__, $this->tests_to_be_run)) {
			echo "	WARNING: Skipping ".$this->class_name."->".__FUNCTION__."() (not in in tests_to_be_run)\n";
			return;
		}

		for ($i = 0; $i < $this->testrun_count; $i++) {

			$context = $this->_get_create_context();
			$data = $this->_get_fake_data($context['instance']);

			$this->actingAs($this->user)
				->visit(route("$this->model_name.create", $context['params']));

			$this->_fill_create_form($data);

			$this->press("_save")
				->see("Created!")
			;

			// if we end up in edit view: generated successfully (otherwise we keep staying in create view)
			$_ = explode('/', $this->currentUri);
			if ((array_pop($_) == 'edit')) {
				$id = array_pop($_);
				if (is_numeric($id) && ($id != '0')) {
					array_push(self::$created_entity_ids, $id);
				}
			}
		}
	}


	/**
	 * Try to create the same data twice.
	 *
	 * @author Patrick Reichel
	 */
	public function testCreateTwiceUsingTheSameData() {

		if (!in_array(__FUNCTION__, $this->tests_to_be_run)) {
			echo "WARNING: Skipping ".$this->class_name."->".__FUNCTION__."() (not in in tests_to_be_run)";
			return;
		}

		$context = $this->_get_create_context();
		$data = $this->_get_fake_data($context['instance']);

		// this is the first create which should run
		$this->actingAs($this->user)
			->visit(route("$this->model_name.create", $context['params']));
		$this->_fill_create_form($data);
		$this->press("_save")
			->see("Created!")
		;

		// this is the second create (using the same data) which usually should fail
		if ($this->creating_twice_should_fail) {
			$msg_expected = "please correct the following errors";
		}
		else {
			$msg_expected = "Created!";
		}
		$this->actingAs($this->user)
			->visit(route("$this->model_name.create"));
		$this->_fill_create_form($data);
		$this->press("_save")
			->see($msg_expected)
		;
	}


	/**
	 * Try to update a database entry.
	 *
	 * @author Patrick Reichel
	 */
	public function testUpdate() {

		if (!in_array(__FUNCTION__, $this->tests_to_be_run)) {
			echo "	WARNING: Skipping ".$this->class_name."->".__FUNCTION__."() (not in in tests_to_be_run)\n";
			return;
		}

		if (!$this->update_fields) {
			echo "	WARNING: No update fields – cannot test!\n";
			return;
		}

		foreach (self::$created_entity_ids as $id) {

			if ($this->debug) echo "\nUpdating $this->model_name $id";

			$context = $this->_get_create_context();
			$data = $this->_get_fake_data($context['instance'], $id);

			$this->actingAs($this->user)
				->visit(route("$this->model_name.edit", $id));

			$this->_fill_edit_form($data);
			$this->press("_save")
				->see("Updated!")
			;
		}

		// clear array (else this data will be used by other models, too)
		self::$created_entity_ids = [];

	}


	/**
	 * Try to delete a database entry.
	 *
	 * @author Patrick Reichel
	 */
	public function testDeleteFromIndexView() {

		if (!in_array(__FUNCTION__, $this->tests_to_be_run)) {
			echo "	WARNING: Skipping ".$this->class_name."->".__FUNCTION__."() (not in in tests_to_be_run)\n";
			return;
		}

		// get IDs visible on index view
		$ids = DB::table($this->database_table)
			->select($this->index_view_order_field)
			->whereNull('deleted_at')
			->orderBy($this->index_view_order_field, $this->index_view_order_order)
			->limit($this->testrun_count)
			->get();

		foreach ($ids as $id) {
			$id = $id->id;

			if ($this->debug) echo "\nDeleting $this->model_name $id";

			$this->actingAs($this->user)
				->visit(route("$this->model_name.index"))
				->post("_delete", [
					"ids[$id]" => '1',
					"_token" => Session::token(),
				]);
			$this->see("Deleted $this->model_name $id");
			$this->notSeeInDatabase($this->database_table, ['deleted_at' => null, 'id' => $id]);
		}
	}


}
