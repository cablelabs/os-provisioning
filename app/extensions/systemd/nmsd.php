<?php

/**
 * This Deamon proofs generically if a file for all specified services exist that indicates that the service has to be restarted
 * @author Nino Ryschawy
 */

/* Don't forget to insert all needed services here! */
$services = ['dhcpd'];

// contains the restart-indicating files
// TODO: use laravel path config
$dir = '/var/www/nmsprime/storage/systemd/';

// contains restart scripts
$dir_scripts = '/var/www/nmsprime/app/extensions/systemd/';

$i = 0;

while (1) {
    foreach ($services as $service) {
        // proof if indication file exists, if so execute the restart script
        if (file_exists($dir.$service)) {
            if (! file_exists($dir_scripts.$service.'.php')) {
                // TODO: print to log file
                // echo 'Error: service restart script not found!';
                continue;
            }

            // proof if this script is already/still running
            if (! exec("ps -aux | grep $service.php | grep -v grep")) {	// when nothing is returned the script isnt running
                unlink($dir.$service);
                // echo "restarted $service";
                exec('php -f '.$dir_scripts.$service.'.php &>/dev/null &');
            }
        }
    }

    sleep(4);
}
