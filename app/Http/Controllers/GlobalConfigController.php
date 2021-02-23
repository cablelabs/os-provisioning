<?php

namespace App\Http\Controllers;

use Str;
use Module;
use Bouncer;
use Illuminate\Support\Arr;

class GlobalConfigController extends BaseController
{
    protected $log_level = ['0 - Emergency', '1 - Alert', '2 - Critical', '3 - Error', '4 - Warning', '5 - Notice', '6 - Info', '7 - Debug'];

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        return [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'ISP Name'],
            ['form_type' => 'text', 'name' => 'street', 'description' => 'Street'],
            ['form_type' => 'text', 'name' => 'city', 'description' => 'City'],
            ['form_type' => 'text', 'name' => 'phone', 'description' => 'Phonenumber'],
            ['form_type' => 'text', 'name' => 'mail', 'description' => 'E-Mail Address'],
            ['form_type' => 'select', 'name' => 'log_level', 'description' => 'System Log Level', 'value' => $this->log_level, 'hidden' => 1],
            ['form_type' => 'text', 'name' => 'passwordResetInterval', 'description' => 'Password Reset Interval', 'help' => trans('helper.PasswordReset')],
            ['form_type' => 'text', 'name' => 'headline1', 'description' => 'Headline 1'],
            ['form_type' => 'text', 'name' => 'headline2', 'description' => 'Headline 2'],
            ['form_type' => 'text', 'name' => 'default_country_code', 'description' => 'Default country code', 'help' => trans('helper.ISO_3166_ALPHA-2')],
            ['form_type' => 'text', 'name' => 'alert1', 'description' => trans('view.Global notification').' - '.trans('view.info')],
            ['form_type' => 'text', 'name' => 'alert2', 'description' => trans('view.Global notification').' - '.trans('view.warning')],
            ['form_type' => 'text', 'name' => 'alert3', 'description' => trans('view.Global notification').' - '.trans('view.critical')],
        ];
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
        $base_controller = new $tmp;

        $view_header = BaseViewController::translate_view('Global Configurations', 'Header');
        $route_name = 'Config.index';

        $enabled = Module::allEnabled();
        $moduleControllers = [0 => $this];
        $moduleModels = [0 => static::get_model_obj()->first()];

        $links = [0 => ['name' => 'Global Config',
            'link' => 'GlobalConfig', ],
        ];

        $i = 1;
        foreach ($enabled as $module) {
            $mod_path = explode('/', $module->getPath());
            $tmp = end($mod_path);

            $modControllerName = 'Modules\\'.$tmp.'\\Http\\Controllers\\'.$tmp.'Controller';
            $modModelNamespace = 'Modules\\'.$tmp.'\\Entities\\'.$tmp;
            $modController = new $modControllerName;

            if (method_exists($modController, 'view_form_fields') &&
                Bouncer::can('view', $modModelNamespace)) {
                $modModel = new $modModelNamespace;

                $moduleControllers[$i] = $modController;
                $moduleModels[$i] = $modModel->first();

                Arr::set($links, $i.'.name', trans("view.$tmp"));
                Arr::set($links, $i.'.link', $tmp);

                $i++;
            }
        }

        // Add SLA Tab
        if (Bouncer::can('view', '\App\Sla')) {
            $moduleControllers[$i] = new \App\Http\Controllers\SlaController;
            $sla_model = new \App\Sla;
            $moduleModels[$i] = $sla_model->first();
            $links[$i] = ['name' => 'SLA', 'link' => 'Sla'];
            $i++;
        }

        foreach ($moduleControllers as $key => $controller) {
            $fields[$key] = BaseViewController::prepare_form_fields($controller->view_form_fields($moduleModels[$key]), $moduleModels[$key]);
            $form_fields[$key] = BaseViewController::add_html_string($fields[$key], 'edit');
        }

        return \View::make('GlobalConfig.index', $base_controller->compact_prep_view(compact('links', 'view_header', 'route_name', 'moduleModels', 'form_fields')));
    }
}
