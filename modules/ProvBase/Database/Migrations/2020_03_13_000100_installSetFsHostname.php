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
class InstallSetFsHostname extends BaseMigration
{
    protected $tablename = '';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // GenieACS > 1.1.3 uses environment variables instead of config.json
        return;

        $filename = '/lib/node_modules/genieacs/config/config.json';
        $conf = json_decode(file_get_contents($filename));

        $host = explode('.', gethostname())[0];
        $domain = \Modules\ProvBase\Entities\ProvBase::first()->domain_name;
        $conf->FS_HOSTNAME = "$host.$domain";

        file_put_contents($filename, json_encode($conf));
        exec('systemctl restart genieacs-fs.service');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // GenieACS > 1.1.3 uses environment variables instead of config.json
        return;

        $filename = '/lib/node_modules/genieacs/config/config.json';
        $conf = json_decode(file_get_contents($filename));

        unset($conf->FS_HOSTNAME);

        file_put_contents($filename, json_encode($conf));
        exec('systemctl restart genieacs-fs.service');
    }
}
