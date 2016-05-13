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

use App\Exceptions\AuthExceptions;


class BaseController extends Controller {

	protected $output_format;
	protected $save_button = 'Save';
	protected $relation_create_button = 'Create';
	protected $index_create_allowed = true;

	protected $permissions = null;
	protected $permission_cores = array('model', 'net');


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
	public function get_form_breadcrumb($view_var)
	{
		return null;
	}


// Auth Stuff: TODO: move to a separate Controller

	/**
	 * Get permissions array from model.
	 * This will overwrite an existing array.
	 *
	 * @throws NoAuthenticatedUserError if no user is logged in
	 */
	protected function _get_permissions() {

		// get the currently authenticated user
		$cur_user = Auth::user();

		$this->permissions = array();

		// no user logged in
		if (is_null($cur_user)) {
			throw new NoAuthenticateduserError("No user logged in");
		}

		// get permissions for each role from model
		$this->permissions['model'] = $cur_user->get_model_permissions();
	}


	/**
	 * Check if user has permission to continue.
	 * Use this method to protect your methods
	 *
	 * @author Patrick Reichel
	 *
	 * @param $access [view|create|edit|delete]
	 * @param $model_to_check model path and name (in format as is stored in database); use current model if not given
	 *
	 * @throws NoAuthenticatedUserError if no user is logged in
	 * @throws NoModelPermissionError if user is not allowed to acces the model
	 * @throws InvalidPermissionsRequest if permission request is invalid
	 * @throws InsufficientRightsError if user has not the specific right needed to perform an action
	 */
	protected function _check_permissions($access, $model_to_check=null) {

		// get the currently authenticated user
		$cur_user = Auth::user();

		// if no model is given: use current model
		if (is_null($model_to_check)) {
			$model_to_check = $this->get_model_name();
		}

		// no user logged in
		if (is_null($cur_user)) {
			throw new AuthExceptions('Login Required');
		}

		// build permissions array for easy access to user rights
		if (is_null($this->permissions)) {
			$this->_get_permissions();
		}

		// check model rights
		if (!array_key_exists($model_to_check, $this->permissions['model'])) {
			throw new AuthExceptions('Access to model '.$model_to_check.' not allowed for user '.$cur_user->login_name.'.');
		}
		if (!array_key_exists($access, $this->permissions['model'][$model_to_check])) {
			throw new AuthExceptions('Something went wrong asking for '.$access.' right in '.$model_to_check.' for user '.$cur_user->login_name.'.');
		}
		if ($this->permissions['model'][$model_to_check][$access] == 0) {
			throw new AuthExceptions('User '.$cur_user->login_name.' is not allowed to '.$access.' in '.$model_to_check.'.');
		}

		// TODO: check net rights
	}


// Class Relation Functions: TODO: move to a separate Controller (if possible?)

	/**
	 * Gets class name
	 * @param
	 * @return
	 */
	protected function get_model_name()
	{
		return explode ('Controller', explode ('\\', explode ('@', Route::getCurrentRoute()->getActionName())[0])[3])[0];
	}

	protected function get_model_obj ()
	{
		$classname = $this->get_model_name();

		if (!$classname)
			return null;

		if (!class_exists($classname))
			return null;

		$classname = $classname;
		$obj = new $classname;

		return $obj;
	}

	protected function get_controller_name()
	{
		return explode('@', Route::getCurrentRoute()->getActionName())[0];
	}

	protected function get_controller_obj()
	{
		$classname = $this->get_controller_name();

		if (!$classname)
			return null;

		if (!class_exists($classname))
			return null;

		$obj = new $classname;
		return $obj;
	}

	protected function get_view_name()
	{
		return explode ('\\', $this->get_model_name())[0];
	}

	protected function get_view_var()
	{
		return strtolower($this->get_view_name());
	}

	protected function get_route_name()
	{
		return explode('\\', $this->get_model_name())[0];
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

			if (method_exists($mod_controller, 'get_form_fields'))
        		$links[($module->get('description') == '') ? $tmp : $module->get('description')] = $tmp;
        }

        return $links;
	}


	/**
	 * Returns a default input data array, that shall be overwritten
	 * from the appropriate model controller if needed.
	 *
	 * Note: Will be running before Validation
	 *
	 * Tasks: Checkbox Entries will automatically set to 0 if not checked
	 */
	protected function prepare_input($data)
	{
		foreach ($this->get_form_fields($this->get_model_obj()) as $field)
		{
			// skip file upload fields
			if ($field['form_type'] == 'file')
				continue;

			// Checkbox Unset ?
			if(!isset($data[$field['name']]) && ($field['form_type'] == 'checkbox'))
				$data[$field['name']] = 0;

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
	protected function prep_rules($rules, $data)
	{
		return $rules;
	}


	/**
	 * Prepare Breadcrumb - $panel_right header
	 * Priority Handling: get_form_breadcrumb(), view_has_many()
	 *
	 * @param view_var: the view_var parameter from edit() context
	 * @return panel_right prepared array for default.blade
	 */
	protected function prepare_breadcrumb($view_var)
	{
		// get_form_breadcrumb()
		$ret = $this->get_form_breadcrumb($view_var);

		if ($ret)
			return $ret;

		// view_has_many()
		if (\Acme\php\ArrayHelper::array_depth($view_var->view_has_many()) >= 2)
		{
			// get actual blade to $b
			$a = $view_var->view_has_many();
			$b = current($a);
			$c = [];

			for ($i = 0; $i < sizeof($view_var->view_has_many()); $i++)
			{
				array_push($c, ['name' => key($a), 'route' => $this->get_route_name().'.edit', 'link' => [$view_var->id, 'blade='.$i]]);
				$b = next($a);
			}

			$ret = ($c);
		}

		return $ret;
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

		$model = $this->get_model_obj();
		if (!$model)
			$model = new BaseModel;

		if(!isset($a['networks']))
		{
			$a['networks'] = [];
			if ($model->module_is_active('HfcBase'))
				$a['networks'] = \Modules\HfcBase\Entities\Tree::get_all_net();
		}

		if(!isset($a['view_header_links']))
			$a['view_header_links'] = BaseViewController::get_view_header_links();


		if(!isset($a['route_name']))
			$a['route_name'] = $this->get_route_name();

		if(!isset($a['model_name']))
			$a['model_name'] = $this->get_model_name();

		if(!isset($a['view_header']))
			$a['view_header'] = $model->get_view_header();

		if(!isset($a['link_header']))
			$a['link_header'] = '';

		if (!isset($a['form_update']))
			$a['form_update'] = $this->get_route_name().'.update';

		$a['save_button'] = $this->save_button;

		// Get Framework Informations
		$gc = GlobalConfig::first();
		$a['framework']['header1'] = $gc->headline1;
		$a['framework']['header2'] = $gc->headline2;
		$a['framework']['version'] = $gc->version();

		return $a;
	}


	/*
	 * This function is used to prepare get_form_field array for edit view
	 * So all general preparation stuff to get_form_fields will be done here.
	 *
	 * Tasks:
	 *  1. Add a (*) to fields description if validation rule contains required
	 *  2. Add Placeholder YYYY-MM-DD for all date fields
	 *  3. Hide all parent view relation select fields
	 *
	 * @param fields: the get_form_fields array()
	 * @param model: the model to view. Note: could be get_model_obj()->find($id) or get_model_obj()
	 * @return: the modifeyed get_form_fields array()
	 *
	 * @autor: Torsten Schmidt
	 */
	protected function prepare_form_fields($fields, $model)
	{
		$ret = [];

		// get the validation rules for related model object
		$rules = $this->get_model_obj()->rules();

		// for all fields
		foreach ($fields as $field)
		{
			// rule exists for actual field ?
			if (isset ($rules[$field['name']]))
			{
				// Task 1: Add a (*) to fields description if validation rule contains required
				if (preg_match('/(.*?)required(.*?)/', $rules[$field['name']]))
					$field['description'] = $field['description']. ' *';

				// Task 2: Add Placeholder YYYY-MM-DD for all date fields
				if (preg_match('/(.*?)date(.*?)/', $rules[$field['name']]))
					$field['options']['placeholder'] = 'YYYY-MM-DD';

			}

			// 3. Hide all parent view relation select fields
			if (is_object($model->view_belongs_to()) && 					// does a view relation exists
				$model->view_belongs_to()->table.'_id' == $field['name'])	// view table name (+_id) == field name ?
				$field['hidden'] = 1;									// hide

			$field['field_value'] = $model[$field['name']];

			array_push ($ret, $field);
		}

		// dd($ret);

		return $ret;
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
			$obj       = $this->get_model_obj();
			$view_path = 'Generic.index';

			if (View::exists($this->get_view_name().'.index'))
				$view_path = $this->get_view_name().'.index';
		}

		$create_allowed = $this->get_controller_obj()->index_create_allowed;

		// perform the search
		foreach ($obj->getFulltextSearchResults($scope, $mode, $query, Input::get('preselect_field'), Input::get('preselect_value')) as $result)
		{
			if(!isset($view_var))
				$view_var = $result->get();
			else
				$view_var = $view_var->merge($result->get());
		}

		return View::make($view_path, $this->compact_prep_view(compact('view_header', 'view_var', 'create_allowed', 'query', 'scope')));
	}


	/**
	 * Display a listing of all objects of the calling model
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$this->_check_permissions("view");
		}
		catch (Exceptions $ex) {
			throw new AuthExceptions($e->getMessage());
		}

		$obj = $this->get_model_obj();

		$view_var = $obj->index_list();

		$link_header  	= BaseViewController::translate($obj->get_view_header().' List');
		$create_allowed = $this->get_controller_obj()->index_create_allowed;
		$view_path = 'Generic.index';


		// TODO: show only entries a user has at view rights on model and net!!
		Log::warning('Showing only index() elements a user can access is not yet implemented');

		if (View::exists($this->get_view_name().'.index'))
			$view_path = $this->get_view_name().'.index';

		return View::make ($view_path, $this->compact_prep_view(compact('link_header', 'view_var', 'create_allowed')));
	}



	/**
	 * Show the form for creating a new model item
	 *
	 * @return View
	 */
	public function create()
	{
		try {
			$this->_check_permissions("create");
		}
		catch (PermissionDeniedError $ex) {
			return View::make('auth.denied', array('error_msg' => $ex->getMessage()));
		}

		$obj = $this->get_model_obj();

		// $view_header 	= 'Create '.$obj->get_view_header();
		$view_header 	= BaseViewController::translate('Create ').BaseViewController::translate($obj->get_view_header());
		// form_fields contain description of fields and the data of the fields
		$form_fields	= BaseViewController::html_form_field ($this->prepare_form_fields ($this->get_controller_obj()->get_form_fields($obj), $obj), 'create');

		// generate Link header - parse parent object from HTML GET array
		// TODO: avoid use of HTML GET array for security considerations
		$view_var = NULL;
		if (isset(array_keys($_GET)[0]))
		{
			$key        = array_keys($_GET)[0];
			$class_name = BaseModel::_guess_model_name(ucwords(explode ('_id', $key)[0]));
			$class      = new $class_name;
			$view_var   = $class->find($_GET[$key]);
		}
		$link_header = BaseViewController::prep_link_header($this->get_route_name(), $view_header, $view_var);


		$view_path = 'Generic.create';
		$form_path = 'Generic.form';

		// proof if there is a special view for the calling model
		if (View::exists($this->get_view_name().'.create'))
			$view_path = $this->get_view_name().'.create';
		if (View::exists($this->get_view_name().'.form'))
			$form_path = $this->get_view_name().'.form';


		return View::make($view_path, $this->compact_prep_view(compact('view_header', 'form_fields', 'form_path', 'link_header')));
	}


	/**
	 * Generic store function - stores an object of the calling model
	 * @param redirect: if set to false returns id of the new created object (default: true)
	 * @return: html redirection to edit page (or if param $redirect is false the new added object id)
	 */
	protected function store($redirect = true)
	{
		try {
			$this->_check_permissions("create");
		}
		catch (PermissionDeniedError $ex) {
			return View::make('auth.denied', array('error_msg' => $ex->getMessage()));
		}

		// dd(Input::all());
		$obj = $this->get_model_obj();
		$controller = $this->get_controller_obj();

		// Prepare and Validate Input
		$data 		= $controller->prepare_input(Input::all());
		$rules 		= $controller->prep_rules($obj::rules(), $data);
		$validator  = Validator::make($data, $rules);
		$data 		= $controller->prepare_input_post_validation ($data);

		if ($validator->fails())
		{
			Log::info ('Validation Rule Error: '.$validator->errors());
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = $obj::create($data)->id;

		if (!$redirect)
			return $id;

		return Redirect::route($this->get_route_name().'.edit', $id)->with('message', 'Created!');
	}


	/*
	 * This function is used to prepare get_form_field array for edit view
	 * So all general preparation stuff to get_form_fields will be done here.
	 *
	 * Tasks:
	 *  1. Add a (*) to fields description if validation rule contains required
	 *  2. Add Placeholder YYYY-MM-DD for all date fields
	 *  3. Hide all parent view relation select fields
	 *
	 * @param fields: the get_form_fields array()
	 * @param model: the model to view. Note: could be get_model_obj()->find($id) or get_model_obj()
	 * @return: the modifeyed get_form_fields array()
	 *
	 * @autor: Torsten Schmidt
	 */
	protected function _prepare_form_fields($fields, $model)
	{
		$ret = [];

		// get the validation rules for related model object
		$rules = $this->get_model_obj()->rules();

		// for all fields
		foreach ($fields as $field)
		{
			// rule exists for actual field ?
			if (isset ($rules[$field['name']]))
			{
				// Task 1: Add a (*) to fields description if validation rule contains required
				if (preg_match('/(.*?)required(?!_)(.*?)/', $rules[$field['name']]))
					$field['description'] = $field['description']. ' *';

				// Task 2: Add Placeholder YYYY-MM-DD for all date fields
				if (preg_match('/(.*?)date(.*?)/', $rules[$field['name']]))
					$field['options']['placeholder'] = 'YYYY-MM-DD';

			}

			// 3. Hide all parent view relation select fields
			if (is_object($model->view_belongs_to()) && 					// does a view relation exists
				$model->view_belongs_to()->table.'_id' == $field['name'])	// view table name (+_id) == field name ?
				$field['hidden'] = '1';									// hide

			array_push ($ret, $field);
		}

		return $ret;
	}


	/**
	 * Add extra data for right box.
	 * This e.g. is needed to add Envia API urls but can also be used for other topics – simply overwrite this placeholder and return array with extra information instead of null…
	 *
	 * @author Patrick Reichel
	 *
	 * @return array of arrays containing extra information
	 */
	protected function _get_extra_data($view_var) {
		return null;
	}


	/**
	 * Show the editing form of the calling Object
	 *
	 * @param  int  $id
	 * @return View
	 */
	public function edit($id)
	{
		try {
			// this needs view rights; edit rights are checked in store/update methods!
			$this->_check_permissions("view");
		}
		catch (PermissionDeniedError $ex) {
			return View::make('auth.denied', array('error_msg' => $ex->getMessage()));
		}

		$obj = $this->get_model_obj();
		//${$this->get_view_var()} = $obj->findOrFail($id);

		// transfer model_name, view_header, view_var
		$view_header 	= BaseViewController::translate('Edit ').BaseViewController::translate($obj->get_view_header());
		$view_var 		= $obj->findOrFail($id);
		$form_fields	= BaseViewController::html_form_field ($this->prepare_form_fields ($this->get_controller_obj()->get_form_fields($view_var), $view_var), 'edit');
		$link_header    = BaseViewController::prep_link_header($this->get_route_name(), $view_header, $view_var);
		$panel_right    = $this->prepare_breadcrumb($view_var);
		$relations      = BaseViewController::prep_right_panels($view_var);

		// TODO: replace
		$extra_data = $this->_get_extra_data($view_var);

		$view_path = 'Generic.edit';
		$form_path = 'Generic.form';

		// proof if there are special views for the calling model
		if (View::exists($this->get_view_name().'.edit'))
			$view_path = $this->get_view_name().'.edit';
		if (View::exists($this->get_view_name().'.form'))
			$form_path = $this->get_view_name().'.form';


		$config_routes = BaseController::get_config_modules();

		return View::make ($view_path, $this->compact_prep_view(compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields', 'config_routes', 'link_header', 'panel_right', 'relations', 'extra_data')));
	}


	/**
	 * Update the specified data in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		try {
			$this->_check_permissions("edit");
		}
		catch (PermissionDeniedError $ex) {
			return View::make('auth.denied', array('error_msg' => $ex->getMessage()));
		}

		// dd(Input::all());

		$obj = $this->get_model_obj()->findOrFail($id);
		$controller = $this->get_controller_obj();

		// Prepare and Validate Input
		$data      = $controller->prepare_input(Input::all());
		$rules = $controller->prep_rules($obj::rules($id), $data);
		$validator = Validator::make($data, $rules);
		$data      = $controller->prepare_input_post_validation ($data);

		if ($validator->fails())
		{
			Log::info ('Validation Rule Error: '.$validator->errors());
			return Redirect::back()->withErrors($validator)->withInput();
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


		return Redirect::route($this->get_route_name().'.edit', $id)->with('message', 'Updated!');
	}



	/**
	 * Removes a specified model object from storage
	 *
	 * @param  int  $id: bulk delete if == 0
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
			$this->_check_permissions("delete");
		}
		catch (PermissionDeniedError $ex) {
			return View::make('auth.denied', array('error_msg' => $ex->getMessage()));
		}

		if ($id == 0)
		{
			// bulk delete
			foreach (Input::all()['ids'] as $id => $val)
				$this->get_model_obj()->findOrFail($id)->delete();
		}
		else
			$this->get_model_obj()->findOrFail($id)->delete();

		return Redirect::back();
	}


// Deprecated:

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
}
