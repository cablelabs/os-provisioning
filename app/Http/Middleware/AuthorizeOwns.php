<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;

class AuthorizeOwns
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * The gate instance.
     *
     * @var \Illuminate\Contracts\Auth\Access\Gate
     */
    protected $gate;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function __construct(Auth $auth, Gate $gate)
    {
        $this->auth = $auth;
        $this->gate = $gate;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $ability
     * @param  array|null  $models
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle($request, Closure $next, $ability, $model)
    {
        $user = $this->auth->authenticate();

        if ($user->can($ability, $model::find($request->id))) {
            return $next($request);
        }

        throw new AuthenticationException('You are not allowed to Enter this page');
    }
}
