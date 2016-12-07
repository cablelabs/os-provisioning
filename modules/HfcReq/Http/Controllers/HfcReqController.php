<?php namespace Modules\Hfcreq\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class HfcReqController extends Controller {
	
	public function index()
	{
		return view('hfcreq::index');
	}
	
}