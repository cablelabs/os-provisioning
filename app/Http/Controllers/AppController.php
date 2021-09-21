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

namespace App\Http\Controllers;

class AppController extends BaseController
{
    /**
     * Returns view of all installed/not installed apps.
     *
     * @author Roy Schneider
     *
     * @return View
     */
    public function showApps()
    {
        $modules = \Route::currentRouteName() == 'Apps.active' ? \Module::allEnabled() : \Module::allDisabled();
        $nativeApps = $this->getNativeApps($modules);
        $tabs = $this->prepareTabs();
        $externalApps = $this->getExternalApps();

        return \View::make('Apps.index', $this->compact_prep_view(compact('nativeApps', 'externalApps', 'tabs')));
    }

    /**
     * Create array of enabled/disabled modules.
     *
     * @author Roy Schneider
     *
     * @param  Nwidart\Modules  $installed
     * @return array $apps
     */
    public function getNativeApps($installed)
    {
        $apps = [];
        foreach ($installed as $module) {
            $icon = $module->get('icon');
            if (is_file(public_path('images/apps/').$icon)) {
                $state = $module->isEnabled() ? 'active' : 'inactive';
                $apps[$state][$module->get('category')][] = [
                    'name' => $module->get('alias'),
                    'icon' => $icon,
                    'description' => $module->get('description'),
                    'link' => $this->getAppLink($module, $state),
                ];
            }
        }

        return $apps;
    }

    /**
     * Generate link to dashboard of the Module or to nmsprime.com.
     *
     * @author Roy Schneider
     *
     * @param  Nwidart\Modules  $installed
     * @param  string  $state
     * @return string $route
     */
    private function getAppLink($module, $state)
    {
        $link = $module->getLowerName().'.link';
        $route = 'https://www.nmsprime.com/'.$module->get('category').'-apps/#'.\Str::lower(str_replace('.png', '', $module->get('icon')));

        if ($state == 'active') {
            $route = config()->has($link) ? route(config()->get($link)) : '#';
        }

        return $route;
    }

    /**
     * Create array of enabled/disabled modules.
     *
     * @author Roy Schneider
     *
     * @return array $tabs
     */
    public function prepareTabs()
    {
        $tabs = [['name' => 'Manage apps', 'icon' => 'cogs', 'route' => 'Apps.active', 'link' => []],
            ['name' => 'Search new apps', 'icon' => 'plus', 'route' => 'Apps.inactive', 'link' => []],
        ];

        return $tabs;
    }
}
