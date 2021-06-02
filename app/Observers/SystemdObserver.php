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

namespace App\Observers;

/**
 * Systemd Observer Class - Handles changes on Model Gateways - restarts system services
 *
 * TODO:
 * place it somewhere else ...
 * Calling this Observer is practically very bad in case there are more services inserted - then all services will restart even
 *		if Config didn't change - therefore a distinction is necessary - or more Observers,
 * another Suggestion:
 * place the restart file creation in the appropriate observer itself
 * only place a static function restart_dhcpd here that creates the file
 */
class SystemdObserver
{
    // insert all services that need to be restarted after a model changed there configuration in that array
    private $services = ['dhcpd', 'kea-dhcp6'];

    public function created($model)
    {
        \Log::debug('systemd: observer called from create context');

        if (! is_dir(storage_path('systemd'))) {
            mkdir(storage_path('systemd'));
        }

        foreach ($this->services as $service) {
            touch(storage_path('systemd/'.$service));
        }
    }

    public function updated($model)
    {
        if (! $model->observer_enabled) {
            return;
        }

        // Exception - Dont restart dhcp server for modems where no relevant changes where made
        $model_name = new \ReflectionClass(get_class($model));
        $model_name = $model_name->getShortName();

        if ($model_name == 'Modem' && ! $model->needs_restart()) {
            return;
        }

        \Log::debug('systemd: observer called from update context', [$model_name, $model->id]);

        if (! is_dir(storage_path('systemd'))) {
            mkdir(storage_path('systemd'));
        }

        foreach ($this->services as $service) {
            touch(storage_path('systemd/'.$service));
        }
    }

    public function deleted($model)
    {
        \Log::debug('systemd: observer called from delete context');

        if (! is_dir(storage_path('systemd'))) {
            mkdir(storage_path('systemd'));
        }

        foreach ($this->services as $service) {
            touch(storage_path('systemd/'.$service));
        }
    }
}
