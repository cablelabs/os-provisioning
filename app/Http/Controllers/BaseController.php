<?php

namespace App\Http\Controllers;

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

use App\Exceptions\AuthExceptions;


class BaseController extends Controller {

	protected $output_format;

	protected $index_create_allowed = true;

	protected $permissions = null;
	protected $permission_cores = array('model', 'net');


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
	 *
	 * @throws NoAuthenticatedUserError if no user is logged in
	 * @throws NoModelPermissionError if user is not allowed to acces the model
	 * @throws InvalidPermissionsRequest if permission request is invalid
	 * @throws InsufficientRightsError if user has not the specific right needed to perform an action
	 */
	protected function _check_permissions($access) {

		// get the currently authenticated user
		$cur_user = Auth::user();

		$cur_model = $this->get_model_name();

		// no user logged in
		if (is_null($cur_user)) {
			throw new AuthExceptions('Login Required');
		}

		// build permissions array for easy access to user rights
		if (is_null($this->permissions)) {
			$this->_get_permissions();
		}

		// check model rights
		if (!array_key_exists($cur_model, $this->permissions['model'])) {
			throw new AuthExceptions('Access to model '.$cur_model.' not allowed for user '.$cur_user->login_name.'.');
		}
		if (!array_key_exists($access, $this->permissions['model'][$cur_model])) {
			throw new AuthExceptions('Something went wrong asking for '.$access.' right in '.$model.' for user '.$cur_user->login_name.'.');
		}
		if ($this->permissions['model'][$cur_model][$access] == 0) {
			throw new AuthExceptions('User '.$cur_user->login_name.' is not allowed to '.$access.' in '.$cur_model.'.');
		}

		// TODO: check net rights
	}

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
	public function __construct() {
	}

	/**
	 * Returns a default input data array, that shall be overwritten from the appropriate model controller if needed
	 *
	 * Note: Checkbox Entries will automatically set to 0 if not checked
	 */
	protected function default_input($data)
	{
		// Checkbox Unset ?
		foreach ($this->get_form_fields($this->get_model_obj()) as $field) 
		{
			if(!isset($data[$field['name']]) && $field['form_type'] == 'checkbox')
				$data[$field['name']] = 0;
		}

		return $data;
	}


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


	public function get_view_header_links ()
	{
		$ret = array();
		$modules = Module::enabled();

		foreach ($modules as $module)
		{
			if (File::exists($module->getPath().'/Config/header.php'))
			{
				/*
				 * TODO: use Config::get() 
				 *       this needs to fix namespace problems first
				 */
				$lines = include ($module->getPath().'/Config/header.php');
				foreach ($lines as $line)
					array_push($ret, $line);
			}
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
			$a['view_header_links'] = $this->get_view_header_links();

		if(!isset($a['route_name']))
			$a['route_name'] = $this->get_route_name();

		if(!isset($a['model_name']))
			$a['model_name'] = $this->get_model_name();

		if(!isset($a['view_header']))
			$a['view_header'] = $model->get_view_header();

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

		$view_var   	= $obj->all();
		$view_header  	= $obj->get_view_header().' List';

		$create_allowed = $this->get_controller_obj()->index_create_allowed;

		$view_path = 'Generic.index';

		// TODO: show only entries a user has at view rights on model and net!!
		Log::warning('Showing only index() elements a user can access is not yet implemented');

		if (View::exists($this->get_view_name().'.index'))
			$view_path = $this->get_view_name().'.index';

		return View::make($view_path, $this->compact_prep_view(compact('view_header', 'view_var', 'create_allowed')));
	}




	/**
	 * Show the form for creating a new model item
	 *
	 * @return Response
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

		$view_header 	= 'Create '.$obj->get_view_header();
		// form_fields contain description of fields and the data of the fields
		$form_fields	= $this->get_controller_obj()->get_form_fields($obj);

		$view_path = 'Generic.create';
		$form_path = 'Generic.form';

		// proof if there is a special view for the calling model
		if (View::exists($this->get_view_name().'.create'))
			$view_path = $this->get_view_name().'.create';
		if (View::exists($this->get_view_name().'.form'))
			$form_path = $this->get_view_name().'.form';

		return View::make($view_path, $this->compact_prep_view(compact('view_header', 'form_fields', 'form_path')));
	}


	/**
	 * Generic store function - stores an object of the calling model
	 * @param $name 	Name of Object
	 * @return $ret 	list
	 */
	protected function store()
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

		$validator = Validator::make($data = $controller->default_input(Input::all()), $obj::rules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = $obj::create($data)->id;

		return Redirect::route($this->get_route_name().'.edit', $id)->with('message', 'Created!');
	}


	/**
	 * Show the editing form of the calling Object
	 *
	 * @param  int  $id
	 * @return Response
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
		$view_header 	= 'Edit '.$obj->get_view_header();
		$view_var 		= $obj->findOrFail($id);
		$form_fields	= $this->get_controller_obj()->get_form_fields($view_var);

		$view_path = 'Generic.edit';
		$form_path = 'Generic.form';

		// proof if there are special views for the calling model
		if (View::exists($this->get_view_name().'.edit'))
			$view_path = $this->get_view_name().'.edit';
		if (View::exists($this->get_view_name().'.form'))
			$form_path = $this->get_view_name().'.form';

		return View::make($view_path, $this->compact_prep_view(compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields')));
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

		$validator = Validator::make($data = $controller->default_input(Input::all()), $obj::rules($id));

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		// update timestamp, this forces to run all observer's
		// Note: calling touch() forces a direct save() which calls all observers before we update $data
		//       when exit in middleware to a new view page (like Modem restart) this kill update process
		$data['updated_at'] = \Carbon\Carbon::now(Config::get('app.timezone'));

		// the Update
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

}
