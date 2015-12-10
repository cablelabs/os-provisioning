<?php 

namespace Modules\ProvBase\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class ProvBaseModuleController extends Controller {

	public function index()
	{
		return View::make('provbase::index');
	}
	
}