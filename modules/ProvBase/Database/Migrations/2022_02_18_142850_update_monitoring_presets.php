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

use Database\Migrations\BaseMigration;
use Modules\ProvBase\Entities\Configfile;
use Modules\ProvBase\Entities\Modem;

class UpdateMonitoringPresets extends BaseMigration
{
    public $migrationScope = 'system';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $monPresets = array_filter((array) json_decode(Modem::callGenieAcsApi('presets', 'GET'), true), function ($preset) {
            return isset($preset['events']['2 PERIODIC']);
        });

        // delete all monitoring presets
        foreach ($monPresets as $monPreset) {
            Modem::callGenieAcsApi("presets/{$monPreset['_id']}", 'DELETE');
        }

        $type = 'tr069';
        $modemQuery = Modem::join('configfile', 'configfile.id', 'modem.configfile_id')
            ->where('configfile.device', $type)
            ->whereNull('configfile.deleted_at')
            ->select('modem.*');

        // recreate all monitoring presets
        Configfile::build_configfiles($modemQuery, $type);
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
