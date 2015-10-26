<?php


class BaseController extends Controller {

	protected $output_format; 


	/**
	 *  json abstraction layer
	 *  
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
	 *  Maybe a generic redirect is a option, but
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
		${$this->get_view_var().'s'} = $this->get_model_obj()->all();

		return View::make($this->get_view_name().'.index', compact($this->get_view_var().'s'));
	}



	/**
	 * Show the form for creating a new model item
	 *
	 * @return Response
	 */
	public function create()
	{
		$obj = $this->get_model_obj();

		// proof if we need to transfer data to the view for this model
		if (method_exists($obj, 'html_list_array'))
			return View::make($this->get_view_name().'.create')->with($obj->html_list_array());
		else
			return View::make($this->get_view_name().'.create');
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

		// proof if Model has/needs a default_input($data) function
		if (method_exists($controller, 'default_input'))
			$validator = Validator::make($data = $controller->default_input(Input::all()), $obj::rules());
		else
			$validator = Validator::make($data = Input::all(), $obj::rules());


		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$id = $obj::create($data)->id;

		return Redirect::route($this->get_view_name().'.edit', $id);
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
		${$this->get_view_var()} = $obj->findOrFail($id);

		// proof if we need to transfer data to the view for this model
		if (method_exists($obj, 'html_list_array'))
			return View::make($this->get_view_name().'.edit', compact($this->get_view_var()))->with($obj->html_list_array());
		else
			return View::make($this->get_view_name().'.edit', compact($this->get_view_var()));
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

		// proof if Model has/needs a default_input($data) function
		if (method_exists($controller, 'default_input'))
			$validator = Validator::make($data = $controller->default_input(Input::all()), $obj::rules($id));
		else
			$validator = Validator::make($data = Input::all(), $obj::rules($id));


		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$obj->update($data);

		return Redirect::route($this->get_view_name().'.index');
	}



	/**
	 * Remove the specified modem from storage.
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
