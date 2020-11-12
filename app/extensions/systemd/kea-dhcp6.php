<?php
/**
 * Restarts the ISC kea-dhcp6 server after checking for correct syntax of the config
 */

// check config
// Check if this works
exec('kea-dhcp6 -t /etc/kea/dhcp6-nmsprime.conf &>/dev/null', $out, $ret);

// restart server
if ($ret != 0) {
    return;
}

// only restart when dhcpd configfiles are not locked
$fn_cm = '/etc/kea/gateways6.conf';
$fn_mta = '/etc/kea/hosts6.conf';

$fp_cm = fopen($fn_cm, 'r');
$fp_mta = fopen($fn_mta, 'r');

$logfile = '/var/www/nmsprime/storage/logs/laravel.log';

if (! flock($fp_cm, LOCK_EX) || ! flock($fp_mta, LOCK_EX)) {
    // Note: This should never occure as flock waits until file is unlocked from other process
    if (file_exists($logfile)) {
        file_put_contents($logfile, '['.date('Y-m-d H:i:s')."] local.ERROR: Kea DHCP Configfiles are locked\n", FILE_APPEND);
    }
}

// Log
if (file_exists($logfile)) {
    file_put_contents($logfile, '['.date('Y-m-d H:i:s')."] local.INFO: Restart kea-dhcp6.service\n", FILE_APPEND);
} else {
    syslog(LOG_ERR, $logfile.' does not exist ['.__FILE__.']');
}

exec('systemctl restart kea-dhcp6');

flock($fp_cm, LOCK_UN);
fclose($fp_cm);
flock($fp_mta, LOCK_UN);
fclose($fp_mta);
