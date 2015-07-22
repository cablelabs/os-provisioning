<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {


	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

	protected function routeContains ($r = '/', $req = 'GET', $s = 'Das Monster')
	{
		echo "\nTest route $req on $r";

		$crawler = $this->client->request($req, $r);

		$this->assertTrue($this->client->getResponse()->isOk());

		$this->assertGreaterThan(0, count($crawler->filter('body:contains("'.$s.'")')), "Expected to see $s within the  view");
	}

}
