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
 * Helper to log something to /var/www/nmsprime/storage/logs/laravel.log
 *
 * @author Patrick Reichel
 */
function laralog($msg, $level = 'INFO')
{
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
