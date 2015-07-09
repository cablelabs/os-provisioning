<?php

use Models\Configfile;
use Models\Modem;
use Models\Endpoint;

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		$crawler = $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());

		$cm  = Modem::first();

		$cf = Configfile::find(22);
		echo $cf->text_make($cm);
	}

}
