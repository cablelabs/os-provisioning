<?php

class ParTest extends TestCase {

	/**
	 * a test that never fails ⇒ verify that unit testing is up and running
	 *
	 * @return void
	 */
	public function testUnittestsRunning()
	{
		// check if running
		$this->assertEquals(1, 1);
	}

}
