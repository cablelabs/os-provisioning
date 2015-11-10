<?php


class BaseModuleController extends BaseController {

	public function get_mvc_path()
	{
		$a = explode('\\', Route::getCurrentRoute()->getActionName());
		return $a[0].'\\'.$a[1];
	}


	protected function get_model_name()
	{
		// quick and dirty :)
		return $this->get_mvc_path().'\\Entities\\'.explode ('Controller', explode ('\\', explode ('@', Route::getCurrentRoute()->getActionName())[0])[4])[0];
	}


	protected function get_controller_name()
	{
		return explode('@', Route::getCurrentRoute()->getActionName())[0];
	}


	protected function get_view_name()
	{
		return explode ('\\', $this->get_model_name())[3];
	}


	protected function get_route_name()
	{
		return explode('\\', $this->get_model_name())[3];
	}

}
