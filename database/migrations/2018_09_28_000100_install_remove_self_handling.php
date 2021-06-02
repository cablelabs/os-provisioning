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
class InstallRemoveSelfHandling extends BaseMigration
{
    protected $tablename = '';

    protected $files = [
        '/modules/BillingBase/Console/accountingCommand.php',
        '/modules/HfcCustomer/Console/MpsCommand.php',
        '/modules/ProvBase/Console/ConfigfileCommand.php',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->files as $file) {
            $file = base_path().$file;
            if (! file_exists($file)) {
                continue;
            }

            $str = file_get_contents($file);
            $str = preg_replace(
                "/use Illuminate\\\\Console\\\\Command;\nuse Illuminate\\\\Contracts\\\\Bus\\\\SelfHandling;/",
                'use Illuminate\\Console\\Command;',
                $str
            );
            $str = preg_replace('/implements SelfHandling, ShouldQueue/', 'implements ShouldQueue', $str);
            file_put_contents($file, $str);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->files as $file) {
            $file = base_path().$file;
            if (! file_exists($file)) {
                continue;
            }

            $str = file_get_contents($file);
            $str = preg_replace(
                '/use Illuminate\\\\Console\\\\Command;/',
                "use Illuminate\\Console\\Command;\nuse Illuminate\\Contracts\\Bus\\SelfHandling;",
                $str
            );
            $str = preg_replace('/implements ShouldQueue/', 'implements SelfHandling, ShouldQueue', $str);
            file_put_contents($file, $str);
        }
    }
}
