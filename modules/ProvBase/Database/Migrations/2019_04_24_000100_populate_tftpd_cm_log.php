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
class PopulateTftpdCmLog extends BaseMigration
{
    protected $tablename = '';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo "Populating /var/log/nmsprime/tftpd-cm.log, this may take some minutes. You can track the progress by following the file.\n";

        // only necessary for git installations
        if (! is_dir('/var/log/nmsprime')) {
            mkdir('/var/log/nmsprime', 0755);
            chown('/var/log/nmsprime', 'apache');
            chgrp('/var/log/nmsprime', 'apache');
        }

        shell_exec('for file in /var/log/messages{-*,}; do zgrep "finished cm/cm-" "$file" | while read line; do date -d "$(awk \'{print $1 " " $2 " " $3}\' <<< "$line")" "+%s $(grep -o "Client.*" <<< "$line")"; done; done > /var/log/nmsprime/tftpd-cm.log');
        chmod('/var/log/nmsprime/tftpd-cm.log', 0600);
        chown('/var/log/nmsprime/tftpd-cm.log', 'apache');
        chgrp('/var/log/nmsprime/tftpd-cm.log', 'apache');

        system('systemctl restart rsyslog.service');
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
