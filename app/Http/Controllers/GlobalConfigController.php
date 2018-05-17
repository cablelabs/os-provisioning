<?php

namespace App\Http\Controllers;

use Module;

class GlobalConfigController extends BaseController {

	protected $log_level = ['0 - Emergency', '1 - Alert', '2 - Critical', '3 - Error', '4 - Warning', '5 - Notice', '6 - Info', '7 - Debug'];

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'ISP Name'),
			array('form_type' => 'text', 'name' => 'street', 'description' => 'Street'),
			array('form_type' => 'text', 'name' => 'city', 'description' => 'City'),
			array('form_type' => 'text', 'name' => 'phone', 'description' => 'Phonenumber'),
			array('form_type' => 'text', 'name' => 'mail', 'description' => 'E-Mail Address'),

			array('form_type' => 'select', 'name' => 'log_level', 'description' => 'System Log Level', 'value' => $this->log_level, 'hidden' => 1),
			array('form_type' => 'text', 'name' => 'headline1', 'description' => 'Headline 1'),
			array('form_type' => 'text', 'name' => 'headline2', 'description' => 'Headline 2'),
			array('form_type' => 'text', 'name' => 'default_country_code', 'description' => 'Default country code', 'help' => 'ISO 3166 ALPHA-2 (two characters)'),
			);
	}


	/**
	 * @author Patrick Reichel
	 */
	public function prepare_input($data) {

		// ISO 3166 country codes are uppercase
		$data['default_country_code'] = \Str::upper($data['default_country_code']);

		$data = parent::prepare_input($data);
		return $data;
	}


	/**
	 * Returns Global Config Index Page with links to the configurable Modules
	 *
	 * @author Nino Ryschawy, Christian Schramm
	 */
	public function index()
	{
        $tmp = get_parent_class();
		$base_controller = new $tmp;

        $view_header = BaseViewController::translate_view("Global Configurations", 'Header');
		$route_name = 'Config.index';

		$enabled = \Module::enabled();
		$module_controller = [0 => $this];
		$module_model = [0 => static::get_model_obj()->first()];

		$links = [0 => ['name' => 'Global Config',
						'link' => 'GlobalConfig']
					];
		$i = 1;
		foreach ($enabled as $module) {
			$mod_path = explode('/', $module->getPath());
			$tmp = end($mod_path);

			$mod_controller_name = 'Modules\\' . $tmp . '\\Http\\Controllers\\' . $tmp . 'Controller';
			$mod_controller = new $mod_controller_name;

			if (method_exists($mod_controller, 'view_form_fields')) {
				$mod_model_namespace = 'Modules\\' . $tmp . '\\Entities\\' . $tmp;
				$mod_model = new $mod_model_namespace;

				$module_controller[$i] = $mod_controller;
				$module_model[$i] = $mod_model->first();

				array_set($links, $i.'.name',(($module->get('description') == '') ? $tmp : $module->get('description') ));
				array_set($links, $i.'.link', $tmp);

				$i++;
			}
		}

		for ($j = 0 ;$j < $i ; $j++) {
			$fields[$j] = BaseViewController::prepare_form_fields($module_controller[$j]->view_form_fields($module_model[$j]), $module_model[$j]);
			$form_fields[$j] = BaseViewController::add_html_string($fields[$j], 'edit');
		}

		//dd($links, $view_header, $route_name, $module_model, $form_fields);
		return \View::make('GlobalConfig.index', $base_controller->compact_prep_view(compact('links', 'view_header', 'route_name', 'module_model', 'form_fields')));
	}
}
