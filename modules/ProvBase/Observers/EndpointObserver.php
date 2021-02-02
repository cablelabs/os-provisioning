<?php

namespace Modules\ProvBase\Observers;

use Modules\ProvBase\Entities\RadReply;
use Modules\ProvBase\Entities\RadIpPool;

class EndpointObserver
{
    public function creating($endpoint)
    {
        if (! $endpoint->fixed_ip) {
            $endpoint->ip = null;
        }
    }

    public function created($endpoint)
    {
        self::reserveAddress($endpoint);

        $endpoint->makeDhcp();
        if ($endpoint->netGw) {
            $endpoint->netGw->makeDhcp4Conf();
        }
        $endpoint->nsupdate();
    }

    public function updating($endpoint)
    {
        if (! $endpoint->fixed_ip) {
            $endpoint->ip = null;
        }
        $endpoint->nsupdate(true);
    }

    public function updated($endpoint)
    {
        self::reserveAddress($endpoint);

        $endpoint->makeDhcp();
        if ($endpoint->netGw) {
            $endpoint->netGw->makeDhcp4Conf();
        }
        $endpoint->nsupdate();
    }

    public function deleted($endpoint)
    {
        self::reserveAddress($endpoint);

        $endpoint->makeDhcp();
        if ($endpoint->netGw) {
            $endpoint->netGw->makeDhcp4Conf();
        }
        $endpoint->nsupdate(true);
    }

    /**
     * Handle changes of reserved ip addresses based on endpoints
     * This is called on created/updated/deleted in Endpoint observer
     *
     * @author Ole Ernst
     */
    private static function reserveAddress($endpoint)
    {
        // delete radreply containing Framed-IP-Address
        $endpoint->modem->radreply()->delete();

        // reset state of original ip address
        RadIpPool::where('framedipaddress', $endpoint->getOriginal('ip'))
            ->update(['expiry_time' => null, 'username' => '']);

        if ($endpoint->deleted_at || ! $endpoint->ip || ! $endpoint->modem->isPPP()) {
            return;
        }

        // add new radreply
        $reply = new RadReply;
        $reply->username = $endpoint->modem->ppp_username;
        $reply->attribute = 'Framed-IP-Address';
        $reply->op = ':=';
        $reply->value = $endpoint->ip;
        $reply->save();

        // set expiry_time to 'infinity' for reserved ip addresses
        RadIpPool::where('framedipaddress', $endpoint->ip)
            ->update(['expiry_time' => '9999-12-31 23:59:59', 'username' => $endpoint->modem->ppp_username]);
    }
}
