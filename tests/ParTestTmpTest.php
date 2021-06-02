<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
