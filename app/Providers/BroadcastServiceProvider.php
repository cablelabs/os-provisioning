<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Broadcast::channel('snmpvalues.{netelement}.{thirdDim}', function ($user) {
            // \Log::debug(__class__.': Authenticate user '.$user->id);

            return ['id' => $user->id, 'name' => $user->login_name];
        });
    }
}
