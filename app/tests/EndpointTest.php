<?php

use Models\Endpoint;


class EndpointTest extends TestCase {

	public function testIndex()
	{
		$this->routeContains ();
		$this->routeContains ('endpoint');
	}

	public function testEdit()
	{
		$m = Endpoint::orderby('id', 'DESC')->first()->id;
		$this->routeContains ("endpoint/$m/edit");
	}

	public function testDelete()
	{
		$m = Endpoint::orderby('id', 'DESC')->first()->id;
		$this->routeContains ("endpoint/$m", 'DELETE');
	}

}
