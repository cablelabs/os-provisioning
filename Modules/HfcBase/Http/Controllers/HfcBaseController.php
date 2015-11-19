<?php namespace Modules\Hfcbase\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class HfcBaseController extends Controller {

	public function index()
	{
		return View::make('hfcbase::index');
	}
	
}