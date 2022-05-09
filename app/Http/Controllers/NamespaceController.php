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

namespace App\Http\Controllers;

use Route;

/**
 * NamespaceController
 *
 * This Controller could be used in any MVC context to retrieve actual MVC names
 *
 * NOTE: You MUST follow the following naming examples:
 *       Model: Contract, IpPool, User
 *       Controller: ContractController, IpPoolController, UserController
 *       View: Contract, IpPool, User
 *       Route: provbase::Contract, provbase::IpPool, User
 *       SQL: contract, ippool, Users
 *
 * @author Torsten Schmidt
 */
class NamespaceController
{
    /**
     * Check if actual request is coming from Ping Pong Module Context or from native App context
     *
     * @return true for Ping Pong Module, otherwise false
     */
    public static function is_module_context()
    {
        // check if it's a http request at all
        $route = Route::getCurrentRoute();
        if (! $route) {
            return false;
        }

        if (strtolower(explode('\\', $route->getActionName())[0]) == 'app') {
            return false;
        }

        return true;
    }

    /**
     * Return the MVC namespace path for actual module, like "Modules\ProvBase"
     *
     * @return mvc namespace
     */
    private static function __module_get_mvc_namespace()
    {
        $action = Route::getCurrentRoute()?->getActionName();

        if (! $action) {
            return;
        }

        $a = explode('\\', $action);
        $key = $action[0] == '\\' ? 1 : 0;

        return $a[$key].'\\'.$a[$key + 1];
    }

    /**
     * Return the Model name for current context, like "Contract"
     * NOTE: will only perform in Ping Pong Context
     *
     * @author Torsten Schmidt, Patrick Reichel
     *
     * @return model name
     */
    public static function module_get_pure_model_name()
    {
        $route = Route::getCurrentRoute();

        if (! $route) {
            return;
        }

        $_ = explode('Controller@', $route->getActionName());
        $_ = explode('\\', $_[0]);
        $model = array_pop($_);

        return $model;
    }

    /**
     * Return Model Name
     *
     * @author Torsten Schmidt, Patrick Reichel
     *
     * @return string model name
     */
    public static function get_model_name()
    {
        $pure_model = static::module_get_pure_model_name();

        if (static::is_module_context()) {
            return static::__module_get_mvc_namespace().'\\Entities\\'.$pure_model;
        }

        if (! is_null($pure_model)) {
            return 'App\\'.$pure_model;
        }
    }

    /**
     * Return Controller Name
     *
     * @return string controller name
     */
    public static function get_controller_name()
    {
        $route = Route::getCurrentRoute();

        return $route ? explode('@', $route->getActionName())[0] : null;
    }

    /**
     * Return View Name
     *
     * @return view name
     */
    public static function get_view_name()
    {
        if (static::is_module_context()) {
            return strtolower(explode('\\', static::get_model_name())[1]).'::'.static::module_get_pure_model_name();
        }

        return explode('\\', static::get_model_name())[1]; // parse xyz from 'App/xyz'
    }

    /**
     * Return Route Name
     *
     * @return route name
     */
    public static function get_route_name()
    {
        if (static::is_module_context()) {
            return explode('\\', static::get_model_name())[3];
        }

        return explode('\\', static::get_model_name())[1]; // parse xyz from 'App/xyz'
    }
}
