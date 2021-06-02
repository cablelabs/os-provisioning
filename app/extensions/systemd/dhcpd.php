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

/**
 * Restarts the dhcpd server after checking for correct syntax of the config
 */
require_once '/var/www/nmsprime/app/extensions/systemd/laralog.php';

// check config
exec('dhcpd -t -cf /etc/dhcp-nmsprime/dhcpd.conf &>/dev/null', $out, $ret);

// restart server
if ($ret == 0) {
    // only restart when dhcpd configfiles are not locked
    $fn_cm = '/etc/dhcp-nmsprime/modems-host.conf';
    $fn_mta = '/etc/dhcp-nmsprime/mta.conf';

    $fp_cm = fopen($fn_cm, 'r');
    $fp_mta = fopen($fn_mta, 'r');

    $logfile = '/var/www/nmsprime/storage/logs/laravel.log';

    if (! flock($fp_cm, LOCK_EX) || ! flock($fp_mta, LOCK_EX)) {
        // Note: This should never occure as flock waits until file is unlocked from other process
        laralog('DHCP Configfiles are locked', 'ERROR');
    }

    laralog('Restart dhcpd.service', 'INFO');
    exec('systemctl restart dhcpd');

    flock($fp_cm, LOCK_UN);
    fclose($fp_cm);
    flock($fp_mta, LOCK_UN);
    fclose($fp_mta);
}
