<?php

use Models\Configfile;
use Models\Modem;
use Models\Endpoint;
use Models\Qualitiy;

class ExampleTest extends TestCase {




	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		$this->routeContains ();
		
		$this->routeContains ('modem');

		$this->routeContains ('endpoint');

		$this->routeContains ('configfile');

		$this->routeContains ('quality');
	}



}
