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

namespace Acme\core;

use App\BaseModel;
use Illuminate\Support\Facades\Route;

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
            'middleware' => ['web', 'auth', 'can:view,'.$models[$name]],
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
        Route::match(['get', 'post'], "$name/create", [
            'as' => $name.'.create',
            'uses' => $controller.'@create',
            'middleware' => ['web', 'auth', 'can:create,'.$models[$name]],
            $options,
        ]);

        // edit
        Route::get("$name/{{$name}}", [
            'as' => $name.'.edit',
            'uses' => $controller.'@edit',
            'middleware' => ['web', 'auth', 'can:view,'.$models[$name]],
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

        Route::get("$name/select/{relation}", [
            'as' => $name.'.select2',
            'uses' => $controller.'@select2Ajax',
            'middleware' => ['web', 'can:view,'.$models[$name]],
            $options,
        ]);

        BaseRoute::get("$name/relation/{model}/{relation}", [
            'as' => $name.'.relationDatatable',
            'uses' => $controller.'@getRelationDatatable',
            'middleware' => ['web', 'can:view,'.$models[$name]],
        ]);

        // update
        Route::match(['patch', 'put'], "$name/{{$name}}", [
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
                'middleware' => ['api', 'can:view,'.$models[$name]],
                $options,
            ]);

            Route::post("$name", [
                'as' => $name.'.api_store',
                'uses' => $controller.'@api_store',
                'middleware' => ['api', 'can:create,'.$models[$name]],
                $options,
            ]);

            Route::match(['get', 'post'], "$name/create", [
                'as' => $name.'.api_create',
                'uses' => $controller.'@api_create',
                'middleware' => ['api', 'can:create,'.$models[$name]],
                $options,
            ]);

            Route::get("$name/{{$name}}", [
                'as' => $name.'.api_get',
                'uses' => $controller.'@api_get',
                'middleware' => ['api', 'can:view,'.$models[$name]],
                $options,
            ]);

            Route::get("$name/{{$name}}/status", [
                'as' => $name.'.api_status',
                'uses' => $controller.'@api_status',
                'middleware' => ['api', 'can:view,'.$models[$name]],
                $options,
            ]);

            Route::match(['patch', 'put'], "$name/{{$name}}", [
                'as' => $name.'.api_update',
                'uses' => $controller.'@api_update',
                'middleware' => ['api', 'can:update,'.$models[$name]],
                $options,
            ]);

            Route::delete("$name/{{$name}}", [
                'as' => $name.'.api_destroy',
                'uses' => $controller.'@api_destroy',
                'middleware' =>  ['api', 'can:delete,'.$models[$name]],
                $options,
            ]);
        });
    }

    /**
     * Add routes needed for a module config MVC
     */
    public static function globalConfResource(string $name, string $controller, array $options = [])
    {
        $models = BaseModel::get_models();

        Route::match(['patch', 'put'], "$name/{{$name}}", [
            'as' => $name.'.update',
            'uses' => $controller.'@update',
            'middleware' => ['web', 'can:update,'.$models[$name]],
            $options,
        ]);

        Route::group(['prefix' => 'api/v{ver}'], function () use ($name, $controller, $options, $models) {
            Route::get("$name/{{$name}}", [
                'as' => $name.'.api_get',
                'uses' => $controller.'@api_get',
                'middleware' => ['api', 'can:view,'.$models[$name]],
                $options,
            ]);

            Route::match(['patch', 'put'], "$name/{{$name}}", [
                'as' => $name.'.api_update',
                'uses' => $controller.'@api_update',
                'middleware' => ['api', 'can:update,'.$models[$name]],
                $options,
            ]);
        });
    }

    /**
     * Our own route group with shared attributes and some default settings
     * like prefix and as statement we MUST use.
     *
     * @author Torsten Schmidt
     *
     * @param  array  $attributes
     * @param  \Closure  $callback
     * @return void
     */
    public static function group(array $attributes, \Closure $callback)
    {
        $attributes['prefix'] = self::$admin_prefix.(isset($attributes['prefix']) ? $attributes['prefix'] : '');
        $attributes['as'] = ''; // clear route name prefix

        Route::group($attributes, $callback);
    }

    /**
     * The following functions are simple helpers to adapt automatic authentication stuff
     */
    public static function appendMiddleware(&$action = null)
    {
        if (! array_key_exists('middleware', $action)) {
            return $action['middleware'] = ['web', 'auth'];
        }

        return array_unshift($action['middleware'], 'web');
    }

    public static function get($uri, $action = null)
    {
        self::appendMiddleware($action);
        array_splice($action['middleware'], 1, 0, 'auth');
        $action['middleware'] = array_unique($action['middleware']);

        return Route::get($uri, $action);
    }

    public static function post($uri, $action = null)
    {
        self::appendMiddleware($action);

        return Route::post($uri, $action);
    }

    public static function put($uri, $action = null)
    {
        self::appendMiddleware($action);

        return Route::put($uri, $action);
    }
}
