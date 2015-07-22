<?php

use Models\Configfile;


class ConfigfileTest extends TestCase {

	public function testIndex()
	{
		$this->routeContains ();
		$this->routeContains ('configfile');
	}

	public function testEdit()
	{
		$m = Configfile::orderby('id', 'DESC')->first()->id;
		$this->routeContains ("configfile/$m/edit");
	}

	public function testDelete()
	{
		$m = Configfile::orderby('id', 'DESC')->first()->id;
		$this->routeContains ("configfile/$m", 'DELETE');
	}

}
