<?php

namespace Tests;

use Route;
use Illuminate\Foundation\Testing\WithoutMiddleware;

/**
 * Tester for visibility of views.
 *
 * For all routes to be found (and not on a blacklist) we check if (ATM create, edit and index) views are visible and
 * if the two headlines can be found on the returned sites.
 * This will fail if there are uncaught Exceptions in views.
 *
 * @author Patrick Reichel
 */
class ViewsVisibleGenericTest extends TestCase
{
    use WithoutMiddleware;

    // flag to enable/disable debug output
    protected $debug = false;

    // some instance variables to be filled later
    protected $user = null;
    protected $routes_to_ignore = null;
    protected $routes_to_test = null;
    protected $models = null;
    protected $global_config = null;

    /**
     * Creates the application.
     *
     * @author Patrick Reichel
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        echo "\n\nFiring up ".__CLASS__;

        $this->_get_routes_blacklist();
        $this->_get_routes_to_test();
        $this->_get_user();
        $this->_get_models();
        $this->_get_global_config();

        return $app;
    }

    /**
     * Gets global config.
     *
     * @author Patrick Reichel
     */
    protected function _get_global_config()
    {
        $this->global_config = \App\GlobalConfig::first();
    }

    /**
     * Used to define a blacklist of routes to be excluded from tests.
     *
     * @author Patrick Reichel
     */
    protected function _get_routes_blacklist()
    {

        // add all routes that cannot be visited (e.g. missing index views, …)
        // you can specify:
        //		single routes (like Item.index)
        //		wildcard routes (like User.*) to ignore complete MVCs
        //		wildcard actions (like *.destroy) to ignore all actions of a kind
        $this->routes_to_ignore = [
            /* 'User.*',
            'Role.*',
            'Ccc.edit', */
            'Config.*',
            'Dashboard.edit',
            'Domain.create',
            'GlobalConfig.*',
            'GuiLog.*',
            'Indices.create',
            'Invoice.index',
            'Item.index',
            'PhonenumberManagement.index',
            'ProvMon.index',
            'ProvVoipEnvia.index',	// not a real MVC
            'Welcome.*',
            '*.destroy',
            '*.dump',
            '*.dumpall',
            '*.fulltextSearch',
            '*.store',
            '*.detach_all',
            '*.',
        ];
    }

    /**
     * Get the routes to be tested.
     * Makes use of route blacklist.
     *
     * @author Patrick Reichel
     */
    protected function _get_routes_to_test()
    {
        $routes_to_test = [];
        foreach (Route::getRoutes() as $route) {

            // handle explicite excluded routes
            if (in_array($route->getName(), $this->routes_to_ignore)) {
                $msg = 'Route '.$route->getName().' is not tested within '.__CLASS__;
                echo "\nINFO: $msg";
                \Log::info($msg);
                continue;
            }

            $route_parts = explode('.', $route->getName());
            $action = array_pop($route_parts);
            $route_parts = [implode('.', $route_parts), $action];

            // handle leading and trailing wildcards
            $wildcard_routes = [];
            array_push($wildcard_routes, '*.'.$route_parts[1]);
            array_push($wildcard_routes, $route_parts[0].'.*');

            foreach ($wildcard_routes as $wildcard_route) {
                if (in_array($wildcard_route, $this->routes_to_ignore)) {
                    $msg = 'Route '.$route->getName().' is not tested within '.__CLASS__;
                    echo "\nINFO: $msg";
                    \Log::info($msg);
                    continue;
                }
            }

            // route has to be tested
            array_push($routes_to_test, $route);
        }

        $this->routes_to_test = collect($routes_to_test);
    }

    /**
     * Get the models from the routes.
     *
     * @author Patrick Reichel
     */
    protected function _get_models()
    {
        $this->models = [];
        foreach ($this->routes_to_test as $route) {
            $controller = explode('@', $route->getAction()['controller'])[0];
            $_ = explode('Controller', $controller);
            array_pop($_);
            $_ = implode('Controller', $_);
            if (\Str::startswith($controller, "App\Http\Controllers")) {
                $model = str_replace('\\Http\\Controllers\\', '\\', $_);
            } else {
                $model = str_replace('\\Http\\Controllers\\', '\\Entities\\', $_);
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
    protected function _get_user()
    {

        // TODO: do not hard code any user class, instead fetch a user dynamically
        //       or add it only for testing (see Laravel factory stuff)
        $this->user = App\User::findOrFail(1);
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
        foreach ($this->routes_to_test as $route) {
            $msg = 'Testing of '.$route->getName().' ('.$route->getAction()['controller'].')';
            if ($this->debug) {
                echo "\n\n$msg";
            }
            \Log::debug($msg);

            echo "\nVisit ".$route->getName();

            // Filter only '*.index' routes and ignore $ignores array
            if ((\Str::endswith($route->getName(), '.index'))) {
                $this->_testGenericMVCIndexView($route);
            } elseif ((\Str::endswith($route->getName(), '.create'))) {
                $this->_testGenericMVCCreateView($route);
            } elseif ((\Str::endswith($route->getName(), '.edit'))) {
                $this->_testGenericMVCEditView($route);
            }
            /* elseif ((\Str::endswith($route->getName(), '.destroy'))) { */

            /* 	$this->_testGenericMVCDestroy($route); */

            /* } */
            else {
                $msg = 'No tests for '.$route->getName().' implemented';
                \Log::warning($msg);
                echo "\nWARNING: $msg";
            }
        }
    }

    /**
     * Tests index view
     *
     * @author Patrick Reichel
     */
    protected function _testGenericMVCIndexView($route)
    {

        /* $controller = $this->app->make(explode('@', $route->getAction()['controller'])[0]); */
        // Index Page
        $this->actingAs($this->user)
            ->visit($route->getPath())
            ->see($this->global_config->headline1)
            ->see($this->global_config->headline2);
    }

    /**
     * Tests create view
     *
     * @author Patrick Reichel
     */
    protected function _testGenericMVCCreateView($route)
    {

        /* $controller = $this->app->make(explode('@', $route->getAction()['controller'])[0]); */

        // Create Page
        $this->actingAs($this->user)
            ->visit($route->getPath())
            ->see($this->global_config->headline1)
            ->see($this->global_config->headline2);
    }

    /**
     * Tests edit view
     *
     * @author Patrick Reichel
     */
    protected function _testGenericMVCEditView($route)
    {
        $uri = $route->uri();
        $curly_bracket_count = substr_count($uri, '{');

        if ($curly_bracket_count > 1) {
            // if there is more than one route parameter – we cannot test (currently not implemented)
            $msg = 'Route '.$route->getName().' expects more than one parameter. Cannot be tested';
            echo "\nWARNING: $msg";
            \Log::warning($msg);

            return;
        }

        if ($curly_bracket_count == 1) {
            // replace the placeholder by model ID

            // therefore first get a model instance to get a valid ID
            $model_name = '\\'.$this->models[$route->getName()];
            $model = $model_name::all()->first();

            // check if we have a model instance – if not we cannot call the edit view
            if (! $model) {
                $msg = "No instance of $model_name found – cannot test ".$route->getName();
                echo "\nWARNING: $msg";
                \Log::warning($msg);

                return;
            }
            $model_id = $model->id;

            // then replace the placeholder in the URI by model ID
            $uri = preg_replace('#\{[a-zA-Z]*\}#', $model->id, $uri);
        }

        $this->actingAs($this->user)
            ->visit($uri)
            ->see($this->global_config->headline1)
            ->see($this->global_config->headline2);
    }

    /**
     * Tests destroy view
     *
     * @author Patrick Reichel
     */
    protected function _testGenericMVCDestroyView($route)
    {

        /* $controller = $this->app->make(explode('@', $route->getAction()['controller'])[0]); */

        // Index Page
        $this->actingAs($this->user)->visit($route->getPath());
    }
}
