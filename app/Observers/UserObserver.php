<?php

namespace App\Observers;

use App;
use Bouncer;
use Session;
use App\User;

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
