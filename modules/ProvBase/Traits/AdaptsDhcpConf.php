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

namespace Modules\ProvBase\Traits;

trait AdaptsDhcpConf
{
    /**
     * Validate DHCPD config and set session key to display error in GUI
     *
     * @param string IP version 4 or 6
     *
     * @author Nino Ryschawy
     */
    public static function validateDhcpConfig($version)
    {
        if ($version == '4') {
            self::validateDhcp4Config();
        }

        if ($version == '6') {
            self::validateDhcp6Config();
        }
    }

    /**
     * Validate DHCPd config
     * This is called on created/updated/deleted in IpPoolObserver and EndpointObserver
     *
     * @author Ole Ernst
     */
    public static function validateDhcp4Config()
    {
        exec('/usr/sbin/dhcpd -t -cf /etc/dhcp-nmsprime/dhcpd.conf &>/dev/null', $out, $ret);

        if ($ret) {
            \Session::push('tmp_error_above_form', trans('messages.dhcpValidationError'));
        }
    }

    /**
     * Validate Kea DHCPD config
     * used in IpPoolObserver and EndpointObserver
     *
     * @author Nino Ryschawy
     */
    public static function validateDhcp6Config()
    {
        exec('/usr/sbin/kea-dhcp6 -t /etc/kea/dhcp6-nmsprime.conf &>/dev/null', $out, $ret);

        if ($ret) {
            \Session::push('tmp_error_above_form', trans('messages.dhcp6ValidationError'));
        }
    }
}
