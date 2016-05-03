<?php

namespace App\Http\Controllers;

use App;
use Module;
use Config;
use File;
use View;
use Validator;
use Input;
use Redirect;
use Route;
use BaseModel;
use Auth;
use Log;
use GlobalConfig;


/*
 * BaseViewController: Is a special Controller which will be a kind of middleware/sub-layer/helper
 *                     between the classical Controllers and Views.

 * Purpose: This Controller is manly used to reduce the logical hard code php stuff from generic views
 *          and to bring the view related stuff from BaseController to a better place - BaseViewController.
 *          This leads to a kind of theoretical sub-layer concept – in fact it is not! See later ..
 *
 * At the time it is not a full qualified sub-layer (API) in this manner that all stuff goes through this
 * Controller, whats between all Controllers and Views. It's more a kind of "Helper" to increase the logical
 * sructuring. This has the advantage that we do not need a complete re-write.

 * Usage: Most of the function here are used in a simple static context from BaseController like
 *        BaseViewController::do_prepare_view_xyz().
 *
 * @author: Torsten Schmidt
 */
class BaseViewController extends Controller {

	/**
     * Searches for a string in the language files under resources/lang/ and returns it for the active application language
     *
     * @author Nino Ryschawy
     */
    public static function translate($string)
    {
        // cut the star at the end of value if there is one for the translate function and append it after translation
        $star = '';
        if (strpos($string, '*'))
        {
            $string = str_replace(' *', '', $string);
            $star = ' *';
        }

        if (strpos($string, 'messages.'))
        	return trans($string).$star;

        $translation = trans("messages.$string");
        // dd($name, $string, $star, $translation, strpos($translation, "messages."));
        // found in lang/{}/messages.php
        if (strpos($translation, 'messages.') === false)
            return $translation.$star;

        return $string.$star;
    }


	// TODO: take language from user setting or the language with highest priority from browser
	// @Nino Ryschawy
	public static function get_user_lang()
	{
		$user = Auth::user();
		if (!isset($user))
			return 'en';
		$language = Auth::user()->language;

		if ($language == 'browser')
		{
			// default
			if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
				return 'en';

			$languages = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
			if (strpos($languages[0], 'de') !== false)
				return 'de';
			else
				return 'en';
		}

		return $language;
	}


	/*
	 * This function is used to prepare get_form_field array for edit view
	 * So all general preparation stuff to view_form_fields will be done here.
	 *
	 * Tasks:
	 *  1. Add a (*) to fields description if validation rule contains required
	 *  2. Add Placeholder YYYY-MM-DD for all date fields
	 *  3. Hide all parent view relation select fields
	 *
	 * @param fields: the view_form_fields array()
	 * @param model: the model to view. Note: could be get_model_obj()->find($id) or get_model_obj()
	 * @return: the modifeyed view_form_fields array()
	 *
	 * @autor: Torsten Schmidt
	 */
	public static function prepare_form_fields($fields, $model)
	{
		$ret = [];

		// get the validation rules for related model object
		$rules = $model->rules();

		// for all fields
		foreach ($fields as $field)
		{
			// rule exists for actual field ?
			if (isset ($rules[$field['name']]))
			{
				// Task 1: Add a (*) to fields description if validation rule contains required
				if (preg_match('/(.*?)required(.*?)/', $rules[$field['name']]))
					$field['description'] = $field['description']. ' *';

				// Task 2: Add Placeholder YYYY-MM-DD for all date fields
				if (preg_match('/(.*?)date(.*?)/', $rules[$field['name']]))
					$field['options']['placeholder'] = 'YYYY-MM-DD';

			}

			// 3. Hide all parent view relation select fields
			if (is_object($model->view_belongs_to()) && 					// does a view relation exists
				$model->view_belongs_to()->table.'_id' == $field['name'])	// view table name (+_id) == field name ?
				$field['hidden'] = 1;									// hide

			$field['field_value'] = $model[$field['name']];

			array_push ($ret, $field);
		}

		return $ret;
	}


	/*
	 * Add ['html'] element to each $fields entry
	 *
	 * The html element contains the HTML formated code to display each HTML field.
	 * You could use 'html' parameter inside the view_form_fields() functions to
	 * overwrite default behavior. The best advice to use these parameter is to
	 * debug the return array of this function.
	 *
	 * @param fields: the prepared view_form_fields array(), each array element represents on (HTML) field
	 * @param context: edit|create - context from which this function is called
	 * @return: array() of fields with added ['html'] element containing the preformed html content
	 *
	 * @autor: Torsten Schmidt
	 */
	public static function compute_form_fields($_fields, $model, $context = 'edit')
	{
		// init
		$ret = [];

		// background color's to toggle through
		$color_array = ['white', '#c8e6c9', '#fff3e0', '#fbe9e7', '#e0f2f1', '#f3e5f5'];
		$color = $color_array[0];

		// prepare form fields
		$fields = static::prepare_form_fields($_fields, $model);

		foreach ($fields as $field)
		{
			$s = '';

			// ignore fields with 'html' parameter
			if (isset($field['html']))
			{
				array_push($ret, $field);
				continue;
			}

			/*
			 * Hide Fields:
			 *
			 * 1. Hide fields that are in HTML _GET array.
			 *    This is required for creating a "relational child"
			 *    elements with pre-filled values. This must be first
			 *    done, otherwise pre-filling does not work
			 *
			 *    Example: Mta/create?modem_id=100002 -> creates MTA to Modem id 100002
			 */
			if (isset($_GET[$field['name']]))
			{
				$s .= \Form::hidden ($field["name"], $_GET[$field['name']]);
				goto finish;
			}

			/*
			 * 2. check if hidden is set in view_form_fields()
			 * 3. globally hide all relation fields
			 *    (this means: all fields ending with _id)
			 */
			if (array_key_exists('hidden', $field))
			{
				$hidden = $field['hidden'];

				if (($context == 'edit' && strpos($hidden, 'E') !== false) ||
				   ($context == 'create' && strpos($hidden, 'C') == false) ||
				   ($hidden == 1 || $hidden == '1'))
					{
						$s .= \Form::hidden ($field["name"], $field['field_value']);
						goto finish;
					}
			}


			/*
			 * Output the Form Elements
			 */
			$value   = isset($field["value"]) ? $field["value"] : [];
			$options = isset($field["options"]) ? $field["options"] : [];
			array_push($options, 'style="background-color:'.$color.'"');

			// select field: used for jquery (java script) realtime based showing/hiding of fields
			$select = null;
			if (isset($field['select']) && is_string($field['select']))
				$select = ['class' => $field['select']];

			// Help: add help msg to form fields - mouse on hover
			if (isset($field['help']))
				$options["title"] = $field['help'];

			// Open Form Group
			$s .= \Form::openGroup($field["name"], $field["description"], $select, $color);

			// form_type ?
			switch ($field["form_type"])
			{
				case 'checkbox' :
					// Checkbox - where pre-checked is enabled
					if ($value == [])
						$value = 1;

					if ($context == 'create')
						// only take care of checked statement if we are called in context create
						$checked = (isset($field['checked'])) ? $field['checked'] : $field['field_value'];
					else
						$checked = $field['field_value'];

					$s .= \Form::checkbox($field['name'], $value, null, $checked);
					break;

				case 'select' :
					$s .= \Form::select($field["name"], $value, $field['field_value'], $options);
					break;

				case 'password' :
					$s .= \Form::password($field['name']);
					break;

				default:
					$value   = $field['field_value'];
					$s .= \Form::$field["form_type"]($field["name"], $value, $options);
					break;
			}

			// Help: add help icon/image behind form field
			if (isset($field['help']))
				$s .= '<div title="'.$field['help'].'" name='.$field['name'].'-help class=col-md-1>'.
				      \HTML::image(asset('images/help.png'), null, ['width' => 20]).'</div>';

			// Close Form Group
			$s .= \Form::closeGroup();


			// Space Element between fields
			if (array_key_exists('space', $field))
			{
				//$s .= "<div class=col-md-12><br></div>";
				$color_array = \Acme\php\ArrayHelper::array_rotate ($color_array);
				$color = $color_array[0];
			}

finish:
			$add = $field;
			$add['html'] = $s;
			array_push($ret, $add);

		}

		// dd($ret);
		return $ret;
	}


	/*
	 * Return the global prepared header links for Main Menu
	 *
	 * NOTE: this function must take care of installed modules!
	 *
	 * @return: array() of header links, like ['module name' => ['page name' => route.entry, ..], ..]
	 *
	 * @author: Torsten Schmidt
	 */
	public static function view_main_menus ()
	{
		$ret = array();
		$modules = Module::enabled();

		// global page
		$array = include(app_path().'/Config/header.php');
		foreach ($array as $lines)
		{
			// array_push($ret, $lines);
			foreach ($lines as $k => $line)
			{
				$key = BaseViewController::translate($k);
				$ret['Global'][$key] = $line;
			}
		}

		// foreach module
		foreach ($modules as $module)
		{
			if (File::exists($module->getPath().'/Config/header.php'))
			{
				/*
				 * TODO: use Config::get()
				 *       this needs to fix namespace problems first
				 */
				$name = ($module->get('description') == '' ? $module->name : $module->get('description')); // module name
				$ret[$name] = [];

				$array = include ($module->getPath().'/Config/header.php');
				foreach ($array as $lines)
				{
					foreach ($lines as $k => $line)
					{
						$key = BaseViewController::translate($k);
						$ret[$name][$key] = $line;
					}
				}
			}
		}

		return $ret;
	}



	/*
	 * Generate Top Header Link (like e.g. Contract > Modem > Mta > ..)
	 * Shows the html links of the related objects recursively
	 *
	 * @param $route_name: route name of actual controller
	 * @param $view_header: the view header name
	 * @param $view_var: the object to generate the link from
	 * @param $html_get: the HTML GET array. This is only required for create context
	 * @return: the HTML link line to be directly included in blade
	 * @author: Torsten Schmidt
	 */
	public static function compute_headline ($route_name, $view_header, $view_var, $html_get = null)
	{
		$s = "";

		// only for create context: parse headline from HTML _GET context array
		// TODO: avoid use of HTML GET array for security considerations
		if (!is_null($html_get) && isset(array_keys($html_get)[0]))
		{
			$key        = array_keys($html_get)[0];
			$class_name = BaseModel::_guess_model_name(ucwords(explode ('_id', $key)[0]));
			$class      = new $class_name;
			$view_var   = $class->find($html_get[$key]);
		}

		if ($view_var != null)
		{
			// Recursively parse all relations from view_var
			$parent = $view_var;
			do
			{
				if ($parent)
				{
					$tmp = explode('\\',get_class($parent));
					$view = end($tmp);

					// get header field name
					// NOTE: for historical reasons check if this is a array or a plain string
					// See: Confluence API  - get_view_headline()
					if(is_array($parent->view_index_label()))
						$name = $parent->view_index_label()['header'];
					else
						$name = $parent->view_index_label();

					$s = \HTML::linkRoute($view.'.edit', $name, $parent->id).' > '.$s;
				}

				// get view parent
				$parent = $parent->view_belongs_to();
			}
			while ($parent);
		}


		// Base Link to Index Table in front of all relations
		if (in_array($route_name, BaseController::get_config_modules()))	// parse: Global Config requires own link
			$s = \HTML::linkRoute('Config.index', 'Global Configurations').': '.$s;
		else
			$s = \HTML::linkRoute($route_name.'.index', $view_header).': '.$s;

		return $s;
	}



	/*
	 * Return the API Version of view_has_many() as normal incremental integer
	 *
	 * @param view_has_many_array: the returned view_has_many() array
	 * @return: api version starting from 1, 2, ..
	 *
	 * @autor: Torsten Schmidt
	 */
	public static function get_view_has_many_api_version ($view_has_many_array)
	{
		if (\Acme\php\ArrayHelper::array_depth($view_has_many_array) < 2)
			return 1;

		return 2;
	}


	/*
	 * Prepare Right Panels to View
	 *
	 * @param $view_var: object/model to be displayed
	 * @return: array() of fields with added ['html'] element containing the preformed html content
	 *
	 * @autor: Torsten Schmidt
	 */
	public static function prep_right_panels ($view_var)
	{
		$api = static::get_view_has_many_api_version($view_var->view_has_many());

		if ($api == 1)
		{
			$relations = $view_var->view_has_many();
		}

		if ($api == 2)
		{
			// API 2: use HTML GET 'blade' to switch between tabs
			// TODO: validate Input blade
			$blade = 0;
			if(Input::get('blade') != '')
				$blade = Input::get('blade');


			// get actual blade to $b from array of all blades in $a
			$a = $view_var->view_has_many();

			if (count($a) == 1)
				return current($a);

			$b = current($a);
			for ($i = 0; $i < $blade; $i++)
				$b = next($a); // move to next blade/tab

			$relations = $b;
		}


		return ($relations);
	}


	/*
	 * Prepare Index Entry Table (<tr>) Colors
	 *
	 * @param $object: the object to look at
	 * @param $rotate_after: rotate color array after number of entries
	 * @return: bootstrap table index class/color [success|warning|danger|info]
	 *
	 * @autor: Torsten Schmidt
	 */
	public static function prep_index_entries_color($object, $rotate_after = 5)
	{
		static $color_array = ['success', 'warning', 'danger', 'info'];
		static $i;

		$class = current($color_array);

		// Check if class object has a own color definition
		if (isset($object->view_index_label()['bsclass']))
			$class = $object->view_index_label()['bsclass'];
		else
		{
			// Rotate Color through $color_array every $rotate_after entries
			if ($i++ % $rotate_after == 0)
			{
				$color_array = \Acme\php\ArrayHelper::array_rotate($color_array);
				$class = $color_array[0];
			}
		}

		return $class;
	}
}