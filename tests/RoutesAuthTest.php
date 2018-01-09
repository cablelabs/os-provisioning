<?php

/**
 * Tests if all routes use auth middleware
 *
 * @author Patrick Reichel
 */
class RoutesAuthTest extends TestCase {

	// there can be routes not using auth middleware – define them here to exclude from testing
	protected $routes_not_using_auth_middleware = [
		'Auth.logout',
		'CustomerAuth.logout',
		'ProvVoipEnvia.cron',
	];

	protected $problematic_routes_to_check = [
		'Tree.delete',
		'Modem.ping',
		'Modem.monitoring',
		'Modem.log',
		'Modem.lease',
	];



	/**
	 * Constructor
	 *
	 * @author Patrick Reichel
	 */
	public function __construct() {

		return parent::__construct();

	}


	/**
	 * Creates a Laravel application used for testing
	 *
	 * @author Patrick Reichel
	 */
	public function createApplication() {

		$app = parent::createApplication();
		return $app;
	}


	/**
	 * Method to test all routes.
	 *
	 * @author Patrick Reichel
	 */
	public function testRoutesAuthMiddleware() {

		$not_testable_routes = [];

		$routeCollection = Route::getRoutes();
		foreach ($routeCollection as $value) {
			$name = $value->getName();

			// no name – no test
			if (!boolval($name))
				continue;

			// route without auth middleware
			if (in_array($name, $this->routes_not_using_auth_middleware))
				continue;

			// problems with route: TODO: check for reasons
			if (in_array($name, $this->problematic_routes_to_check))
				continue;

			$_ = URL::route($value->getName(), [1, 1, 1, 1, 1], true);
			$url = explode('?', $_)[0];
			$method = $value->getMethods()[0];

			// admin redirects to login page
			if (
				($name == 'admin')
				||
				($name == 'Auth.login')
				||
				($name == 'CustomerAuth.login')
			) {
				echo "\nTesting $name ($method: $url)";
				$this->visit($url)
					->see('Username')
					->see('Password')
					->see('Sign me in');
			}
			else {
				switch ($method) {
					case "GET":
						echo "\nTesting $name ($method: $url)";
						$this->get($url);
						break;
					/* case "POST": */
					/* 	echo "\nTesting $name ($method: $url)"; */
					/* 	$this->post($url); */
					/* 	break; */
					default:
						array_push($not_testable_routes, "$name ($method: $url)");
				}
				$this->assertResponseStatus(403);
			}
		}

		// print routes with known problems
		if ($not_testable_routes) {
			echo "\n\nThere are routes that are not testable (e.g. not implemented method):";
		}
		foreach ($not_testable_routes as $r) {
			echo "\n	$r";
		}

		// print routes with known problems
		if ($this->problematic_routes_to_check) {
			echo "\n\nThere are routes with known problems (e.g. return code is 500)";
			echo "\nSolve and remove from array!";
		}
		foreach ($this->problematic_routes_to_check as $r) {
			echo "\n	$r";
		}
	}
}
