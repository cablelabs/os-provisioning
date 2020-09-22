<?php

namespace App\Http\Controllers;

class AppController extends BaseController
{
    /**
     * Returns view of all installed/not installed apps.
     *
     * @author Roy Schneider
     * @return View
     */
    public function showApps()
    {
        $modules = \Route::currentRouteName() == 'Apps.active' ? \Module::enabled() : \Module::disabled();
        $apps = $this->getApps($modules);
        $tabs = $this->prepareTabs();

        return \View::make('Apps.index', $this->compact_prep_view(compact('apps', 'tabs')));
    }

    /**
     * Create array of enabled/disabled modules.
     *
     * @author Roy Schneider
     * @param  Nwidart\Modules $installed
     * @return array $apps
     */
    public function getApps($installed)
    {
        $apps = [];
        foreach ($installed as $module) {
            $icon = $module->icon;
            if (is_file('/var/www/nmsprime/public/images/apps/'.$icon)) {
                $state = $module->active() ? trans('messages.active_apps') : trans('messages.inactive_apps');
                $apps[$state][$module->category][] = ['name' => $module->alias, 'icon' => $icon, 'description' => $module->description];
            }
        }

        return $apps;
    }

    /**
     * Create array of enabled/disabled modules.
     *
     * @author Roy Schneider
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
