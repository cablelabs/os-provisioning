<?php

namespace Modules\ProvBase\Observers;

use File;
use Modules\ProvBase\Entities\Nas;
use Modules\ProvBase\Entities\NetGw;

/**
 * NetGW Observer Class
 * Handles changes on CMTS Gateways
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class NetGwObserver
{
    protected const NETGW_TFTP_PATH = '/tftpboot/cmts';

    public function created($netgw)
    {
        self::updateNas($netgw);

        if ($netgw->type != 'cmts') {
            return;
        }

        if (\Module::collections()->has('ProvMon')) {
            \Artisan::call('nms:cacti', ['--modem-id' => 0, '--netgw-id' => $netgw->id]);
        }

        $netgw->makeDhcpConf();

        File::put(self::NETGW_TFTP_PATH."/$netgw->id.cfg", $netgw->get_raw_netgw_config());
    }

    public function updated($netgw)
    {
        self::updateNas($netgw);

        if ($netgw->type != 'cmts') {
            return;
        }

        $netgw->makeDhcpConf();

        File::put(self::NETGW_TFTP_PATH."/$netgw->id.cfg", $netgw->get_raw_netgw_config());
    }

    public function deleted($netgw)
    {
        self::updateNas($netgw);

        if ($netgw->type != 'cmts') {
            return;
        }

        // v6 function automatically takes care of deletions
        $netgw->makeDhcp6Conf();

        File::delete(NetGw::NETGW_INCLUDE_PATH."/$netgw->id.conf");
        File::delete(self::NETGW_TFTP_PATH."/$netgw->id.cfg");

        NetGw::make_includes();
    }

    /**
     * Handle changes of nas based on netgw
     * This is called on created/updated/deleted in NetGw observer
     *
     * @author Ole Ernst
     */
    private static function updateNas($netgw)
    {
        // netgw is deleted or its type was changed to != bras
        if ($netgw->deleted_at || $netgw->type != 'bras') {
            $netgw->nas()->delete();
            exec('sudo systemctl restart radiusd.service');

            return;
        }

        // we need to use \Request::get() since nas_secret is guarded
        $update = ['nasname' => $netgw->ip, 'secret' => \Request::get('nas_secret')];

        if ($netgw->nas()->count()) {
            $netgw->nas()->update($update);
        } else {
            Nas::insert(array_merge($update, ['shortname' => $netgw->id]));
        }

        exec('sudo systemctl restart radiusd.service');
    }
}
