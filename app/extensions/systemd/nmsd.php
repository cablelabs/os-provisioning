<?php

/**
 * This Deamon proofs generically if a file for all specified services exist that indicates that the service has to be restarted
 * @author Nino Ryschawy
 */

// contains the restart-indicating files
// TODO: use laravel path config
$dir = '/var/www/nmsprime/storage/systemd/';

// contains restart scripts
$dir_scripts = '/var/www/nmsprime/app/extensions/systemd/';

while (1) {

    $services = glob("$dir/*");

    foreach ($services as $service) {
        $service = basename($service);

        if (! file_exists($dir_scripts.$service.'.php')) {
            file_put_contents($logfile, '['.date('Y-m-d H:i:s')."] local.Error: Restart script for service $service not found! (nmsd.php)\n", FILE_APPEND);

            continue;
        }

        // proof if this script is already/still running
        if (! exec("ps -aux | grep $service.php | grep -v grep")) {	// when nothing is returned the script isnt running
            unlink($dir.$service);

            exec('php -f '.$dir_scripts.$service.'.php &>/dev/null &');
        }
    }

    sleep(4);
}
