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
class InstallUpdate254To300 extends BaseMigration
{
    protected $tablename = '';
    public $migrationScope = 'system';

    /**
     * Restart GenieACS & nmsprimed after configuration has changed from NMS Prime version 2.5.4 to 3.0.0
     * Remove sessions to rebuild cached sidebar
     *
     * @return void
     */
    public function up()
    {
        exec('systemctl reenable genieacs-{cwmp,fs,nbi,ui}');
        exec('systemctl restart genieacs-{cwmp,fs,nbi,ui}');
        exec('systemctl restart httpd');

        exec('systemctl daemon-reload');
        exec('systemctl restart nmsprimed');

        \Artisan::call('nms:radgroupreply-repopulate');

        exec('rm -f storage/framework/sessions/*');
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
