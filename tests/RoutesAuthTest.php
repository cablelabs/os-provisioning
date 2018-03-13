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

	// some routes make problems (e.g. returning status 500 in testing
	// Solve this problems and remove routes from array
	protected $problematic_routes_to_check = [
		'Tree.delete',
		'Modem.ping',
		'Modem.monitoring',
		'Modem.log',
		'Modem.lease',
	];

	// some routes do redirect to login page instead of giving status 403
	// as these routs needs other tests you can define them here
	protected $routes_redirecting_to_login_page = [
		'admin',
		'Auth.login',
		'CustomerAuth.login',
		'CHome',
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

			if (in_array($name, $this->routes_redirecting_to_login_page)) {
				// special test for redirects to login page
				echo "\nTesting $name ($method: $url)";
				$this->visit($url)
					->see('Username')
					->see('Password')
					->see('Sign me in');
			}
			else {
				// all other routes should return 403 if not logged in
				echo "\nTesting $name ($method: $url)";
				$this->call($method, $url, []);
				$this->assertResponseStatus(403);
				$this->see('denied');
			}
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
