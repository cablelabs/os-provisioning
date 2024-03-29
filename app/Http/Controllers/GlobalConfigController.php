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

use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use Silber\Bouncer\BouncerFacade as Bouncer;

class GlobalConfigController extends BaseController
{
    protected $log_level = ['0 - Emergency', '1 - Alert', '2 - Critical', '3 - Error', '4 - Warning', '5 - Notice', '6 - Info', '7 - Debug'];

    public const BG_IMAGES_PATH_REL = 'public/base/bg-images/';

    protected function getFileUploadPaths(): array
    {
        return [
            'login_img' => self::BG_IMAGES_PATH_REL,
        ];
    }

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        $pictures = $this->getFilesForSelect(self::BG_IMAGES_PATH_REL);

        $ret = [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'ISP Name'],
            ['form_type' => 'text', 'name' => 'street', 'description' => 'Street'],
            ['form_type' => 'text', 'name' => 'city', 'description' => 'City'],
            ['form_type' => 'text', 'name' => 'phone', 'description' => 'Phonenumber'],
            ['form_type' => 'text', 'name' => 'mail', 'description' => 'E-Mail Address'],
            ['form_type' => 'select', 'name' => 'log_level', 'description' => 'System Log Level', 'value' => $this->log_level, 'hidden' => 1],
            ['form_type' => 'text', 'name' => 'password_reset_interval', 'description' => 'Password Reset Interval', 'help' => trans('helper.PasswordReset')],
            ['form_type' => 'text', 'name' => 'headline1', 'description' => 'Headline 1'],
            ['form_type' => 'text', 'name' => 'headline2', 'description' => 'Headline 2'],
            ['form_type' => 'text', 'name' => 'default_country_code', 'description' => 'Default country code', 'help' => trans('helper.ISO_3166_ALPHA-2')],
            ['form_type' => 'text', 'name' => 'alert1', 'description' => trans('view.Global notification').' - '.trans('view.info')],
            ['form_type' => 'text', 'name' => 'alert2', 'description' => trans('view.Global notification').' - '.trans('view.warning')],
            ['form_type' => 'text', 'name' => 'alert3', 'description' => trans('view.Global notification').' - '.trans('view.critical')],

            ['form_type' => 'select', 'name' => 'login_img', 'description' => trans('view.loginImg'), 'value' => $pictures],
            ['form_type' => 'file', 'name' => 'login_img_upload', 'description' => trans('view.loginImgUpload')],
        ];

        if (Module::collections()->has('HfcBase')) {
            $ret[] = ['form_type' => 'checkbox', 'name' => 'is_all_nets_sidebar_enabled', 'description' => 'isAllNetsSidebarEnabled'];
        }

        return $ret;
    }

    /**
     * @author Patrick Reichel
     */
    public function prepare_input($data)
    {
        // ISO 3166 country codes are uppercase
        $data['default_country_code'] = Str::upper($data['default_country_code']);

        $data = parent::prepare_input($data);

        return $data;
    }

    /**
     * Returns Global Config Index Page with links to the configurable Modules
     *
     * @author Nino Ryschawy
     * @author Christian Schramm
     */
    public function index()
    {
        $tmp = get_parent_class();
        $base_controller = new $tmp();

        $view_header = BaseViewController::translate_view('Global Configurations', 'Header');
        $route_name = 'Config.index';

        $enabled = Module::allEnabled();
        $slug = Str::slug('Global Config', '_');
        $moduleControllers[$slug] = $this;
        $moduleModels[$slug] = static::get_model_obj()->first();

        $links[$slug] = [
            'name' => $slug,
            'local' => 'Global Config',
            'link' => 'GlobalConfig',
        ];

        foreach ($enabled as $module) {
            $mod_path = explode('/', $module->getPath());
            $tmp = end($mod_path);

            $modControllerName = 'Modules\\'.$tmp.'\\Http\\Controllers\\'.$tmp.'Controller';
            $modModelNamespace = 'Modules\\'.$tmp.'\\Entities\\'.$tmp;
            $modController = new $modControllerName();

            if (method_exists($modController, 'view_form_fields') &&
                Bouncer::can('view', $modModelNamespace)) {
                $modModel = new $modModelNamespace();
                $slug = Str::slug(trans("view.$tmp"), '_');

                $moduleControllers[$slug] = $modController;
                $moduleModels[$slug] = $modModel->first();

                $links[$slug] = [
                    'name' => $slug,
                    'local' => trans("view.$tmp"),
                    'link' => $tmp,
                ];
            }
        }

        // Add SLA Tab
        if (Bouncer::can('view', '\App\Sla')) {
            $slug = Str::lower('SLA');
            $moduleControllers[$slug] = new \App\Http\Controllers\SlaController();
            $sla_model = new \App\Sla();
            $moduleModels[$slug] = $sla_model->first();
            $links[$slug] = [
                'name' => $slug,
                'local' => 'SLA',
                'link' => 'Sla',
            ];
        }

        $firstTab = session('tab.Config', $links['global_config']['name']);

        foreach ($moduleControllers as $key => $controller) {
            $fields[$key] = BaseViewController::prepare_form_fields($controller->view_form_fields($moduleModels[$key]), $moduleModels[$key]);
            $form_fields[$key] = BaseViewController::add_html_string($fields[$key], 'edit');
        }

        return View::make('GlobalConfig.index', $base_controller->compact_prep_view(compact('links', 'view_header', 'route_name', 'moduleModels', 'form_fields', 'firstTab')));
    }
}
