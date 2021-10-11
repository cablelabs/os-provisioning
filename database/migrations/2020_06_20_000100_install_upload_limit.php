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
class InstallUploadLimit extends BaseMigration
{
    protected $tablename = '';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // exec("sed -e 's/^upload_max_filesize =.*/upload_max_filesize = 100M/' -e 's/^post_max_size =.*/post_max_size = 100M/' -i /etc/{,opt/rh/rh-php73/}php.ini");
        // exec('systemctl restart rh-php73-php-fpm.service');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // exec("sed -e 's/^upload_max_filesize =.*/upload_max_filesize = 50M/' -e 's/^post_max_size =.*/post_max_size = 50M/' -i /etc/{,opt/rh/rh-php73/}php.ini");
        // exec('systemctl restart rh-php73-php-fpm.service');
    }
}
