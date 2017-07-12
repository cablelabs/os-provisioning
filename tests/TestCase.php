<?php


abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
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


        // NOTE: This is tricky shit! AND should be solved different!
        //       See NOTE on ExampleTest.php
        require_once __DIR__.'/../app/Http/routes.php';

        return $app;
    }
}
