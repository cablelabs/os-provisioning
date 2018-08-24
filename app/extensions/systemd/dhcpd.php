<?php
/**
 * Restarts the dhcpd server after checking for correct syntax of the config
 */

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
        if (file_exists($logfile)) {
            file_put_contents($logfile, '['.date('Y-m-d H:i:s')."] local.ERROR: DHCP Configfiles are locked\n", FILE_APPEND);
        }
    }

    // Log
    if (file_exists($logfile)) {
        file_put_contents($logfile, '['.date('Y-m-d H:i:s')."] local.INFO: Restart DHCPD\n", FILE_APPEND);
    } else {
        syslog(LOG_ERR, $logfile.' does not exist ['.__FILE__.']');
    }

    exec('systemctl restart dhcpd');

    flock($fp_cm, LOCK_UN);
    fclose($fp_cm);
    flock($fp_mta, LOCK_UN);
    fclose($fp_mta);
}
