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

namespace App\Observers;

use App;
use App\User;
use Bouncer;
use Session;

class UserObserver
{
    public function created($user)
    {
        Bouncer::allow($user)->toOwn(User::class);
        $user->api_token = $user->api_token = \Illuminate\Support\Str::random(80);
        $user->save();
    }

    public function updating($user)
    {
        // Rebuild cached sidebar when user changes his language
        if ($user->isDirty('language')) {
            Session::forget('menu');

            $userLang = checkLocale($user->language);

            App::setLocale($userLang);
            Session::put('language', $userLang);
        }

        if ($user->isDirty('password')) {
            $user->api_token = \Str::random(80);
        }
    }

    public function deleting($user)
    {
        $self = \Auth::user();
        $authRank = $self->getHighestRank();

        if ($authRank == '101') {
            return;
        }

        if ($self->hasSameRankAs($user) || $self->hasLowerRankThan($user)) {
            return false;
        }
    }
}
