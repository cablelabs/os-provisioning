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

	protected function routeContains ($r = '/', $s = 'Das Monster')
	{
		$crawler = $this->client->request('GET', $r);

		$this->assertTrue($this->client->getResponse()->isOk());

		$this->assertGreaterThan(0, count($crawler->filter('body:contains("'.$s.'")')), "Expected to see $s within the  view");
	}

}
