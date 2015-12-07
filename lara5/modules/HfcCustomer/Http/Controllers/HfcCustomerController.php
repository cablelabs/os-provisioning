<?php namespace Modules\Hfccustomer\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class HfcCustomerController extends Controller {

	public function index()
	{
		return View::make('hfccustomer::index');
	}
	
}