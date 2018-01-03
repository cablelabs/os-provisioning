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

			$_ = URL::route($value->getName(), [1, 1, 1, 1, 1], true);
			$url = explode('?', $_)[0];
			$method = $value->getMethods()[0];
echo "\nTesting $name ($method: $url)";

			// admin redirects to login page
			if (
				($name == 'admin')
				||
				($name == 'Auth.login')
				||
				($name == 'CustomerAuth.login')
			) {
				$this->visit($url)
					->see('Username')
					->see('Password')
					->see('Sign me in');
			}
			else {
				switch ($method) {
				case "GET":
					$this->get($url);
					break;
				/* case "POST": */
				/* 	$this->post("https://localhost:8080/admin/Contract"); */
				/* 	break; */
				default:
					echo "\n";
					echo "WARNING: Cannot check $name ($method: $url)";
				}
				$this->assertResponseStatus(403);
			}
		}
	}
}
