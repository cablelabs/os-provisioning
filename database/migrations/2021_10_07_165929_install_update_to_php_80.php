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
class InstallUpdateToPhp80 extends BaseMigration
{
    public $migrationScope = 'system';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tz = date_default_timezone_get();
        foreach (['/etc/php.ini', '/etc/opt/remi/php80/php.ini'] as $file) {
            if (! is_file($file)) {
                continue;
            }

            $str = file_get_contents($file);
            $str = preg_replace('/^;date\.timezone =$/m', "date.timezone = $tz", $str);
            $str = preg_replace('/^memory_limit =.*/m', 'memory_limit = 1024M', $str);
            $str = preg_replace('/^upload_max_filesize =.*/m', 'upload_max_filesize = 100M', $str);
            $str = preg_replace('/^post_max_size =.*/m', 'post_max_size = 100M', $str);
            file_put_contents($file, $str);
        }

        foreach (['/etc/opt/remi/php80/php.d/10-opcache.ini', '/etc/php.d/10-opcache.ini'] as $file) {
            if (! is_file($file)) {
                continue;
            }

            $ini = file_get_contents($file);
            $ini .= ";JIT Compiler\nopcache.jit_buffer_size=100M\nopcache.jit=tracing\n";
            file_put_contents($file, $ini);
        }

        system('systemctl start php80-php-fpm.service');
        system('systemctl enable php80-php-fpm.service');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
