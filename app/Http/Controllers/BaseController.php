<?php

namespace App\Http\Controllers;

use App;
use Module;
use Config;
use File;
use View;
use Validator;
use Input;
use Redirect;
use Route;
use BaseModel;
use Auth;
use NoAuthenticateduserError;
use Log;
use GlobalConfig;
use Illuminate\Support\Facades\Request;
use Yajra\Datatables\Datatables;
use Monolog\Logger;

use App\Exceptions\AuthExceptions;


/*
 * BaseController: The Basic Controller in our MVC design.
 */
class BaseController extends Controller {

	/*
	 * Default VIEW styling options
	 * NOTE: All these values could be used in the inheritances classes
	 */
	protected $save_button = 'Save';
	protected $force_restart_button = 'Force Restart';
	protected $relation_create_button = 'Create';

	// if set to true a create button on index view is available
	protected $index_create_allowed = true;
	protected $index_delete_allowed = true;

	protected $edit_left_md_size = 8;
	protected $index_left_md_size = 12;
	protected $edit_right_md_size = null;

	protected $edit_view_save_button = true;
	protected $edit_view_force_restart_button = false;

	protected $index_tree_view = false;

	/**
	 * Placeholder for Many-to-Many-Relation multiselect fields that should be handled generically
	 */
	protected $many_to_many = [];


	// Auth Vars
	// TODO: move to Auth API
	// protected $permissions = null;
	// protected $permission_cores = array('model', 'net');


	/**
	 * Constructor
	 *
	 * Basically this is a placeholder for eventually later use. I need to
	 * overwrite the constructor in a subclass – and want to call the parent
	 * constructor if there are changes in base classes. But calling the
	 * parent con is only possible if it is explicitely defined…
	 *
	 * @author Patrick Reichel
	 */
	public function __construct()
	{
		// set language
		App::setLocale(BaseViewController::get_user_lang());
	}


	/*
	 * Base Function for Breadcrumb. -> Panel Header Right
	 * overwrite this function in child controller if required.
	 *
	 * NOTE: Breadcrumb means Panel Header Right in Bootstrap language
	 *
	 * @param view_var: the model object to be displayed
	 * @return: array, e.g. [['name' => '..', 'route' => '', 'link' => [$view_var->id]], .. ]
	 * @author: Torsten Schmidt
	 */
	protected function get_form_tabs($view_var)
	{
		$class = \NamespaceController::get_model_name();
		$class_name = substr(strrchr($class, "\\"), 1);

		return ['0' => [
			'name' => 'Logging',
			'route' => 'GuiLog.filter',
			'link' => ['model_id' => $view_var->id, 'model' => $class_name]
		],
		'1' =>
			['name' => "Edit",
				'route' => $class_name.'.edit',
				'link' => ['model_id' => $view_var->id, 'model' => $class_name]
			],
		];
	}


	public static function get_model_obj ()
	{
		$classname = \NamespaceController::get_model_name();

		// Rewrite model to check with new assigned Model
		if (!$classname)
			return null;

		if (!class_exists($classname))
			return null;

		$classname = $classname;
		$obj = new $classname;

		return $obj;
	}

	public static function get_controller_obj()
	{
		$classname = \NamespaceController::get_controller_name();

		if (!$classname)
			return null;

		if (!class_exists($classname))
			return null;

		$obj = new $classname;
		return $obj;
	}



	public static function get_config_modules()
	{
		$modules = Module::enabled();
		$links = ['Global Config' => 'GlobalConfig'];

		foreach($modules as $module)
		{
			$mod_path = explode('/', $module->getPath());
			$tmp = end($mod_path);

			$mod_controller_name = 'Modules\\'.$tmp.'\\Http\\Controllers\\'.$tmp.'Controller';
			$mod_controller = new $mod_controller_name;

			if (method_exists($mod_controller, 'view_form_fields'))
				$links[($module->get('description') == '') ? $tmp : $module->get('description')] = $tmp;
		}

		return $links;
	}


	/**
	 * Set all nullable field without value given to null.
	 * Use this to e.g. set dates to null (instead of 0000-00-00).
	 *
	 * Call this method on demand from your prepare_input()
	 *
	 * @author Patrick Reichel
	 *
	 * @param $nullable_fields array containing fields to check
	 */
	protected function _nullify_fields($data, $nullable_fields=[]) {

		foreach ($this->view_form_fields(static::get_model_obj()) as $field)
		{
			// set all nullable fields to null if not given
			if (array_key_exists($field['name'], $data)) {
				if (array_search($field['name'], $nullable_fields) !== False) {
					if ($data[$field['name']] == '') {
						$data[$field['name']] = null;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Returns a default input data array, that shall be overwritten
	 * from the appropriate model controller if needed.
	 *
	 * Note: Will be running before Validation
	 *
	 * Tasks: Checkbox Entries will automatically set to 0 if not checked
	 *
	 */
	protected function prepare_input($data)
	{
		// Checkbox Unset ?
		foreach ($this->view_form_fields(static::get_model_obj()) as $field)
		{
			// skip file upload fields
			if ($field['form_type'] == 'file')
				continue;

			// Checkbox Unset ?
			if(!isset($data[$field['name']]) && ($field['form_type'] == 'checkbox'))
				$data[$field['name']] = 0;

			// JavaScript controlled checkboxes sometimes returns “on” if checked – which results in
			// logical false (=0) in database so we have to overwrite this by 1
			// this is e.g. the case for the active checkbox on ProvVoip\Phonenumber
			// the value in $_POST seems to be browser dependend – extend the array if needed
			if (
				($field['form_type'] == 'checkbox')
				&&
				(in_array(\Str::lower($data[$field['name']]), ["on", "checked"]))
			) {
				$data['active'] = "1";
			}

			// multiple select?
			if ($field['form_type'] == 'select' && isset($field['options']['multiple'])) {
				$field['name'] = str_replace('[]', '', $field['name']);
				continue; 			// multiselects will have array in data so don't trim
			}

			// trim all inputs as default
			$data[$field['name']] = trim($data[$field['name']]);

		}

		return $data;
	}


	/**
	 * Returns a default input data array, that shall be overwritten
	 * from the appropriate model controller if needed.
	 *
	 * Note: Will be running _after_ Validation
	 */
	protected function prepare_input_post_validation($data)
	{
		return $data;
	}


	/**
	 * Returns an array of validation rules in dependence of the formular Input data
	 * of the http request, that shall be overwritten from the appropriate model
	 * controller if needed.
	 *
	 * Note: Will be running before Validation
	 */
	protected function prepare_rules($rules, $data)
	{
		return $rules;
	}


	/**
	 * Prepare Breadcrumb - $panel_right header
	 * Priority Handling: get_form_tabs(), view_has_many()
	 *
	 * @param view_var: the view_var parameter from edit() context
	 * @return panel_right prepared array for default.blade
	 */
	protected function prepare_tabs($view_var)
	{
		// Version 1
		$ret = $this->get_form_tabs($view_var);

		if (count($ret) > 2)
			return $ret;

		// view_has_many() Version 2
		$a = $view_var->view_has_many();
		if (BaseViewController::get_view_has_many_api_version($a) == 2)
		{
			// get actual blade to $b
			$b = current($a);
			$c = [];

			for ($i = 0; $i < sizeof($a); $i++)
			{
				array_push($c, ['name' => key($a), 'route' => \NamespaceController::get_route_name().'.edit', 'link' => [$view_var->id, 'blade='.$i]]);
				$b = next($a);
			}

			// add tab for GuiLog
			array_push($c, $ret[0]);

			return $c;
		}
		else {
			return $ret;
		}
	}


	/**
	 * Handle file uploads.
	 * - check if a file is uploaded
	 * - if so:
	 *   - move file to dst_path
	 *   - overwrite base_field in Input with filename
	 *
	 * @param base_field Input field to be processed
	 * @param dst_path Path to move uploaded file in
	 * @author Patrick Reichel
	 */
	protected function handle_file_upload($base_field, $dst_path) {

		$upload_field = $base_field."_upload";

		if (Input::hasFile($upload_field)) {

			// get filename
			$filename = Input::file($upload_field)->getClientOriginalName();

			// move file
			Input::file($upload_field)->move($dst_path, $filename);

			// place filename as chosen value in Input field
			Input::merge(array($base_field => $filename));
		}

	}


	/**
	 * Set required default Variables for View
	 * Use it like:
	 *   View::make('Route.Name', $this->compact_prep_view(compact('ownvar1', 'ownvar2')));
	 *
	 * @author Torsten Schmidt
	 */
	public function compact_prep_view ()
	{
		$a     = func_get_args()[0];

		$model = static::get_model_obj();
		if (!$model)
			$model = new BaseModel;

		if(!isset($a['networks'])){
			$a['networks'] = [];
			if (\PPModule::is_active('HfcReq'))
				$a['networks'] = \Modules\HfcReq\Entities\NetElement::get_all_net();
		}

		if(!isset($a['view_header_links']))
			$a['view_header_links'] = BaseViewController::view_main_menus();


		if(!isset($a['route_name']))
			$a['route_name'] = \NamespaceController::get_route_name();

		if(!isset($a['model_name']))
			$a['model_name'] = \NamespaceController::get_model_name();

		if(!isset($a['view_header']))
			$a['view_header'] = $model->view_headline();

		if(!isset($a['view_no_entries']))
			$a['view_no_entries'] = $model->view_no_entries();

		if(!isset($a['headline']))
			$a['headline'] = '';

		if (!isset($a['form_update']))
			$a['form_update'] = \NamespaceController::get_route_name().'.update';

		if (!isset($a['edit_left_md_size']))
			$a['edit_left_md_size'] = $this->edit_left_md_size;

		if (!isset($a['index_left_md_size']))
			$a['index_left_md_size'] = $this->index_left_md_size;

		if (!is_null($this->edit_right_md_size) && !isset($a['edit_right_md_size']))
			$a['edit_right_md_size'] = $this->edit_right_md_size;

		if (!isset($a['html_title']))
			$a['html_title'] = 'NMS Prime - '.\App\Http\Controllers\BaseViewController::translate_view(\NamespaceController::module_get_pure_model_name(),'Header');

		if (( \PPModule::is_active('Provvoipenvia')) && (!isset($a['envia_interactioncount'])) )
			$a['envia_interactioncount'] = \Modules\ProvVoipEnvia\Entities\EnviaOrder::get_user_interaction_needing_enviaorder_count();

		$a['save_button'] = $this->save_button;
		$a['force_restart_button'] = $this->force_restart_button;
		$a['edit_view_save_button'] = $this->edit_view_save_button;
		$a['edit_view_force_restart_button'] = $this->edit_view_force_restart_button;

		// Get Framework Informations
		$gc = GlobalConfig::first();
		$a['framework']['header1'] = $gc->headline1;
		$a['framework']['header2'] = $gc->headline2;
		$a['framework']['version'] = $gc->version();

		return $a;
	}


	/**
	 * Perform a fulltext search.
	 *
	 * @author Patrick Reichel
	 */
	public function fulltextSearch() {
		// get the search scope
		$scope = Input::get('scope');

		// get the mode to use and transform to sql syntax
		$mode = Input::get('mode');

		// get the query to search for
		$query = Input::get('query');

		if ($scope == 'all')
		{
			$view_path = 'Generic.searchglobal';
			$obj       = new BaseModel;
			$view_header = 'Global Search';
		}
		else
		{
			$obj       = static::get_model_obj();
			$view_path = 'Generic.index';

			if (View::exists(\NamespaceController::get_view_name().'.index'))
				$view_path = \NamespaceController::get_view_name().'.index';
		}

		$create_allowed = static::get_controller_obj()->index_create_allowed;
		$delete_allowed = static::get_controller_obj()->index_delete_allowed;

		// perform the search
		foreach ($obj->getFulltextSearchResults($scope, $mode, $query, Input::get('preselect_field'), Input::get('preselect_value')) as $result)
		{
			if(!isset($view_var))
				$view_var = $result->get();
			else
				$view_var = $view_var->merge($result->get());
		}

		return View::make($view_path, $this->compact_prep_view(compact('view_header', 'view_var', 'create_allowed', 'delete_allowed', 'query', 'scope')));
	}


	/**
	 * Overwrite this method in your controllers to inject additional data in your edit view
	 * Default is an empty array that simply will be ignored on generic views
	 *
	 * For an example view EnviaOrder and their edit.blade.php
	 *
	 * @author Patrick Reichel
	 *
	 * @return data to be injected; should be an array
	 */
	protected function _get_additional_data_for_edit_view($model) {

		return array();
	}


	/**
	 * Use this in your Controller->view_form_fields() methods to add a first [0 => ''] in front of your options coming from database
	 * Don't use array_merge for this topic as this will reassing numerical keys and in doing so destroying the mapping of database IDs!!
	 *
	 * Watch ProductController for a usage example.
	 *
	 * @author Patrick Reichel
	 *
	 * @param $options the options array generated from database
	 * @param $first_value value to be set at $options[0] – defaults to empty string
	 *
	 * @return $options array with 0 element on first position
	 */
	protected function _add_empty_first_element_to_options($options, $first_value='') {

		$ret = [0 => $first_value];

		foreach ($options as $key => $value) {
			$ret[$key] = $value;
		}

		return $ret;
	}


	/**
	 * Display a listing of all objects of the calling model
	 *
	 * @return View
	 */
	public function index()
	{
		$model 			= static::get_model_obj();
		$headline  		= \App\Http\Controllers\BaseViewController::translate_view( $model->view_headline(), 'Header' , 2 );
		$view_header 	= \App\Http\Controllers\BaseViewController::translate_view('Overview','Header');
		$create_allowed = static::get_controller_obj()->index_create_allowed;
		$delete_allowed = static::get_controller_obj()->index_delete_allowed;

		if ($this->index_tree_view)
		{
			$view_var = $model::where('parent_id', 0)->get();
			$undeletables = $model::undeletables();

			return View::make ('Generic.tree', $this->compact_prep_view(compact('headline', 'view_header', 'view_var', 'create_allowed', 'undeletables')));
		}

		$view_path = 'Generic.index';
		if (View::exists(\NamespaceController::get_view_name().'.index'))
			$view_path = \NamespaceController::get_view_name().'.index';

		// TODO: show only entries a user has at view rights on model and net!!
		Log::warning('Showing only index() elements a user can access is not yet implemented');

		return View::make ($view_path, $this->compact_prep_view(compact('headline','view_header', 'model', 'create_allowed', 'delete_allowed')));
	}

	/**
	 * Show the form for creating a new model item
	 *
	 * @return View
	 */
	public function create()
	{
		$model = static::get_model_obj();
		$view_header = \App\Http\Controllers\BaseViewController::translate_view( $model->view_headline() , 'Header');
		$headline    = BaseViewController::compute_headline(\NamespaceController::get_route_name(), $view_header , NULL, $_GET);
		$fields 	 = BaseViewController::prepare_form_fields(static::get_controller_obj()->view_form_fields($model), $model);
		$form_fields = BaseViewController::add_html_string ($fields, 'create');
		// $form_fields = BaseViewController::add_html_string (static::get_controller_obj()->view_form_fields($model), $model, 'create');

		$view_path = 'Generic.create';
		$form_path = 'Generic.form';

		// proof if there is a special view for the calling model
		if (View::exists(\NamespaceController::get_view_name().'.create'))
			$view_path = \NamespaceController::get_view_name().'.create';
		if (View::exists(\NamespaceController::get_view_name().'.form'))
			$form_path = \NamespaceController::get_view_name().'.form';


		return View::make($view_path, $this->compact_prep_view(compact('view_header', 'form_fields', 'form_path', 'headline')));
	}


	/**
	 * Generic store function - stores an object of the calling model
	 * @param redirect: if set to false returns id of the new created object (default: true)
	 * @return: html redirection to edit page (or if param $redirect is false the new added object id)
	 */
	public function store($redirect = true)
	{
		$obj = static::get_model_obj();
		$controller = static::get_controller_obj();

		// Prepare and Validate Input
		$data 		= $controller->prepare_input(Input::all());
		$rules 		= $controller->prepare_rules($obj::rules(), $data);
		$validator  = Validator::make($data, $rules);
		$data 		= $controller->prepare_input_post_validation ($data);

		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput()->with('message', 'please correct the following errors')->with('message_color', 'danger');
		}

		$obj = $obj::create($data);

		// Add N:M Relations
		$this->_set_many_to_many_relations($obj, $data);

		$id  = $obj->id;
		if (!$redirect)
			return $id;

		return Redirect::route(\NamespaceController::get_route_name().'.edit', $id)->with('message', 'Created!')->with('message_color', 'success');
	}


	/**
	 * Show the editing form of the calling Object
	 *
	 * @param  int  $id
	 * @return View
	 */
	public function edit($id)
	{
		$model    = static::get_model_obj();
		$view_var = $model->findOrFail($id);
		$view_header 	= BaseViewController::translate_view($model->view_headline(),'Header');
		$headline       = BaseViewController::compute_headline(\NamespaceController::get_route_name(), $view_header, $view_var);

		$fields 		= BaseViewController::prepare_form_fields(static::get_controller_obj()->view_form_fields($view_var), $view_var);
		$form_fields	= BaseViewController::add_html_string ($fields, 'edit');
		// $form_fields	= BaseViewController::add_html_string (static::get_controller_obj()->view_form_fields($view_var), $view_var, 'edit');

		// prepare_tabs & prep_right_panels are redundant - TODO: improve
		$panel_right    = $this->prepare_tabs($view_var);
		$relations      = BaseViewController::prep_right_panels($view_var);

		// check if there is additional data to be passed to blade template
		// on demand overwrite base method _get_additional_data_for_edit_view($model)
		$additional_data = $this->_get_additional_data_for_edit_view($view_var);

		// we explicitly set the method to call in relation links
		// if not given we set default to “edit“ to meet former behavior
		foreach ($relations as $rel_key => $relation) {
			if (!array_key_exists('method', $relation)) {
				$method = 'edit';
			}
			else {
				$method = 'show';
			}
		}

		$view_path = 'Generic.edit';
		$form_path = 'Generic.form';

		// proof if there are special views for the calling model
		if (View::exists(\NamespaceController::get_view_name().'.edit'))
			$view_path = \NamespaceController::get_view_name().'.edit';
		if (View::exists(\NamespaceController::get_view_name().'.form'))
			$form_path = \NamespaceController::get_view_name().'.form';

		// $config_routes = BaseController::get_config_modules();
		// return View::make ($view_path, $this->compact_prep_view(compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields', 'config_routes', 'link_header', 'panel_right', 'relations', 'extra_data')));
		return View::make ($view_path, $this->compact_prep_view(compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields', 'headline', 'panel_right', 'relations', 'method', 'additional_data')));
	}


	/**
	 * Update the specified data in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$obj = static::get_model_obj()->findOrFail($id);
		$controller = static::get_controller_obj();

		// Prepare and Validate Input
		$data      = $controller->prepare_input(Input::all());
		$rules     = $controller->prepare_rules($obj::rules($id), $data);
		$validator = Validator::make($data, $rules);
		$data      = $controller->prepare_input_post_validation ($data);

		if ($validator->fails()) {
			Log::info ('Validation Rule Error: '.$validator->errors());
			return Redirect::back()->withErrors($validator)->withInput()->with('message', 'please correct the following errors')->with('message_color', 'danger');
		}

		// update timestamp, this forces to run all observer's
		// Note: calling touch() forces a direct save() which calls all observers before we update $data
		//       when exit in middleware to a new view page (like Modem restart) this kill update process
		//       so the solution is not to run touch(), we set the updated_at field directly
		$data['updated_at'] = \Carbon\Carbon::now(Config::get('app.timezone'));

		// The Update
		// Note: Eloquent Update requires updated_at to either be in the fillable array or to have a guarded field
		//       without updated_at field. So we globally use a guarded field from now, to use the update timestamp
		$obj->update($data);

		// Add N:M Relations
		$this->_set_many_to_many_relations($obj, $data);

		// error msg created while observer execution
		$msg = \Session::has('error') ? \Session::get('error') : 'Updated';
		$color = \Session::has('error') ? 'warning' : 'info';

		$route_model = \NamespaceController::get_route_name();

		if (in_array($route_model, self::get_config_modules()))
			return Redirect::route('Config.index');

		return Redirect::route($route_model.'.edit', $id)->with('message', $msg)->with('message_color', $color);
	}


	/**
	 * Store Many to Many Relations in Pivot table
	 *
	 * IMPORTANT NOTES:
	 *	 To assign a model to the pivot table we need an extra multiselect field in the controllers
	 *	 	view_form_fields() that must be mentioned inside the guarded array of the model and the many_to_many array of the Controller!!
	 *	 The multiselect's field name must be in form of the models relation function and a concatenated '_ids'
	 *		like: '<relation-function>_ids' , e.g. 'users_ids' for the multiselect in Tickets view to assign users
	 *
	 * @param Object 	The Object to store/update
	 * @param Array 	Input Data
	 *
	 * @author Nino Ryschawy
	 */
	private function _set_many_to_many_relations($obj, $data)
	{
		if (!$this->many_to_many)
			return;

		foreach ($this->many_to_many as $field)
		{
			if (!isset($data[$field]))
				$data[$field] = [];

			// relation-function_id
			$func = explode('_', $field)[0];
			$attached = $obj->$func->pluck('id')->all();

			// attach new assignments
			foreach ($data[$field] as $rel_id)
			{
				if ($rel_id && !in_array($rel_id, $attached))
					$obj->$func()->attach($rel_id, ['created_at' => date('Y-m-d H:i:s')]);
			}

			// detach removed assignments (from selected multiselect options)
			foreach ($attached as $rel_id)
			{
				if (!in_array($rel_id, $data[$field]))
					$obj->$func()->detach($rel_id);
			}
		}
	}


	/**
	 * Removes a specified model object from storage
	 *
	 * @param  int  $id: bulk delete if == 0
	 * @return Response
	 */
	public function destroy($id)
	{
		// helper to inform the user about success on deletion
		$to_delete = 0;
		$deleted = 0;

		// bulk delete
		if ($id == 0)
		{
			// Error Message when no Model is specified - NOTE: delete_message must be an array of the structure below !
			if (!Input::get('ids'))
				return Redirect::back()->with('delete_message', ['message' => 'No Entry For Deletion specified', 'class' => \NamespaceController::get_route_name(), 'color' => 'danger']);

			$obj = static::get_model_obj();

			foreach (Input::get('ids') as $id => $val) {
				$to_delete++;
				if ($obj->findOrFail($id)->delete()) {
					$deleted++;
				}
			}
		}
		else {
			$to_delete++;
			$obj = static::get_model_obj();
			if ($obj->findOrFail($id)->delete()) {
				$deleted++;
			}
		}
		$obj = isset($obj) ? $obj : static::get_model_obj();
		$class = \NamespaceController::get_route_name();

		if (!$deleted && !$obj->force_delete) {
			$message = 'Could not delete '.$class;
			$color = 'danger';
		}
		elseif (($deleted == $to_delete) || $obj->force_delete) {
			$message = 'Successful deleted '.$class;
			$color = 'success';
		}
		else {
			$message = 'Deleted '.$deleted.' out of '.$to_delete.' '.$class;
			$color = 'warning';
		}

		return Redirect::back()->with('delete_message', ['message' => $message, 'class' => $class, 'color' => $color]);
	}


	/**
	 * Detach a pivot entry of an n-m relationship
	 *
	 * @param 	id 			Integer 	Model ID the relational model is attached to
	 * @param 	function 	String 		Function Name of the N-M Relation
	 * @return 	Response 	Object 		Redirect back
	 *
	 * @author Nino Ryschawy
	 */
	public function detach($id, $function)
	{
		$model = \NamespaceController::get_model_name();
		$model = $model::find($id);

		if (\Input::has('ids'))
			$model->{$function}()->detach(array_keys(\Input::get('ids')));

		return \Redirect::back();
	}


	public function dump($id) {
		return static::get_model_obj()->findOrFail($id);
	}

	public function dumpall() {
		return static::get_model_obj()->all();
	}


// Deprecated:
	protected $output_format;

	/**
	 *  json abstraction layer
	 */
	protected function json ()
	{
		$this->output_format = 'json';

		$data = Input::all();
		$id   = $data['id'];
		$func = $data['function'];

		if ($func == 'update')
			return $this->update($id);
	}


	/**
	 *  Maybe a generic redirect is an option, but
	 *  howto handle fails etc. ?
	 */
	protected function generic_return ($view, $param = null)
	{
		if ($this->output_format == 'json')
			return $param;

		if (isset($param))
			return Redirect::route($view, $param);
		else
			return Redirect::route($view);
	}


	/**
	 * Tree View Specific Stuff
	 *
	 * TODO: Implement the Tree View as Javascript Tree Table - preparations are already made in comments (use jstree.min.js)
	 * 		 see Color Admin Bootstrap Theme: http://wrapbootstrap.com/preview/WB0N89JMK -> UI-Elements -> Tree View
	 *
	 * @author Nino Ryschawy
	 *
	 * global Variables
	 *	$INDEX  : used for shifting the children elements
	 *	$I 		: used to increment over specficied colours (defined in variable)
	 */
	public static $INDEX = 0;
	public static $I = 0;
	public static $colours = ['', 'text-danger', 'text-success', 'text-warning', 'text-info'];


	/**
	 * Returns the Tree View (Table) as HTML Text
	 *
	 * IMPORTANT NOTES
		* If the Model uses the Generic BaseController@index function a separate index.blade.php has to be installed in
		*	modules/Resources/Modelname/ that includes the Generic.tree blade
		* The Generic.tree blade calls this function
		* The Model currently has to have a function called get_tree_list that shall return the ordered tree of objects
		*	(with delete_disabled) - see NetElementType.php
	 */
	public static function make_tree_table()
	{
		$data = '';

		// tree with select fields
		// $data .= '<div id="jstree-checkable" class="jstree jstree-2 jstree-default jstree-checkbox-selection" role="tree" aria-multiselectable="true" tabindex="0" aria-activedescendant="j1" aria-busy="false" aria-selected="false">';
		// $data .= '<ul class="jstree-container-ul jstree-children jstree-wholerow-ul jstree-no-dots" role="group">';

		// default tree
		// $data = '<div id="jstree-default" class="jstree jstree-1 jstree-default" role="tree" aria-multiselectable="true" tabindex="0" aria-activedescendant="j1" aria-busy="false">';
		// $data .= '<ul class="jstree-children" role="group" style>';

		$model = NamespaceController::get_model_name();
		$data .= self::_create_index_view_data($model::get_tree_list());

		// $data .= '</ul></div></div>';

		return $data;
	}


	/**
	 * writes whole index view table data as HTML in string
	 *
	 * @param array with all Model Objects in hierarchical tree structure
	 *
	 * @author Nino Ryschawy
	 */
	private static function _create_index_view_data($ordered_tree)
	{
		$data = '';

		foreach ($ordered_tree as $object)
		// foreach ($ordered_tree as $key => $object)
		{
			if (is_array($object))
			{
				self::$INDEX += 1;
				if (self::$INDEX == 1)
					self::$I--;

				// $data .= '<ul role="group" class="jstree-children" style>';
				$data .= self::_create_index_view_data($object);
				// $data .= '</ul>';
			}
			else
				// $data .= self::_print_label_elem($object, isset($ordered_tree[$key+1]));
				$data .= self::_print_label_elem($object);

			if (self::$INDEX == 0)
				self::$I++;
		}

		self::$INDEX -= 1;
		$data .= (self::$INDEX == 0) && (strpos(substr($data, strlen($data)-8), '<br><br>') === false) ? '<br>' : '';

		return $data;
	}

	/**
	 * Returns the HTML string for one label Element for Tree Index View
	 *
	 * @param $object 	Model Object
	 *
	 * @author Nino Ryschawy
	 *
	 * TODO: implement with jstree.min.js
	 */
	// public static function _print_label_elem($object, $list = false)
	private static function _print_label_elem($object)
	{
		$cur_model_complete = get_class($object);
		$cur_model_parts = explode('\\', $cur_model_complete);
		$cur_model = array_pop($cur_model_parts);

		$data = '';

		// default tree
		// $data .= '<li role="treeitem" data-jstree="{&quot;opened&quot;:true, &quot;selected&quot;:true" aria-selected="false" aria-level="'.self::$INDEX.'" aria-labelledby="'.self::$I.'_anchor" aria-expanded="true" id="j'.self::$I.'" class="jstree-node jstree-open">';
		// 	$data .= $list ? '<i class="jstree-icon jstree-ocl" role="presentation"></i>' : '';

		// tree with select fields
		// $data .= '<li role="treeitem" aria-selected="false" aria-level="'.self::$INDEX.'" aria-labelledby="'.self::$I.'_anchor" id="j'.self::$I.'" class="jstree-node  jstree-leaf">';
		// 	$data .= '<div unselectable="on" role="presentation" class="jstree-wholerow">&nbsp;</div><i class="jstree-icon jstree-ocl" role="presentation"></i>';

		for ($cnt = 0; $cnt <=self::$INDEX; $cnt++)
			$data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

		$data .= \Form::checkbox('ids['.$object->id.']', 1, Null, null, ['style' => 'simple', 'disabled' => $object->index_delete_disabled ? 'disabled' : null]).'&nbsp;&nbsp;';
		// $data .= \HTML::linkRoute($cur_model.'.edit', $object->view_index_label(), $object->id, ['class' => self::$colours[self::$I % count(self::$colours)]]);
		$name = self::$INDEX == 0 ? '<strong>'.$object->view_index_label().'</strong>' : $object->view_index_label();
		$data .= '<a class="'.self::$colours[self::$I % count(self::$colours)].'" href="'.route($cur_model.'.edit', $object->id).'">'.$name.'</a>';
		$data .= '<br>';
		// link for javascript tree
		// $data .= '<a class="jstree-anchor" href="'.route($cur_model.'.edit', $object->view_index_label(), $object->id).'" tabindex="-1" id="'.self::$I.'_anchor">';
		// $data .= '<i class="jstree-icon jstree-themeicon fa fa-folder text-warning fa-lg jstree-themeicon-custom" role="presentation"></i>';
		// $data .= $object->view_index_label().'</a>';
		// $data .= '</li>';

		return $data;
	}



	/**
	 * Return a List of Filenames (e.g. PDF Logos, Tex Templates)
	 *
	 * @param 	dir 	Directory name relative to storage/app/config/
	 * @return  Array 	of Filenames
	 *
	 * @author 	Nino Ryschawy
	 */
	public static function get_storage_file_list($dir)
	{
		$files_raw = \Storage::files("config/$dir");
		$files[null] = 'None';

		foreach ($files_raw as $file)
		{
			$name = explode('/', $file);
			$name = array_pop($name);
			$files[$name] = $name;
		}

		return $files;
	}


	/**
	 * Grep Log entries of all severity Levels above the specified one of a specific Logfile
	 *
	 * @author Nino Ryschawy
	 *
	 * @return Array 		Last Log entry first
	 */
	public static function get_logs($filename, $severity_level = Logger::DEBUG)
	{
		$levels = Logger::getLevels();

		foreach ($levels as $key => $value)
		{
			if ($severity_level <= $value)
				break;

			unset($levels[$key]);
		}

		$levels 	= implode('\|', array_keys($levels));
		$filename 	= $filename[0] == '/' ? $filename : storage_path("logs/$filename");

		exec ("grep \"$levels\" $filename", $logs);

		return array_reverse($logs);
	}

	/**
     * Process datatables ajax request.
	 *
     * For Performance tests and fast Copy and Paste: $start = microtime(true) and $end = microtime(true);
	 * calls view_index_label() which determines how datatables are configured
	 * you can find examples in every model with index page
	 * Documentation is written here, because this seems like the first place to look for it
	 * @param table - tablename of model
	 * @param index_header - array like [$table.'.column1' , $table.'.column2', ..., 'foreigntable1.column1', 'foreigntable2.column1', ..., 'customcolumn']
	 * order in index_header is important and datatables will have the column order given here
	 * @param bsclass - defines a Bootstrap class for colering the Rows
	 * @param header - defines whats written in Breadcrumbs Header at Edit and Create Pages
	 * @param edit - array like [$table.'.column1' => 'customfunction', 'foreigntable.column' => 'customfunction', 'customcolumn' => 'customfunction']
	 * customfunction will be called for every element in table.column, foreigntable.column or customcolumn
	 * CAREFUL customcolumn will not be sortable or searchable - to use them anyways use the sortsearch key
	 * @param eager_loading array like [foreigntable1, foreigntable2, ...] - eager load foreign tables
	 * @param order_by array like ['0' => 'asc'] - order table by id in ascending order, ['1' => 'desc'] - order table after first column in descending order
	 * @param sortsearch array like ['customcolumn' => 'false'] prevents that user is able to sort what is impossible => prevent errors
	 *
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @author Christian Schramm
     */
    public function index_datatables_ajax()
    {
		$model = static::get_model_obj();
		$dt_config =  $model->view_index_label();

		$header_fields = $dt_config['index_header'];
		$edit_column_data = isset($dt_config['edit']) ? $dt_config['edit'] : [];
		$filter_column_data = isset($dt_config['filter']) ? $dt_config['filter'] : [];
		$eager_loading_tables = isset($dt_config['eager_loading']) ? $dt_config['eager_loading'] : [];
		$additional_raw_where_clauses = isset($dt_config['where_clauses']) ? $dt_config['where_clauses'] : [];

		// if no id Column is drawn, draw it to generate links with id
		!array_has($header_fields, $dt_config['table'].'.id') ? array_push($header_fields, 'id') : null;

		if (empty($eager_loading_tables) ){ //use eager loading only when its needed
			$request_query = $model::select($dt_config['table'].'.*');
			$first_column = substr(head($header_fields), strlen($dt_config["table"]) + 1);
		} else {
			$request_query = $model::with($eager_loading_tables)->select($dt_config['table'].'.*'); //eager loading | select($select_column_data);
			if (starts_with(head($header_fields), $dt_config["table"]))
				$first_column = substr(head($header_fields), strlen($dt_config["table"]) + 1);
			else
				$first_column = head($header_fields);
		}

		// apply additional where clauses
		foreach ($additional_raw_where_clauses as $where_clause) {
			$request_query = $request_query->whereRaw($where_clause);
		}

		$DT = Datatables::of($request_query);
		$DT ->addColumn('responsive', '')
			->addColumn('checkbox', '');

		foreach ($filter_column_data as $column => $custom_query) {
			$DT->filterColumn($column, function($query, $keyword) use ($custom_query) {
				$query->whereRaw( $custom_query, ["%{$keyword}%"]);
			});
		};

		$DT	->editColumn('checkbox', function ($object) {
				if(method_exists($object , 'set_index_delete'))
					$object->set_index_delete();

				return "<input style='simple' align='center' class='' name='ids[".$object->id."]' type='checkbox' value='1' ".
				($object->index_delete_disabled ? "disabled" : '').">";
			})
            ->editColumn($first_column, function ($object) use ($first_column) {
				return '<a href="'.route(\NamespaceController::get_route_name().'.edit', $object->id).'"><strong>'.
				$object->view_icon().array_get($object, $first_column).'</strong></a>';
			});

		foreach ($edit_column_data as $column => $functionname) {
			if($column == $first_column)
			{
			$DT->editColumn($column, function($object) use ($functionname) {
				return '<a href="'.route(\NamespaceController::get_route_name().'.edit', $object->id).'"><strong>'.
				$object->view_icon().$object->$functionname().'</strong></a>';
			});
			} else {
			$DT->editColumn($column, function($object) use ($functionname) {
				return $object->$functionname();
			});
		}
		};

		$DT	->setRowClass(function ($object) {
			$bsclass = isset($object->view_index_label()['bsclass']) ? $object->view_index_label()['bsclass'] : 'info';
			return $bsclass;
		});

		return $DT->make(true);
	}
}
