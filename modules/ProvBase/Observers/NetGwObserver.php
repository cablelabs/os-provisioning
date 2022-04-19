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
            \Artisan::call('nms:cacti', ['--netgw-id' => $netgw->id]);
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
    public static function updateNas($netgw)
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
