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

        $endpoint->modem->restart_modem();
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

        $endpoint->modem->restart_modem();
    }

    public function deleted($endpoint)
    {
        self::reserveAddress($endpoint);

        $endpoint->makeDhcp();
        if ($endpoint->netGw) {
            $endpoint->netGw->makeDhcp4Conf();
        }
        $endpoint->nsupdate(true);

        $endpoint->modem->restart_modem();
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

        // add / update unreserved ip address
        if ($endpoint->getOriginal('ip')) {
            RadIpPool::updateOrCreate(
                ['framedipaddress' => $endpoint->getOriginal('ip')],
                ['pool_name' => 'CPEPub', 'expiry_time' => null, 'username' => '']
            );
        }

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

        // remove reserved ip address from ippool
        RadIpPool::where('framedipaddress', $endpoint->ip)->delete();
    }
}
