<?php

/**
 * Helper to log something to /var/www/nmsprime/storage/logs/laravel.log
 *
 * @author Patrick Reichel
 */
function laralog($msg, $level='INFO') {
    $logfile = '/var/www/nmsprime/storage/logs/laravel.log';
    $levels = [
        'CRITICAL',
        'DEBUG',
        'ERROR',
        'INFO',
        'NOTICE',
        'WARNING',
    ];

    if (! file_exists($logfile)) {
        syslog(LOG_ERR, $logfile.' does not exist ['.__FILE__.']');
        syslog(LOG_ERR, "Could not log '$msg' of level '$level'");
        return;
    }

    if (! in_array($level, $levels)) {
        file_put_contents($logfile, '['.date('Y-m-d H:i:s')."] local.ERROR: Unknown log level '$level' given – changing to ERROR\n", FILE_APPEND);
        $level = 'ERROR';
    }

    file_put_contents($logfile, '['.date('Y-m-d H:i:s')."] local.$level: $msg\n", FILE_APPEND);

}
