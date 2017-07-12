<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * Test all MVCs
     *
     * @return void
     */
    public function testGenericMvc()
	{
		// TODO: do not hard code any user class, instead fetch a user dynamically
		//       ore add it only for testing (see Laravel factory stuff)
		$user = App\Authuser::findOrFail(1);

		// TODO: there must be a namespace problem in Testing Context!
		//       All/Many normal non pingpong classes are not found, like routing, Controllers, ...
		//       To fix this issue for routing, i have added the basic route file to TestCase.php
		//       This should be solved better ..
		//       When solving this problem, the following ignore test case array can shrink
		$ignore = [
			'Authuser.index',
			'Authrole.index',
			'Config.index',
			'GlobalConfig.index',
			'GuiLog.index',
			'PhonenumberManagement.index',	// not a real MVC
			'ProvMon.index',
			'ProvVoipEnvia.index',	// not a real MVC
		];

		// Fetch all routes
		foreach (Route::getRoutes() as $route)
		{
			// Filter only '*.index' routes and ignore $ignores array
			if ((strpos($route->getName(), '.index') !== false) &&
				!(in_array($route->getName(), $ignore)))
			{
				// Info Message
				$msg = "Testing of ".$route->getName().' '.$route->getPath();
				echo "\n$msg";
				\Log::debug($msg);

				// Index Page
				$this->actingAs($user)->visit($route->getPath());

				// TODO: edit, delete, create page
				// NOTE: it seems to be quit tricky to fetch the object / model from
				//       this context. This is required to make next tests, like jumping
				//       to admin/Contract/{id}/edit. We need to find the first valid id..
			}
		}

    }
}
