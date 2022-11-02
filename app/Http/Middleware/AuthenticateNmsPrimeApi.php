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
        if (isset($request->route()->parameters()['ver']) && $request->route()->parameters()['ver'] == 0 && ! $this->auth->guard()->basic()) {
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
