<?php


class BaseController extends Controller {

	protected $output_format; 

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}


	/**
	 *  Testing Parent Functions, call with parent::update() 
	 *  in sub classes ..
	 */
	protected function update($id)
	{
		exec ("logger \"call to update base controller\"");
	}


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
	 * Generic function to build a list with key of id
	 * @param $array 	
	 * @return $ret 	list
	 */
	protected function html_list ($array, $column)
	{
		$ret = array();

		foreach ($array as $a)
		{
			$ret[$a->id] = $a->{$column};	
		}

		return $ret;
	}

	/**
	 * Generic store function an Object with name $name
	 * @param $name 	Name of Object
	 * @return $ret 	list
	 */
	// TODO: look for $name if it works as object template
	// protected function store($name)
	// {
	// 	$validator = Validator::make($data = $this->default_input(Input::all()), $name::rules());

	// 	if ($validator->fails())
	// 	{
	// 		return Redirect::back()->withErrors($validator)->withInput();
	// 	}

	// 	$id = $name::create($data)->id;

	// 	return Redirect::route($name.'.edit', $id);
	// }	


	protected function get_model_name()
	{
		// TODO: generic replace ..
		return explode ('Controller', Route::getCurrentRoute()->getActionName())[0];

	}

	protected function get_model ()
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

	protected function get_controller()
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
	 * Show the form for editing the specified mta.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		${$this->get_view_var()} = $this->get_model()->findOrFail($id);

		return View::make($this->get_view_name().'.edit', compact($this->get_view_var()))->with($this->get_controller()->html_list_array()); 
	}


	/**
	 * Update the specified data in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$obj = $this->get_model()->findOrFail($id);

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
				$this->get_model()->destroy($id);
		}
		else
			$this->get_model()->destroy($id);

		return $this->index();
	}
	
}
