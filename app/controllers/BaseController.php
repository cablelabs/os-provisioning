<?php

require_once(app_path().'/Exceptions.php');

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
			throw new NoAuthenticateduserError("No user logged in");
		}

		// build permissions array for easy access to user rights
		if (is_null($this->permissions)) {
			$this->_get_permissions();
		}

		// check model rights
		if (!array_key_exists($cur_model, $this->permissions['model'])) {
			throw new NoModelPermissionError('Access to model '.$cur_model.' not allowed.');
		}
		if (!array_key_exists($access, $this->permissions['model'][$cur_model])) {
			throw new InvalidPermissionsRequest('Something went wrong asking for '.$access.' right in '.$model.'.');
		}
		if ($this->permissions['model'][$cur_model][$access] == 0) {
			throw new InsufficientRightsError('You are not allowed to '.$access.' in '.$cur_model.'.');
		}

		// TODO: check net rights
	}

	/**
	 * Returns a default input data array, that shall be overwritten from the appropriate model controller if needed
	 */
	protected function default_input($data)
	{
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
		// TODO: generic replace ..
		return 'Models\\'.explode ('Controller', Route::getCurrentRoute()->getActionName())[0];
	}

	protected function get_model_obj ()
	{
		$classname = $this->get_model_name();

		if (!$classname)
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

		$obj = new $classname;
		return $obj;
	}

	protected function get_view_name()
	{
		return explode ('\\', $this->get_model_name())[1];
	}

	protected function get_view_var()
	{
		return strtolower($this->get_view_name());
	}

	protected function get_route_name()
	{
		return explode('\\', $this->get_model_name())[1];
	}


	public function get_view_header_links ()
	{
		$ret = array();
		$modules = Module::enabled();

		foreach ($modules as $module)
		{
			foreach (Config::get($module->getName().'::header') as $line)
				array_push($ret, $line);
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
	 * Perform a fulltext search.
	 *
	 * @author Patrick Reichel
	 */
	public function fulltextSearch() {

		$obj = $this->get_model_obj();

		$model_name 	= $this->get_model_name();
		$view_header  	= $obj->get_view_header();
		$route_name     = $this->get_route_name();
		$view_header_links = $this->get_view_header_links();

		$create_allowed = $this->get_controller_obj()->index_create_allowed;

		$view_path = 'Generic.index';

		if (View::exists($this->get_view_name().'.index'))
			$view_path = $this->get_view_name().'.index';


		// get the search scope
		$scope = Input::get('scope');

		// get the mode to use and transform to sql syntax
		$mode = Input::get('mode');

		// get the query to search for
		$query = Input::get('query');

		$view_var = $obj->getFulltextSearchResults($scope, $mode, $query);

		return View::make($view_path, compact('model_name', 'view_header', 'view_var', 'create_allowed', 'query', 'scope', 'route_name', 'view_header_links'));

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
		catch (PermissionDeniedError $ex) {
			return View::make('auth.denied', array('error_msg' => $ex->getMessage()));
		}

		$obj = $this->get_model_obj();

		$model_name 	= $this->get_model_name();
		$view_var   	= $obj->all();
		$view_header  	= $obj->get_view_header().' List';
		$route_name 	= $this->get_route_name();

		$create_allowed = $this->get_controller_obj()->index_create_allowed;

		$view_path = 'Generic.index';
		$view_header_links = $this->get_view_header_links();

		// TODO: show only entries a user has at view rights on model and net!!
		Log::warning('Showing only index() elements a user can access is not yet implemented');

		if (View::exists($this->get_view_name().'.index'))
			$view_path = $this->get_view_name().'.index';

		return View::make($view_path, compact('model_name', 'view_header', 'view_var', 'create_allowed', 'route_name', 'view_header_links'));
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

		$model_name 	= $this->get_model_name();
		$route_name 	= $this->get_route_name();
		$view_header 	= 'Create '.$obj->get_view_header();
		// form_fields contain description of fields and the data of the fields
		$form_fields	= $this->get_controller_obj()->get_form_fields($obj);

		$view_path = 'Generic.create';
		$form_path = 'Generic.form';
		$view_header_links = $this->get_view_header_links();

		// proof if there is a special view for the calling model
		if (View::exists($this->get_view_name().'.create'))
			$view_path = $this->get_view_name().'.create';
		if (View::exists($this->get_view_name().'.form'))
			$form_path = $this->get_view_name().'.form';

		return View::make($view_path, compact('model_name', 'view_header', 'form_fields', 'form_path', 'route_name', 'view_header_links'));
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
		$model_name 	= $this->get_model_name();
		$route_name 	= $this->get_route_name();
		$view_header 	= 'Edit '.$obj->get_view_header();
		$view_var 		= $obj->findOrFail($id);
		$form_fields	= $this->get_controller_obj()->get_form_fields($view_var);

		$view_path = 'Generic.edit';
		$form_path = 'Generic.form';
		$view_header_links = $this->get_view_header_links();

		// proof if there are special views for the calling model
		if (View::exists($this->get_view_name().'.edit'))
			$view_path = $this->get_view_name().'.edit';
		if (View::exists($this->get_view_name().'.form'))
			$form_path = $this->get_view_name().'.form';

		return View::make($view_path, compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields', 'route_name', 'view_header_links'));
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

		$obj->touch();			// update timestamp, this forces to run all observer's
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
