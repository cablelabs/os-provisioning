<?php

namespace App\extensions\websockets;

class WebsocketApi
{
    protected $pusherApi;

    public function getPusherApi()
    {
        $connection = config('broadcasting.connections.pusher');

        return new \Pusher\Pusher(
            $connection['key'],
            $connection['secret'],
            $connection['app_id'],
            [
                'cluster' => $connection['options']['cluster'],
                'curl_options' => $connection['options']['curl_options'],
                'host' => $connection['options']['host'],
                'port' => $connection['options']['port'],
                'useTLS' => $connection['options']['encrypted'],
                'scheme' => $connection['options']['scheme'],
            ]
        );
    }

    /**
     * Check if subscribers are currently listening to the given channel
     * See BeyondCode\LaravelWebSockets\HttpApi\Controllers\FetchChannelsController
     *
     * @param string
     * @param bool
     * @return bool
     */
    public function channelHasSubscribers($channel, $excludeRequestingUser = false)
    {
        if (! $this->pusherApi) {
            $this->pusherApi = self::getPusherApi();
        }

        $users = $this->pusherApi->get_users_info($channel);
        if (! $users) {
            \Log::debug("$channel channel - Subscribed users: false");

            return false;
        }

        /* Attention: With this the currently initiating user will not be counted as subscriber even when websocket
            connection is already established so that initiating the loop always works. The resulting problem can be
            that the user can initiate multiple loops when opening multiple tabs (at the same time). This should be
            addressed in javascript by not triggering the loop when the tab is hidden
        */
        if ($excludeRequestingUser && isset($users->users[0]) && $users->users[0]->id == auth()->user()->id) {
            unset($users->users[0]);
        }

        \Log::debug("$channel channel - subscribed users: ".json_encode($users->users));

        return $users->users != [];
    }
}
