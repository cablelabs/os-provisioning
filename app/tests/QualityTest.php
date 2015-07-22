<?php

use Models\Quality;


class qualityTest extends TestCase {

	public function testIndex()
	{
		$this->routeContains ();
		$this->routeContains ('quality');
	}

	public function testEdit()
	{
		$m = Quality::orderby('id', 'DESC')->first()->id;
		$this->routeContains ("quality/$m/edit");
	}

	public function testDelete()
	{
		$m = Quality::orderby('id', 'DESC')->first()->id;
		$this->routeContains ("quality/$m", 'DELETE');
	}

}
