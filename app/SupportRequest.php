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

namespace App;

use Nwidart\Modules\Facades\Module;

class SupportRequest extends BaseModel
{
    public $table = 'supportrequest';

    public function rules()
    {
        return [
            'mail' => 'email',
            // 'license' => '',
        ];
    }

    public static function view_headline(): string
    {
        return 'Support Request';
    }

    // View Icon
    public static function view_icon(): string
    {
        return '<i class="fa fa-user-circle text-info"></i>';
    }

    // link title in index view
    public function view_index_label()
    {
        return 'Support Request';
    }

    /**
     * BOOT - init SupportRequestObserver
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new \App\Observers\SupportRequestObserver);
        self::observe(new \App\Observers\SystemdObserver);
    }

    /**
     * Get System state in preparation of support request
     *
     * @return array
     */
    public static function system_status()
    {
        $out = [];
        $services = ['dhcpd', 'xinetd', 'ntpd', 'named', 'firewalld'];
        foreach ($services as $service) {
            exec("systemctl status $service", $out[$service]);
        }

        // routes, ip
        exec('/usr/sbin/ip r', $out['routes']);
        exec('/usr/sbin/ip a', $out['ip']);

        if (! Module::collections()->has(['Dashboard', 'ProvBase'])) {
            return $out;
        }

        // thumb of modems
        if (\Module::collections()->has('Dashboard')) {
            $modemStatistics = \Modules\Dashboard\Http\Controllers\DashboardController::get_modem_statistics();
        }

        if (! isset($modemStatistics) || ! $modemStatistics) {
            return $out;
        }

        $out['modem_statistic'] = $modemStatistics;
        foreach (['text', 'state', 'fa'] as $key) {
            unset($out['modem_statistic']->$key);
        }

        return $out;
    }
}
