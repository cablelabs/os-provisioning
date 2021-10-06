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
 * This Deamon proofs generically if a file for all specified services exist that indicates that the service has to be restarted
 *
 * @author Nino Ryschawy
 */

// contains the restart-indicating files
// TODO: use laravel path config
$dir = '/var/www/nmsprime/storage/systemd/';

// contains restart scripts
$dir_scripts = '/var/www/nmsprime/app/extensions/systemd/';

// time to wait for same kind of requests to be merged into one request
$delay = 10;

while (1) {
    $services = glob("$dir/*");

    foreach ($services as $service) {
        clearstatcache();
        $mtime = filemtime($service);
        $service = basename($service);

        if (! file_exists($dir_scripts.$service.'.php') || (time() - $mtime < $delay)) {
            continue;
        }

        // proof if this script is already/still running
        if (! exec("ps -aux | grep $service.php | grep -v grep")) {	// when nothing is returned the script isnt running
            unlink($dir.$service);

            exec('/opt/remi/php80/root/usr/bin/php -f '.$dir_scripts.$service.'.php &>/dev/null &');
        }
    }

    sleep(1);
}
