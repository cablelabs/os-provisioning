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
}
