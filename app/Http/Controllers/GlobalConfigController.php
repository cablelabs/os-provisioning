<?php

namespace App\Http\Controllers;

use Str;
use Module;
use Bouncer;

class GlobalConfigController extends BaseController
{
    protected $log_level = ['0 - Emergency', '1 - Alert', '2 - Critical', '3 - Error', '4 - Warning', '5 - Notice', '6 - Info', '7 - Debug'];

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        // label has to be the same like column in sql table
        return [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'ISP Name'],
            ['form_type' => 'text', 'name' => 'street', 'description' => 'Street'],
            ['form_type' => 'text', 'name' => 'city', 'description' => 'City'],
            ['form_type' => 'text', 'name' => 'phone', 'description' => 'Phonenumber'],
            ['form_type' => 'text', 'name' => 'mail', 'description' => 'E-Mail Address'],

            ['form_type' => 'select', 'name' => 'log_level', 'description' => 'System Log Level', 'value' => $this->log_level, 'hidden' => 1],
            ['form_type' => 'text', 'name' => 'headline1', 'description' => 'Headline 1'],
            ['form_type' => 'text', 'name' => 'headline2', 'description' => 'Headline 2'],
            ['form_type' => 'text', 'name' => 'default_country_code', 'description' => 'Default country code', 'help' => trans('helper.ISO_3166_ALPHA-2')],
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

        $enabled = Module::enabled();
        $module_controller = [0 => $this];
        $module_model = [0 => static::get_model_obj()->first()];

        $links = [0 => ['name' => 'Global Config',
                        'link' => 'GlobalConfig', ],
                    ];
        $i = 1;

        foreach ($enabled as $module) {
            $mod_path = explode('/', $module->getPath());
            $tmp = end($mod_path);

            $mod_controller_name = 'Modules\\'.$tmp.'\\Http\\Controllers\\'.$tmp.'Controller';
            $mod_model_namespace = 'Modules\\'.$tmp.'\\Entities\\'.$tmp;
            $mod_controller = new $mod_controller_name;

            if (method_exists($mod_controller, 'view_form_fields') &&
                Bouncer::can('view', $mod_model_namespace)) {
                $mod_model = new $mod_model_namespace;

                $module_controller[$i] = $mod_controller;
                $module_model[$i] = $mod_model->first();

                array_set($links, $i.'.name', (($module->get('description') == '') ? $tmp : $module->get('description')));
                array_set($links, $i.'.link', $tmp);

                $i++;
            }
        }

        // Add SLA Tab
        if (Bouncer::can('view', '\App\Sla')) {
            $module_controller[$i] = new \App\Http\Controllers\SlaController;
            $sla_model = new \App\Sla;
            $module_model[$i] = $sla_model->first();
            $links[$i] = ['name' => 'SLA', 'link' => 'Sla'];
            $i++;
        }

        for ($j = 0; $j < $i; $j++) {
            $fields[$j] = BaseViewController::prepare_form_fields($module_controller[$j]->view_form_fields($module_model[$j]), $module_model[$j]);
            $form_fields[$j] = BaseViewController::add_html_string($fields[$j], 'edit');
        }

        return \View::make('GlobalConfig.index', $base_controller->compact_prep_view(compact('links', 'view_header', 'route_name', 'module_model', 'form_fields')));
    }
}
