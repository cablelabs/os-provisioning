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
                'useTLS' => true,
                'curl_options' => [
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ],
                'host' => $connection['options']['host'],
                'port' => '6001',
                'scheme' => 'https',
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
            \Log::debug('Subscribed users: false');

            return false;
        }

        \Log::debug('Subscribed users: '.json_encode($users->users));

        return $users->users != [];
    }
}
