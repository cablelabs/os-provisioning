<?php

namespace Acme\core;

use Route;
use Request;
use App\BaseModel;

/**
 * BaseRoute API
 *
 * This Class will be used to create our own http routing functions
 *
 * @author Torsten Schmidt
 */
class BaseRoute
{
    public static $admin_prefix = 'admin';

    /**
     * Return the correct base URL
     * @todo move somewhere else
     * @return type string the actual base url
     */
    public static function get_base_url()
    {
        $url = Request::root();
        $port = Request::getPort();

        if ($port == env('HTTPS_ADMIN_PORT', 8080)) {
            return $url.'/admin';
        }

        if ($port == env('HTTPS_CCC_PORT', 443)) {
            return $url.'/customer';
        }

        return $url; // will not work
    }

    /**
     * Our own custom Route function, which generates generic Routes
     * for our Ressources and takes care of authorization and
     * authentication through middlewares
     *
     * @author Torsten Schmidt
     * @author Christian Schramm
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return void
     */
    public static function resource(string $name, string $controller, array $options = [])
    {
        $models = BaseModel::get_models();
        if (! isset($models[$name])) {
            return;
        }

        // Index
        Route::get($name, [
            'as' => $name.'.index',
            'uses' => $controller.'@index',
            'middleware' => ['web', 'can:view,'.$models[$name]],
            $options,
        ]);

        // Index DataTable via Ajax
        Route::get("$name/datatables", [
            'as' => $name.'.data',
            'uses' => $controller.'@index_datatables_ajax',
            'middleware' => ['web', 'can:view,'.$models[$name]],
            $options,
        ]);

        // Store
        Route::post($name, [
            'as' => $name.'.store',
            'uses' => $controller.'@store',
            'middleware' => ['web', 'can:create,'.$models[$name]],
            $options,
        ]);

        // Create
        Route::get("$name/create", [
            'as' => $name.'.create',
            'uses' => $controller.'@create',
            'middleware' => ['web', 'can:create,'.$models[$name]],
            $options,
        ]);

        Route::post("$name/create", [
            'as' => $name.'.create',
            'uses' => $controller.'@create',
            'middleware' => ['web', 'can:create,'.$models[$name]],
            $options,
        ]);

        // Import
        Route::get("$name/import", [
            'as' => $name.'.import',
            'uses' => $controller.'@import',
            'middleware' => ['web', 'can:create,'.$models[$name]],
            $options,
        ]);

        Route::post("$name/import_parse", [
            'as' => $name.'.import_parse',
            'uses' => $controller.'@import_parse',
            'middleware' => ['web', 'can:create,'.$models[$name]],
            $options,
        ]);

        Route::post("$name/import_process", [
            'as' => $name.'.import_process',
            'uses' => $controller.'@import_process',
            'middleware' => ['web', 'can:create,'.$models[$name]],
            $options,
        ]);

        // edit
        Route::get("$name/{{$name}}", [
            'as' => $name.'.edit',
            'uses' => $controller.'@edit',
            'middleware' => ['web', 'can:view,'.$models[$name]],
            $options,
        ]);

        Route::get("$name/{{$name}}/log", [
            'as' => $name.'.guilog',
            'uses' => '\App\Http\Controllers\GuiLogController@filter',
            'middleware' => ['web', 'can:view,'.$models[$name]],
        ]);

        Route::get("$name/autocomplete/{column}", [
            'as' => $name.'.autocomplete',
            'uses' => $controller.'@autocomplete_ajax',
            'middleware' => ['web', 'can:view,'.$models[$name]],
            $options,
        ]);

        // update
        Route::patch("$name/{{$name}}", [
            'as' => $name.'.update',
            'uses' => $controller.'@update',
            'middleware' => ['web', 'can:update,'.$models[$name]],
            $options,
        ]);

        Route::put("$name/{{$name}}", [
            'as' => $name.'.update',
            'uses' => $controller.'@update',
            'middleware' => ['web', 'can:update,'.$models[$name]],
            $options,
        ]);

        // delete
        Route::delete("$name/{{$name}}", [
            'as' => $name.'.destroy',
            'uses' => $controller.'@destroy',
            'middleware' =>  ['web', 'can:delete,'.$models[$name]],
            $options,
        ]);

        /*
         * API Routes using Basic Authentication
         *
         * Every User with the Ability 'use-api' can access these Routes
         */
        Route::group(['prefix' => 'api/v{ver}'], function () use ($name, $controller, $options, $models) {
            Route::get("$name", [
                'as' => $name.'.api_index',
                'uses' => $controller.'@api_index',
                'middleware' => ['api', 'auth.basic', 'can:view,'.$models[$name]],
                $options,
            ]);

            Route::post("$name", [
                'as' => $name.'.api_store',
                'uses' => $controller.'@api_store',
                'middleware' => ['api', 'auth.basic', 'can:create,'.$models[$name]],
                $options,
            ]);

            Route::get("$name/create", [
                'as' => $name.'.api_create',
                'uses' => $controller.'@api_create',
                'middleware' => ['api', 'auth.basic', 'can:create,'.$models[$name]],
                $options,
            ]);

            Route::post("$name/create", [
                'as' => $name.'.api_create',
                'uses' => $controller.'@api_create',
                'middleware' => ['api', 'auth.basic', 'can:create,'.$models[$name]],
                $options,
            ]);

            Route::get("$name/{{$name}}", [
                'as' => $name.'.api_get',
                'uses' => $controller.'@api_get',
                'middleware' => ['api', 'auth.basic', 'can:view,'.$models[$name]],
                $options,
            ]);

            Route::get("$name/{{$name}}/status", [
                'as' => $name.'.api_status',
                'uses' => $controller.'@api_status',
                'middleware' => ['api', 'auth.basic', 'can:view,'.$models[$name]],
                $options,
            ]);

            Route::patch("$name/{{$name}}", [
                'as' => $name.'.api_update',
                'uses' => $controller.'@api_update',
                'middleware' => ['api', 'auth.basic', 'can:update,'.$models[$name]],
                $options,
            ]);

            Route::put("$name/{{$name}}", [
                'as' => $name.'.api_update',
                'uses' => $controller.'@api_update',
                'middleware' => ['api', 'auth.basic', 'can:update,'.$models[$name]],
                $options,
            ]);

            Route::delete("$name/{{$name}}", [
                'as' => $name.'.api_destroy',
                'uses' => $controller.'@api_destroy',
                'middleware' =>  ['api', 'auth.basic', 'can:delete,'.$models[$name]],
                $options,
            ]);
        });
    }

    /**
     * Our own route group with shared attributes and some default settings
     * like prefix and as statement we MUST use.
     *
     * @author Torsten Schmidt
     * @param  array  $attributes
     * @param  \Closure  $callback
     * @return void
     */
    public static function group(array $attributes, \Closure $callback)
    {
        $attributes['prefix'] = self::$admin_prefix;
        $attributes['as'] = ''; // clear route name prefix

        Route::group($attributes, $callback);
    }

    /**
     * The following functions are simple helpers to adapt automatic authentication stuff
     */
    public static function appendMiddleware($action = null)
    {
        if (array_key_exists('middleware', $action)) {
            array_unshift($action['middleware'], 'web');
        } else {
            $action['middleware'] = ['web', 'auth'];
        }

        return $action;
    }

    public static function get($uri, $action = null)
    {
        $action = self::appendMiddleware($action);

        return Route::get($uri, $action);
    }

    public static function post($uri, $action = null)
    {
        $action = self::appendMiddleware($action);

        return Route::post($uri, $action);
    }

    public static function put($uri, $action = null)
    {
        $action = self::appendMiddleware($action);

        return Route::put($uri, $action);
    }
}
