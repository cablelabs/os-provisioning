<?php

use Models\Modem;


class ModemTest extends TestCase {

	public function testIndex()
	{
		$this->routeContains ();
		$this->routeContains ('modem');
	}

	public function testEdit()
	{
		$m = Modem::orderby('id', 'DESC')->first()->id;
		$this->routeContains ("modem/$m/edit");
	}

	public function testDelete()
	{
		$m = Modem::orderby('id', 'DESC')->first()->id;
		$this->routeContains ("modem/$m", 'DELETE');
	}

}
