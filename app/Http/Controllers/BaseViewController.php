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
     * Searches for a "*" (required field), deletes it for trans function and appends it at the end
     * used in everything Form related (Labels, descriptions)
     * @author Nino Ryschawy, Christian Schramm
     */
    public static function translate_label($string)
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

        // found in lang/{}/messages.php
        if (strpos($translation, 'messages.') === false)
            return $translation.$star;

        return $string.$star;
    }

	/**
     * Searches for a string in the language files under resources/lang/ and returns it for the active application language
     * used in everything view related 
     * @param string: 	string that is searched in resspurces/lang/{App-language}/view.php
     * @param type: 	can be Header, Menu, Button, jQuery, Search
     * @param count: 	standard at 1 , For plural translation - needs to be seperated with pipe "|""
     *					example: Index Headers -> in view.php: 'Header_Mta'	=> 'MTA|MTAs',
     * @author Christian Schramm
     */
    public static function translate_view($string, $type, $count = 1)
    {
        if (strpos($string, 'view.'.$type.'_'))
        	return trans($string);

   		$translation = trans_choice('view.'.$type.'_'.$string, $count);

        // found in lang/{}/messages.php
        if (strpos($translation, 'view.'.$type.'_') === false)
            return $translation;

        return $string;
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


	/**
	 * This function is used to prepare the resulting view_form_field array for edit view.
	 * So all general preparation stuff to view_form_fields() will be done here.
	 *
	 * Tasks:
	 *  1. Add a (*) to fields description if validation rule contains required
	 *  2. Add Placeholder YYYY-MM-DD for all date fields
	 *  3. Hide all parent view relation select fields (works only in edit context)
	 *  4. auto-fill field_value with correlating model data (from sql)
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
				// 1. Add a (*) to fields description if validation rule contains required
				if (preg_match('/(.*?)required(.*?)/', $rules[$field['name']]))
					$field['description'] = $field['description']. ' *';

				// 2. Add Placeholder YYYY-MM-DD for all date fields
				if (preg_match('/(.*?)date(.*?)/', $rules[$field['name']]))
					$field['options']['placeholder'] = 'YYYY-MM-DD';

			}

			// 3. Hide all parent view relation select fields (in edit context)
			//    NOTE: this will not work in create context, because view_belongs_to() returns null !
			//          Hiding in create context will only work with hard coded 'hidden' => 1 entry in view_form_fields()
			if (is_object($model->view_belongs_to()) && 					// does a view relation exists
				$model->view_belongs_to()->table.'_id' == $field['name'])	// view table name (+_id) == field name ?
				$field['hidden'] = 1;									// hide

			// 4. set all field_value's to SQL data
			$field['field_value'] = $model[$field['name']];

			// 4.(sub-task) auto-fill all field_value's with HTML POST array if supposed
			if (isset($_POST[$field['name']]))
				$field['field_value'] = $_POST[$field['name']];

			// 4. (sub-task)
			// write explicitly given init_value to field_value
			// this is needed e.g. by Patrick to prefill new PhonenumberManagement and PhonebookEntry with data from Contract
			if (array_key_exists('init_value', $field)) {
				$field['field_value'] = $field['init_value'];
			}

			array_push ($ret, $field);
		}

		return $ret;
	}


	/**
	 * Add ['html'] element to each $fields entry
	 *
	 * The html element contains the HTML formated code to display each HTML field.
	 * You could use 'html' parameter inside the view_form_fields() functions to
	 * overwrite default behavior. The best advice to use these parameter is to
	 * debug the return array of this function and adapt it to you requirements.
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

		// foreach fields
		foreach ($fields as $field)
		{
			$s = '';

			// ignore fields with 'html' parameter
			if (isset($field['html']))
			{
				array_push($ret, $field);
				continue;
			}

			// hidden stuff
			if (array_key_exists('hidden', $field))
			{
				$hidden = $field['hidden'];

				if (($context == 'edit' && strpos($hidden, 'E') !== false) || // hide edit context only?
				   ($context == 'create' && strpos($hidden, 'C') !== false) || // hide create context only?
				   ($hidden == 1 || $hidden == '1')) // hide globally?
					{
						$s .= \Form::hidden ($field["name"], $field['field_value']);
						goto finish;
					}
			}


			// prepare value and options vars
			$value   = isset($field["value"]) ? $field["value"] : [];
			$options = isset($field["options"]) ? $field["options"] : [];

			// field color
			if(!isset($options['style']))
				$options['style'] = '';
			$options['style'] .= " background-color:$color";

			// Help: add help msg to form fields - mouse on hover
			if (isset($field['help']))
				$options["title"] = $field['help'];

			// select field: used for jquery (java script) realtime based showing/hiding of fields
			$select = null;
			if (isset($field['select']) && is_string($field['select']))
				$select = ['class' => $field['select']];

			// Open Form Group
			$s .= \Form::openGroup($field["name"], $field["description"], $select, $color);

			// Output the Form Elements
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
					$s .= \Form::$field["form_type"]($field["name"], $field['field_value'], $options);
					break;
			}

			// Help: add help icon/image behind form field
			if (isset($field['help']))
				$s .= '<div title="'.$field['help'].'" name='.$field['name'].'-help class=col-md-1>'.
				      \HTML::image(asset('images/help.png'), null, ['width' => 20]).'</div>';

			// Close Form Group
			$s .= \Form::closeGroup();



finish:
			// Space Element between fields and color switching
			if (array_key_exists('space', $field))
			{
				//$s .= "<div class=col-md-12><br></div>";
				$color_array = \Acme\php\ArrayHelper::array_rotate ($color_array);
				$color = $color_array[0];
			}

			// add ['html'] parameter
			$add = $field;
			$add['html'] = $s;
			array_push($ret, $add);

		}

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
				$key = \App\Http\Controllers\BaseViewController::translate_view($k, 'Menu');
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
						$key = \App\Http\Controllers\BaseViewController::translate_view($k, 'Menu');
						$ret[$name][$key] = $line;
					}
				}
			}
		}

		return $ret;
	}



	/**
	 * Generate Top Header Link (like e.g. Contract > Modem > Mta > ..)
	 * Shows the html links of the related objects recursively
	 *
	 * @param $route_name: route name of actual controller
	 * @param $view_header: the view header name
	 * @param $view_var: the object to generate the link from
	 * @param $html: the HTML GET array. See note bellow!
	 * @return the HTML link line to be directly included in blade
	 * @author Torsten Schmidt
	 *
	 * NOTE: in create context we are forced to work with HTML GET array in $html.
	 *       The first request will also work with POST array, but if validation fails
	 *       there is no longer any POST array we can work with. Note that POST array is
	 *       generated in relation.blade.
	 *
	 *       To avoid this we must ensure that every relational create send it's correlating
	 *       model key, like contract_id=xyz in HTML GET request.
	 */
	public static function compute_headline ($route_name, $view_header, $view_var, $html = null)
	{
		$s = "";

		// only for create context: parse headline from HTML POST context array
		if (!is_null($html) && isset(array_keys($html)[0]))
		{
			$key        = array_keys($html)[0];
			$class_name = BaseModel::_guess_model_name(ucwords(explode ('_id', $key)[0]));
			$class      = new $class_name;
			$view_var   = $class->find($html[$key]);
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

					$s = \HTML::linkRoute($view.'.edit', BaseViewController::translate_view($name, 'Header'), $parent->id).' > '.$s;
				}
				// get view parent
				$parent = $parent->view_belongs_to();
			}
			while ($parent);
		}


		// Base Link to Index Table in front of all relations
		if (in_array($route_name, BaseController::get_config_modules()))	// parse: Global Config requires own link
			$s = \HTML::linkRoute('Config.index', BaseViewController::translate_view('Global Configurations', 'Header')).': '.$s;
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
