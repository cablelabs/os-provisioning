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
	 * Add ['html'] element to each $fields entry
	 *
	 * The html element contains the HTML formated code to display each HTML field.
	 * You could use 'html' parameter inside the get_form_fields() functions to
	 * overwrite default behavior. The best advice to use these parameter is to
	 * debug the return array of this function.
	 *
	 * TODO: move too a separate class
	 *
	 * @param fields: the prepared get_form_fields array(), each array element represents on (HTML) field
	 * @param context: edit|create - context from which this function is called
	 * @return: array() of fields with added ['html'] element containing the preformed html content
	 *
	 * @autor: Torsten Schmidt
	 */
	public static function html_form_field($fields, $context = 'edit')
	{
		$ret = [];

		// background color's to toggle through
		$color_array = ['white', '#c8e6c9', '#fff3e0', '#fbe9e7', '#e0f2f1', '#f3e5f5'];
		$color = $color_array[0];


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
			 * 2. check if hidden is set in get_form_fields()
			 * 3. globally hide all relation fields
			 *    (this means: all fields ending with _id)
			 */
			if (array_key_exists('hidden', $field))
			{
				$s .= \Form::hidden ($field["name"]);
				goto finish;
			}


			/*
			 * Output the Form Elements
			 */
			$value   = isset($field["value"]) ? $field["value"] : [];
			$options = isset($field["options"]) ? $field["options"] : [];
			array_push($options, 'style="background-color:'.$color.'"');

			// Help: add help msg to form fields - mouse on hover
			if (isset($field['help']))
				$options["title"] = $field['help'];

			// Open Form Group
			$s .= \Form::openGroup($field["name"], $field["description"], [], $color);

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
				$color_array = array_merge( array(array_pop($color_array)), $color_array);
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


	public static function get_view_header_links ()
	{
		$ret = array();
		$modules = Module::enabled();

		$array = include(app_path().'/Config/header.php');
		foreach ($array as $lines)
		{
			// array_push($ret, $lines);
			foreach ($lines as $k => $line)
			{
				$key = BaseViewController::translate($k);
				array_push($ret, [$key => $line]);
			}
		}

		foreach ($modules as $module)
		{
			if (File::exists($module->getPath().'/Config/header.php'))
			{
				/*
				 * TODO: use Config::get()
				 *       this needs to fix namespace problems first
				 */
				$array = include ($module->getPath().'/Config/header.php');
				foreach ($array as $lines)
				{
					// array_push($ret, $lines);
					foreach ($lines as $k => $line)
					{
						$key = BaseViewController::translate($k);
						array_push($ret, [$key => $line]);
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
	 * @return: the HTML link line to be directly included in blade
	 * @author: Torsten Schmidt
	 */
	public static function prep_link_header ($route_name, $view_header, $view_var)
	{
		$s = "";

		if ($view_var != null)
		{
			// Recursivly parse all relations from view_var
			$parent = $view_var;
			do
			{
				if ($parent)
				{
					$tmp = explode('\\',get_class($parent));
					$view = end($tmp);

					// get header field name
					// NOTE: for historical reasons check if this is a array or a plain string
					// See: Confluence API  - get_view_link_header()
					if(is_array($parent->get_view_link_title()))
						$name = $parent->get_view_link_title()['header'];
					else
						$name = $parent->get_view_link_title();

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
}