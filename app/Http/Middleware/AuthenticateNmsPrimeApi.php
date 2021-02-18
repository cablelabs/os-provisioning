<?php

namespace App\Http\Middleware;

use Closure;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Illuminate\Contracts\Auth\Factory as Auth;

class AuthenticateNmsPrimeApi
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request. Copied from the original Laravel middleware
     * and modified to use basic auth for api v0.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        if ($request->route()->parameters()['ver'] == 0 && ! $this->auth->guard()->basic()) {
            if (Bouncer::cannot('use api')) {
                return response()->v0ApiReply(['messages' => ['errors' => ['Unauthorized']]], false, null, 403);
            }

            return $next($request);
        }

        if ($this->auth->guard('api')->check()) {
            $this->auth->shouldUse('api');
        }

        return $next($request);
    }
}
