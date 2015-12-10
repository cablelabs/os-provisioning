<?php namespace Modules\Provvoip\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class ProvVoipController extends Controller {

	public function index()
	{
		return View::make('provvoip::index');
	}
	
}