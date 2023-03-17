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

namespace App\Providers;

use Auth;
use Bouncer;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Dynamically register permissions with Laravel's Gate.
     *
     * @return void
     */
    public function boot()
    {
        Bouncer::useAbilityModel(\App\Ability::class);
        Bouncer::useRoleModel(\App\Role::class);
        Bouncer::ownedVia(\App\User::class, 'id');
        Bouncer::cache();

        $this->registerPolicies();

        Auth::extend('admin', function ($app, $name, array $config) {
            return new adminGuard(Auth::createUserProvider($config['admin']));
        });

        Auth::extend('ccc', function ($app, $name, array $config) {
            return new cccGuard(Auth::createUserProvider($config['ccc']));
        });

        Gate::define('viewWebSocketsDashboard', fn ($user = null) => $user?->isAn('admin'));
    }
}
