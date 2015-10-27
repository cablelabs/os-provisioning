<?php


class BaseController extends Controller {

	protected $output_format;


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
		return explode ('Controller', Route::getCurrentRoute()->getActionName())[0];
	}

	protected function get_model_obj ()
	{
		$classname = $this->get_model_name();

		if (!$classname)
			return null;

		$classname = 'Models\\'.$classname;
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
		return $this->get_model_name(); 
	}

	protected function get_view_var()
	{
		return strtolower($this->get_view_name());
	}


	/**
	 * Display a listing of all objects of the calling model
	 *
	 * @return Response
	 */
	public function index()
	{
		$obj = $this->get_model_obj();
		
		$model_name 	= $this->get_model_name();
		$view_var   	= $obj->all();
		$view_header  	= $obj->get_view_header();

		// proof if the calling Model has a special index view - if not call the generic view
		if (View::exists($this->get_view_name().'.index'))
			return View::make($this->get_view_name().'.index', compact('model_name', 'view_header', 'view_var'));

		return View::make('Generic.index', compact('model_name', 'view_header', 'view_var'));			
	}



	/**
	 * Show the form for creating a new model item
	 *
	 * @return Response
	 */
	public function create()
	{
		$obj = $this->get_model_obj();

		$model_name 	= $this->get_model_name();
		$view_header 	= $obj->get_view_header();
		// form_fields contain description of fields and the data of the fields
		$form_fields	= $this->get_controller_obj()->get_form_fields();

		$view_path = 'Generic.create';
		$form_path = 'Generic.form';

		// proof if there is a special view for the calling model
		if (View::exists($this->get_view_name().'.create'))
			$view_path = $this->get_view_name().'.create';
		if (View::exists($this->get_view_name().'.form'))
			$form_path = $this->get_view_name().'.form';

		return View::make($view_path, compact('model_name', 'view_header', 'form_fields', 'form_path'));
	}


	/**
	 * Generic store function - stores an object of the calling model
	 * @param $name 	Name of Object
	 * @return $ret 	list
	 */
	protected function store()
	{
		$obj = $this->get_model_obj();
		$controller = $this->get_controller_obj();

		$validator = Validator::make($data = $controller->default_input(Input::all()), $obj::rules());

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = $obj::create($data)->id;

		return Redirect::route($this->get_view_name().'.edit', $id)->with('message', 'Created!');
	}


	/**
	 * Show the editing form of the calling Object
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$obj = $this->get_model_obj();
		//${$this->get_view_var()} = $obj->findOrFail($id);

		// transfer model_name, view_header, view_var
		$model_name 	= $this->get_model_name();
		$view_header 	= $obj->get_view_header();
		$view_var 		= $obj->findOrFail($id);
		$form_fields	= $this->get_controller_obj()->get_form_fields();

		$view_path = 'Generic.edit';
		$form_path = 'Generic.form';

		// proof if there are special views for the calling model
		if (View::exists($this->get_view_name().'.edit'))
			$view_path = $this->get_view_name().'.edit';
		if (View::exists($this->get_view_name().'.form'))
			$form_path = $this->get_view_name().'.form';
			
		return View::make($view_path, compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields'));
	}


	/**
	 * Update the specified data in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$obj = $this->get_model_obj()->findOrFail($id);
		$controller = $this->get_controller_obj();

		$validator = Validator::make($data = $controller->default_input(Input::all()), $obj::rules($id));

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$obj->update($data);

		return Redirect::route($this->get_view_name().'.edit', $id)->with('message', 'Updated!');
	}



	/**
	 * Removes a specified model object from storage
	 *
	 * @param  int  $id: bulk delete if == 0
	 * @return Response
	 */
	public function destroy($id)
	{
		if ($id == 0)
		{
			// bulk delete
			foreach (Input::all()['ids'] as $id => $val)
				$this->get_model_obj()->destroy($id);
		}
		else
			$this->get_model_obj()->destroy($id);

		return $this->index();
	}
	
}
