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
		/*
		$crawler = $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());

		$cm  = Modem::first();

		$cf = Configfile::find(22);
		echo $cf->text_make($cm);
		*/

		$cm = Modem::first();
		$q  = $cm->quality;
		print_r($q);
	}

}
