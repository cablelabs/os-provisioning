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
     * @return bool
     */
    public function channelHasSubscribers($channel)
    {
        if (! $this->pusherApi) {
            $this->pusherApi = self::getPusherApi();
        }

        $users = $this->pusherApi->get_users_info($channel);

        if (! $users) {
            \Log::debug("$channel channel - Subscribed users: false");

            return false;
        }

        \Log::debug("$channel channel - subscribed users: ".json_encode($users->users));

        return $users->users != [];
    }
}
