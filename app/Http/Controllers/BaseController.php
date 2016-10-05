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


/*
 * BaseController: The Basic Controller in our MVC design.
 */
class BaseController extends Controller {

	/*
	 * Default VIEW styling options
	 * NOTE: All these values could be used in the inheritances classes
	 */
	protected $save_button = 'Save';
	protected $relation_create_button = 'Create';
	protected $index_create_allowed = true;
	protected $index_delete_allowed = true;
	protected $edit_left_md_size = 4;
	protected $edit_view_save_button = true;



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
		return null;
	}


	protected static function get_model_obj ()
	{
		$classname = \NamespaceController::get_model_name();

		if (!$classname)
			return null;

		if (!class_exists($classname))
			return null;

		$classname = $classname;
		$obj = new $classname;

		return $obj;
	}

	protected static function get_controller_obj()
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
		// get_form_tabs()
		$ret = $this->get_form_tabs($view_var);

		if ($ret)
			return $ret;

		// view_has_many()
		if (BaseViewController::get_view_has_many_api_version($view_var->view_has_many()) == 2)
		{
			// get actual blade to $b
			$a = $view_var->view_has_many();
			$b = current($a);
			$c = [];

			for ($i = 0; $i < sizeof($view_var->view_has_many()); $i++)
			{
				array_push($c, ['name' => key($a), 'route' => \NamespaceController::get_route_name().'.edit', 'link' => [$view_var->id, 'blade='.$i]]);
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

		$model = static::get_model_obj();
		if (!$model)
			$model = new BaseModel;

		if(!isset($a['networks']))
		{
			$a['networks'] = [];
			if (\PPModule::is_active('HfcBase'))
				$a['networks'] = \Modules\HfcBase\Entities\Tree::get_all_net();
		}

		if(!isset($a['view_header_links']))
			$a['view_header_links'] = BaseViewController::view_main_menus();


		if(!isset($a['route_name']))
			$a['route_name'] = \NamespaceController::get_route_name();

		if(!isset($a['model_name']))
			$a['model_name'] = \NamespaceController::get_model_name();

		if(!isset($a['view_header']))
			$a['view_header'] = $model->view_headline();

		if(!isset($a['headline']))
			$a['headline'] = '';

		if (!isset($a['form_update']))
			$a['form_update'] = \NamespaceController::get_route_name().'.update';

		if (!isset($a['edit_left_md_size']))
			$a['edit_left_md_size'] = $this->edit_left_md_size;

		$a['save_button'] = $this->save_button;
		$a['edit_view_save_button'] = $this->edit_view_save_button;

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
	 * @return Response
	 */
	public function index()
	{
		$model = static::get_model_obj();

		$view_var   = $model->index_list();
		$view_header = \App\Http\Controllers\BaseViewController::translate_view('Overview','Header');
		$headline  	= \App\Http\Controllers\BaseViewController::translate_view( $model->view_headline(), 'Header' , 2 );
		$b_text		= $model->view_headline();
		$create_allowed = static::get_controller_obj()->index_create_allowed;
		$delete_allowed = static::get_controller_obj()->index_delete_allowed;

		$view_path = 'Generic.index';
		if (View::exists(\NamespaceController::get_view_name().'.index'))
			$view_path = \NamespaceController::get_view_name().'.index';

		// TODO: show only entries a user has at view rights on model and net!!
		Log::warning('Showing only index() elements a user can access is not yet implemented');

		return View::make ($view_path, $this->compact_prep_view(compact('headline','view_header', 'view_var', 'create_allowed', 'delete_allowed', 'b_text')));
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
		$form_fields = BaseViewController::compute_form_fields (static::get_controller_obj()->view_form_fields($model), $model, 'create');

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

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput()->with('message', 'please correct the following errors')->with('message_color', 'red');
		}

		$id = $obj::create($data)->id;

		if (!$redirect)
			return $id;

		return Redirect::route(\NamespaceController::get_route_name().'.edit', $id)->with('message', 'Created!')->with('message_color', 'blue');
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
		$view_header 	= BaseViewController::translate_view('Edit'.$model->view_headline(),'Header');
		$headline       = BaseViewController::compute_headline(\NamespaceController::get_route_name(), $view_header, $view_var);
		$panel_right    = $this->prepare_tabs($view_var);
		$form_fields	= BaseViewController::compute_form_fields (static::get_controller_obj()->view_form_fields($view_var), $view_var, 'edit');
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

		if ($validator->fails())
		{
			Log::info ('Validation Rule Error: '.$validator->errors());
			return Redirect::back()->withErrors($validator)->withInput()->with('message', 'please correct the following errors')->with('message_color', 'red');
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


		return Redirect::route(\NamespaceController::get_route_name().'.edit', $id)->with('message', 'Updated!')->with('message_color', 'blue');
	}



	/**
	 * Removes a specified model object from storage
	 *
	 * @param  int  $id: bulk delete if == 0
	 * @return Response
	 */
	public function destroy($id)
	{
		// bulk delete
		if ($id == 0)
		{
			// Error Message when no Model is specified - NOTE: delete_message must be an array of the structure below !
			if (!isset(Input::all()['ids']))
				return Redirect::back()->with('delete_message', ['message' => 'No Entry For Deletion specified', 'class' => \NamespaceController::get_route_name(), 'color' => 'red']);

			foreach (Input::all()['ids'] as $id => $val)
				static::get_model_obj()->findOrFail($id)->delete();
		}
		else
			static::get_model_obj()->findOrFail($id)->delete();

		$class = \NamespaceController::get_route_name();
		return Redirect::back()->with('delete_message', ['message' => 'Successful deleted '.$class, 'class' => $class, 'color' => 'blue']);
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
}
