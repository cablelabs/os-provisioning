<?php

namespace Modules\ProvVoip\Entities;

// Model not found? execute composer dump-autoload in lara root dir
class PhonebookEntry extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'phonebookentry';

	public static $config = null;


	// Add your validation rules here
	// Also have a look at PhonebookEntryController@prep_rules!
	public static function rules($id=null)
	{
		return array(
			'phonenumbermanagement_id' => 'required|exists:phonenumbermanagement,id|min:1',
			'reverse_search' => 'required|phonebook_one_character_option',
			'publish_in_print_media' => 'required|phonebook_one_character_option',
			'publish_in_electronic_media' => 'required|phonebook_one_character_option',
			'directory_assistance' => 'required|phonebook_one_character_option',
			'entry_type' => 'required|phonebook_one_character_option',
			'publish_address' => 'required|phonebook_one_character_option',
			'company' => 'required_if:entry_type,F',
			'academic_degree' => 'phonebook_predefined_string',
			'noble_rank' => 'phonebook_predefined_string',
			'nobiliary_particle' => 'phonebook_predefined_string',
			'lastname' => 'required_if:entry_type,P|phonebook_string',
			'other_name_suffix' => 'phonebook_predefined_string',
			'firstname' => 'phonebook_string',
			'street' => 'required|phonebook_string',
			'houseno' => 'required|phonebook_string',
			'zipcode' => 'required|phonebook_string',
			'city' => 'required|phonebook_string',
			'urban_district' => 'phonebook_string',
			'business' => 'phonebook_string',
			'number_usage' => 'required|phonebook_one_character_option',
			'tag' => 'phonebook_predefined_string',
		);
	}


	public static function read_config() {

		if (is_null(static::$config)) {

			// we have to use the raw scanner because of the special characters like “(”…
			$config = parse_ini_file('/etc/nms/provvoip/phonebook_entry__config.ini', true, INI_SCANNER_RAW);

			// with using the raw scanner type we have to convert some values
			// false is a string in this case – and boolval("false") == true

			// lambda to exchange bool strings with real boolean values
			$to_bool = function(&$item) {
				if (\Str::lower($item) == "false") {
					$item = false;
				}
				elseif(\Str::lower($item) == "true") {
					$item = true;
				}
			};

			// lambda to change integer strings to integer
			$to_int = function(&$item) {
				if (is_numeric($item)) {
					$item = intval($item);
				}
			};

			$process_valid = function($value_raw, $replacements) {

				$valids = explode(',', str_replace(" ", "", $value_raw));

				$ret = "";

				foreach ($valids as $valid) {
					$ret .= $replacements[$valid];
				}

				return $ret;
			};

			$to_array = function($value_raw) {

				return explode(',', str_replace(" ", "", $value_raw));
			};

			// walk through each section in config array
			// attention: we have to work with the array itself as array_walk works in place
			foreach (array_keys($config) as $section) {
				// modify booleans
				array_walk($config[$section], $to_bool);

				// modify integers and lists (but not for section char_lists
				if ($section != "char_lists") {
					array_walk($config[$section], $to_int);

					foreach (array_keys($config[$section]) as $var) {

						// replace valid groups by valid characters
						if ($var == "valid") {
							$config[$section][$var] = $process_valid($config[$section][$var], $config['char_lists']);
						}

						if (in_array($var, ['in_list'])) {
							$config[$section][$var] = $to_array($config[$section][$var]);
						}
					}
				}
			}

			static::$config = $config;
		}

	}

	// Name of View
	public static function get_view_header()
	{
		return 'Phonebook entry';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return $this->id;
	}

	/**
	 * ALL RELATIONS
	 * link with phonenumbers
	 */
	public function phonenumbermanagement()
	{
		return $this->belongsTo('Modules\ProvVoip\Entities\PhonenumberManagement');
	}

	// belongs to an phonenumber
	public function view_belongs_to ()
	{
		return $this->phonenumbermanagement;
	}


	/**
	 * Helper to get options defined in lists.
	 *
	 * @author Patrick Reichel
	 */
	public function get_options_from_list($section) {

		$options = array();

		if (is_null(static::$config)) {
			static::read_config();
		};

		foreach (static::$config[$section]['in_list'] as $option) {
			$options[$option] = $option.' – '.static::$config[$section][$option];
		}

		return $options;
	}


}
