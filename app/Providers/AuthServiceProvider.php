<?php

namespace App\Providers;

use Auth;
use Bouncer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
    }
}
