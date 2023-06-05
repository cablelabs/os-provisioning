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

namespace Modules\ProvBase\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\ProvBase\Services\FirmwareUpgradeService;

class UpgradeFirmwareCommand extends Command
{
    protected $name = 'firmware:icingadata';

    protected $signature = 'firmware:upgrade';

    protected $description = 'Upgrade modems firmware according to active firmware upgrades schedule';

    protected $firmwareUpgradeService;

    public function __construct(FirmwareUpgradeService $firmwareUpgradeService)
    {
        parent::__construct();
        $this->firmwareUpgradeService = $firmwareUpgradeService;
    }

    public function handle()
    {
        Log::info('Firmware upgrade process has been started.');
        $this->firmwareUpgradeService->upgradeFirmware();
        Log::info('Firmware upgrade process has been done successfully.');
    }
}
