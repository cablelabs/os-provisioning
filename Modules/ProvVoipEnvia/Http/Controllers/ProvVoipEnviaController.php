<?php namespace Modules\Provvoipenvia\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class ProvVoipEnviaController extends Controller {

	public function index()
	{
		return View::make('provvoipenvia::index');
	}
	
}