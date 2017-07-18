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
			'Authuser.*',
			'Authrole.*',
			'Config.*',
			'GlobalConfig.*',
			'GuiLog.*',
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
				$model = str_replace("\\Http\\Controllers\\", "\\", $_);
			}
			else {
				$model = str_replace("\\Http\\Controllers\\", "\\Entities\\", $_);
			}

			$this->models[$route->getName()] = $model;
			/* if (!in_array($model, $this->models)) { */
			/* 	$model = "\\$model"; */
			/* 	/1* $this->models[$model] = new $model(); *1/ */
			/* 	$this->models[$model] = null; */
			/* } */
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

		/* dd("TODO: Implement single index/edit tester instead of this method as next step!"); */

		// TODO: there must be a namespace problem in Testing Context!
		//       All/Many normal non pingpong classes are not found, like routing, Controllers, ...
		//       To fix this issue for routing, i have added the basic route file to TestCase.php
		//       This should be solved better ..
		//       When solving this problem, the following ignore test case array can shrink

		// Fetch all routes
		foreach ($this->routes_to_test as $route)
		{
			$msg = "Testing of ".$route->getName().' ('.$route->getAction()['controller'].')';
			echo "\n\n$msg";
			\Log::debug($msg);

			// Filter only '*.index' routes and ignore $ignores array
			if ((\Str::endswith($route->getName(), '.index'))) {

				$this->_testGenericMVCIndex($route);

			}
			elseif ((\Str::endswith($route->getName(), '.create'))) {

				$this->_testGenericMVCCreate($route);

			}
			elseif ((\Str::endswith($route->getName(), '.edit'))) {

				$this->_testGenericMVCEdit($route);

			}
			/* elseif ((\Str::endswith($route->getName(), '.destroy'))) { */

			/* 	$this->_testGenericMVCDestroy($route); */

			/* } */
			else {
				$msg = 'No tests for '.$route->getName().' implemented';
				\Log::warning($msg);
				echo "\n  WARNING: $msg";
			}

		}
    }

	/**
	 * Tests index view
	 *
	 * @author Patrick Reichel
	 */
	protected function _testGenericMVCIndex($route) {

		$controller = $this->app->make(explode('@', $route->getAction()['controller'])[0]);

		// Index Page
		$this->actingAs($this->user)->visit($route->getPath());
	}

	/**
	 * Tests create view
	 *
	 * @author Patrick Reichel
	 */
	protected function _testGenericMVCCreate($route) {

		$controller = $this->app->make(explode('@', $route->getAction()['controller'])[0]);

		// Index Page
		$this->actingAs($this->user)->visit($route->getPath());
	}

	/**
	 * Tests edit view
	 *
	 * @author Patrick Reichel
	 */
	protected function _testGenericMVCEdit($route) {

		$model_name = "\\".$this->models[$route->getName()];
		/* $model = $model::all()->take(1); */
		$model = $model_name::all()->first();
		$model_id = $model->id;
		/* $model = call_user_func($model_name.'::find'); */
		/* dd($route->getAction()); */
		/* $controller = $this->app->make(explode('@', $route->getAction()['controller'])[0], array($model_id)); */
		echo "\n--------\n";
		echo($model_id);

		$route->setParameter('id', $model_id);
		$uri = $route->getPath();
		/* $route_params = $route->signatureParameters(); */
		/* if ($route_params) { */
		/* 	$first_param = $route_params[0]->name; */
		/* 	echo "\n$first_param"; */
		/* 	$uri = str_replace("{".$first_param."}", $model_id, $uri); */
		/* } */
		echo "\n$uri";
		$this->actingAs($this->user)->visit($uri);
	}

	/**
	 * Tests destroy view
	 *
	 * @author Patrick Reichel
	 */
	protected function _testGenericMVCDestroy($route) {

		$controller = $this->app->make(explode('@', $route->getAction()['controller'])[0]);

		// Index Page
		$this->actingAs($this->user)->visit($route->getPath());
	}
}
