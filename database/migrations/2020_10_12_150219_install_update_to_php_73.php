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

use Illuminate\Support\Facades\Artisan;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Illuminate\Database\Migrations\Migration;

class InstallUpdateToPhp73 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        system('systemctl cat rh-php71-php-fpm.service &>/dev/null && systemctl stop rh-php71-php-fpm.service');
        system('systemctl cat rh-php71-php-fpm.service &>/dev/null && systemctl disable rh-php71-php-fpm.service');

        system('systemctl start rh-php73-php-fpm.service');
        system('systemctl enable rh-php73-php-fpm.service');

        $zone = exec("timedatectl | grep 'Time zone' | cut -d':' -f2 | cut -d' ' -f2");
        exec("sed -e 's|^;date.timezone =.*|date.timezone = {$zone}|' -e 's/^memory_limit =.*/memory_limit = 1024M/' -e 's/^upload_max_filesize =.*/upload_max_filesize = 100M/' -e 's/^post_max_size =.*/post_max_size = 100M/' -i /etc/opt/rh/rh-php73/php.ini");

        Artisan::call('module:v6:migrate');
        Bouncer::refresh();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        system('systemctl stop rh-php73-php-fpm.service');
        system('systemctl disable rh-php73-php-fpm.service');

        system('systemctl start rh-php71-php-fpm.service');
        system('systemctl enable rh-php71-php-fpm.service');

        unlink('/var/www/nmsprime/modules_statuses.json');
    }
}
