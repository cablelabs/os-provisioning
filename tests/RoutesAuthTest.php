<?php

namespace Tests;

use URL;
use Route;

/**
 * Tests if all routes use auth middleware
 *
 * @author Patrick Reichel
 */
class RoutesAuthTest extends TestCase
{
    // there can be routes not using auth middleware – define them here to exclude from testing
    protected $routes_not_using_auth_middleware = [
        'ProvVoipEnvia.cron',
    ];

    // some routes make problems (e.g. returning status 500 in testing
    // Solve this problems and remove routes from array
    protected $routes_which_are_not_checked = [
        'debugbar.openhandler',
        'debugbar.clockwork',
        'debugbar.assets.css',
        'debugbar.assets.js',
    ];

    // some routes do redirect to login page instead of giving status 403
    // as these routs needs other tests you can define them here
    protected $routes_redirecting_to_login_page = [
        'admin',
        'adminLogin',
        'HomeCcc',
        'CustomerPsw',
    ];

    // there now is an API with own routes – add all available API versions here
    protected $api_versions = [
        0,
    ];

    /**
     * Constructor
     *
     * @author Patrick Reichel
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * Creates a Laravel application used for testing
     *
     * @author Patrick Reichel
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        return $app;
    }

    /**
     * Method to test all routes.
     *
     * @author Patrick Reichel
     */
    public function testRoutesAuthMiddleware()
    {
        $routeCollection = Route::getRoutes();
        foreach ($routeCollection as $value) {
            $name = $value->getName();
            $method = $value->getMethods()[0];

            // no name – no test
            if (! boolval($name) ||
                in_array($name, $this->routes_not_using_auth_middleware) ||
                in_array($name, $this->routes_which_are_not_checked)
                ) {
                continue;
            }

            $fullUrl = URL::route($value->getName(), [1, 1, 1, 1, 1], true);
            $url = explode('?', $fullUrl)[0];
            $method = $value->getMethods()[0];
            $isApiRoute = strpos($url, 'api/v');
            $isDetatchAll = strpos($name, 'detach_all');

            echo "\nTesting $name ($method: $url)";

            if ($isApiRoute) {
                $this->call($method, $url, [], [], [], ['PHP_AUTH_USER' => 'testuser', 'PHP_AUTH_PW' => 'test']);
                $this->assertResponseStatus(401); //Unauthorized
            } elseif (in_array($name, $this->routes_redirecting_to_login_page) || $method == 'GET') {
                $this->visit($url)
                    ->see('Username')
                    ->see('Password')
                    ->see('Sign me in');
            } elseif ($isDetatchAll) {
                $this->call($method, $url, []);
                $this->assertResponseStatus(302);
            } else {  // all other routes should return 403 if not logged in

                $this->call($method, $url, []);
                $this->assertResponseStatus(403);
            }
        }

        // print routes with known problems
        if (! empty($this->routes_which_are_not_checked)) {
            echo "\n\nThese routes are not checked";

            foreach ($this->routes_which_are_not_checked as $route) {
                echo "\n	$route";
            }

            echo "\n\nThese routes are not using auth middleware";
            echo "\nPlease review them carefully - they are exposed";
            foreach ($this->routes_not_using_auth_middleware as $route) {
                echo "\n	$route";
            }
        }
    }
}
