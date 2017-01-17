<?php namespace Modules\Mail\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class MailController extends Controller {
	
	public function index()
	{
		return view('mail::index');
	}
	
}
