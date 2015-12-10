<?php

use Models\Mta;


class MtaTest extends TestCase {

	public function testIndex()
	{
		$this->routeContains ();
		$this->routeContains ('mta');
	}

	public function testEdit()
	{
		$m = Mta::first()->id;
		$this->routeContains ("mta/$m/edit");
	}

	public function testCreate()
	{
		$this->routeContains ("mta/create");
	}

	public function testDelete()
	{
		$m = Mta::orderby('id', 'DESC')->first()->id;
		$this->routeContains ("mta/$m", 'DELETE');
	}
}

