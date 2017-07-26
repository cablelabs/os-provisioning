<?php


abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
	 * The base URL to use while testing the application.
	 * Strange: This needs to be empty string; otherwise we get 404 in our tests
	 * The correct base URL has to be added to phpunit.xml
     *
     * @var string
     */
	protected $baseUrl = '';



    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
