<?php

namespace Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;

class ParTestTmpTest extends Illuminate\Foundation\Testing\TestCase
{
    /* use WithoutMiddleware; */

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        /* global $app; */

        /* $app['env'] = 'testing'; */

        /* return $app; */

        /* $app = require __DIR__.'/../bootstrap/app.php'; */

        /* $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); */

        /* // NOTE: This is tricky shit! AND should be solved different! */
        /* //       See NOTE on ExampleTest.php */
        /* require_once __DIR__.'/../app/Http/routes.php'; */

        return $app;
    }

    public function testBoo()
    {
        $this->withoutMiddleware();

        /* foreach (Route::getRoutes() as $route) { */
        /* 	echo $route->getName()."\n"; */
        /* 	$this->assertTrue(1===1, 'boo test failed'); */
        /* } */
    }
}
