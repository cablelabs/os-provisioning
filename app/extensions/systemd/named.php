<?php
/**
 * Restarts named server after syntax check
 */

require_once('/var/www/nmsprime/app/extensions/systemd/laralog.php');

$configfile = '/etc/named-nmsprime.conf';

// lock configfile
$fp = fopen($configfile, 'r');
if (! flock($fp, LOCK_EX)) {
    laralog('named configfiles are locked – cannot restart', 'CRITICAL');
} else {

    // check config
    exec('/usr/sbin/named-checkconf '.$configfile, $out, $ret);
    if ($ret > 0) {
        laralog("Cannot restart named – /usr/sbin/named-checkconf $configfile failed", 'CRITICAL');
    } else {
        laralog('Restarting named', 'INFO');
        exec('systemctl restart named.service', $out, $ret);
        if ($ret > 0) {
            laralog('Restarting named via systemctl failed', 'CRITICAL');
        }
    }
    flock($fp, LOCK_UN);
}

fclose($fp);

