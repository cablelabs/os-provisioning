<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use WithoutMiddleware;


	protected $user = null;
	protected $routes_to_ignore = null;
	protected $routes_to_test = null;
	protected $models = null;


	/**
	 * Creates the application.
	 *
	 * @author Patrick Reichel
	 */
	public function createApplication() {
		$app = parent::createApplication();

		$this->_get_routes_blacklist();
		$this->_get_routes_to_test();
		$this->_get_models();
		$this->_get_user();

		return $app;
	}


	/**
	 * Used to define a blacklist of routes to be excluded from tests.
	 *
	 * @author Patrick Reichel
	 */
	protected function _get_routes_blacklist() {

		$this->routes_to_ignore = [
			'Authuser.index',
			'Authrole.index',
			'Config.index',
			'GlobalConfig.index',
			'GuiLog.index',
			'PhonenumberManagement.index',	// not a real MVC
			'ProvMon.index',
			'ProvVoipEnvia.index',	// not a real MVC
			'Welcome.*',
		];
	}


	/**
	 * Get the routes to be tested.
	 * Makes use of route blacklist.
	 *
	 * @author Patrick Reichel
	 */
	protected function _get_routes_to_test() {

		$routes_to_test = [];
		foreach (Route::getRoutes() as $route) {

			if (in_array($route->getName(), $this->routes_to_ignore)) {
				continue;
			}
			$_ = explode(".", $route->getName());
			array_pop($_);
			$wildcard = implode(".", $_).".*";
			if (in_array($wildcard, $this->routes_to_ignore)) {
				continue;
			}

			array_push($routes_to_test, $route);
		}

		$this->routes_to_test = collect($routes_to_test);
	}


	/**
	 * Get the models from the routes.
	 *
	 * @author Patrick Reichel
	 */
	protected function _get_models() {

		$this->models = [];
		foreach ($this->routes_to_test as $route) {

			$controller = explode('@', $route->getAction()['controller'])[0];
			$_ = explode("Controller", $controller);
			array_pop($_);
			$_ = implode("Controller", $_);
			if (\Str::startswith($controller, "App\Http\Controllers")) {
				$model = str_replace("\\Http\\Controllers\\", "\\Models\\", $_);
			}
			else {
				$model = str_replace("\\Http\\Controllers\\", "\\Entities\\", $_);
			}

			if (!in_array($model, $this->models)) {
				$model = "\\$model";
				/* $this->models[$model] = new $model(); */
				$this->models[$model] = null;
			}
		}
	}


	/**
	 * Gets a user having the permissions needed for tests.
	 *
	 * @TODO: Switch from hardcoded user to dynamic getting from database or create a new one!
	 *
	 * @author Patrick Reichel
	 */
	protected function _get_user() {

		// TODO: do not hard code any user class, instead fetch a user dynamically
		//       ore add it only for testing (see Laravel factory stuff)
		$this->user = App\Authuser::findOrFail(1);
	}


	/**
	 * Tests all index views.
	 *
	 * @author Patrick Reichel
	 */
	/* public function testExistanceOfIndexViews() { */
	/* 	$this->assertTrue(true); */
	/* } */


	/**
	 * Tests all edit views.
	 *
	 * @TODO: This should be run after creates to be sure that there are existing models – especially if running tests in build process!
	 *
	 * @author Patrick Reichel
	 */
	/* public function testExistanceOfEditViews() { */

	/* 	foreach ($this->models as $modelname => $model) { */
	/* 		$modelname::all()->first(); */
	/* 		$id = $model->id; */
	/* 	} */
	/* } */


    /**
     * Test all MVCs
     *
     * @return void
     */
    public function testGenericMvc()
	{

		dd("TODO: Implement single index/edit tester instead of this method as next step!");

		// TODO: there must be a namespace problem in Testing Context!
		//       All/Many normal non pingpong classes are not found, like routing, Controllers, ...
		//       To fix this issue for routing, i have added the basic route file to TestCase.php
		//       This should be solved better ..
		//       When solving this problem, the following ignore test case array can shrink

		// Fetch all routes
		foreach ($this->routes_to_test as $route)
		{
			$msg = "Testing of ".$route->getName().' ('.$route->getAction()['controller'].')';
			echo "\n$msg";
			\Log::debug($msg);

			// Filter only '*.index' routes and ignore $ignores array
			if ((strpos($route->getName(), '.index') !== false)) {
				// Info Message

				$controller = $this->app->make(explode('@', $route->getAction()['controller'])[0]);

				// Index Page
				$this->actingAs($this->user)->visit($route->getPath());

				// TODO: edit, delete, create page
				// NOTE: it seems to be quit tricky to fetch the object / model from
				//       this context. This is required to make next tests, like jumping
				//       to admin/Contract/{id}/edit. We need to find the first valid id..
			}
		}

    }
}
