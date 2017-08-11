<?php namespace Modules\Ticket\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class TicketController extends Controller {
	
	public function index()
	{
		return view('ticket::index');
	}
	
}