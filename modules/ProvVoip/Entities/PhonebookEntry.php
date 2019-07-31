<?php

namespace Modules\ProvVoip\Entities;

class PhonebookEntry extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'phonebookentry';

    public static $config = null;

    // Add your validation rules here
    // Also have a look at PhonebookEntryController::prepare_rules (we replace form names in rules by their value)!
    public static function rules($id = null)
    {
        $ret = [
            'phonenumbermanagement_id' => 'required|exists:phonenumbermanagement,id,deleted_at,NULL|min:1',
            'reverse_search' => 'required|phonebook_one_character_option',
            'publish_in_print_media' => 'required|phonebook_one_character_option',
            'publish_in_electronic_media' => 'required|phonebook_one_character_option',
            'directory_assistance' => 'required|phonebook_one_character_option',
            'entry_type' => 'required|phonebook_one_character_option',
            'publish_address' => 'required|phonebook_one_character_option',
            'company' => 'phonebook_entry_type_dependend:entry_type|phonebook_string',
            'salutation' => 'phonebook_entry_type_dependend:entry_type|phonebook_predefined_string',
            'academic_degree' => 'phonebook_entry_type_dependend:entry_type|phonebook_predefined_string',
            'noble_rank' => 'phonebook_entry_type_dependend:entry_type|phonebook_predefined_string',
            'nobiliary_particle' => 'phonebook_entry_type_dependend:entry_type|phonebook_predefined_string',
            'lastname' => 'phonebook_entry_type_dependend:entry_type|phonebook_string',
            'other_name_suffix' => 'phonebook_entry_type_dependend:entry_type|phonebook_predefined_string',
            'firstname' => 'phonebook_entry_type_dependend:entry_type|phonebook_string',
            'street' => 'required|phonebook_string',
            'houseno' => 'required|phonebook_string',
            'zipcode' => 'required|phonebook_string',
            'city' => 'required|phonebook_string',
            'urban_district' => 'phonebook_string',
            'business' => 'phonebook_predefined_string',
            'usage' => 'required|phonebook_one_character_option',
        ];

        // starting with API version 2.7 envia TEL ignores “tag”
        // not sure if true for others – so only removed if provvoipenvia is enabled
        if (! \Module::collections()->has('ProvVoipEnvia')) {
            $ret['tag'] = 'phonebook_predefined_string';
        }

        return $ret;
    }

    public static function read_config()
    {
        if (is_null(static::$config)) {

            // we have to use the raw scanner because of the special characters like “(”…
            if (\Module::collections()->has('ProvVoipEnvia')) {
                // to use the envia TEL API there seems to be some special cases – use own config file
                $config = parse_ini_string(\Storage::get('config/provvoip/phonebook_entry__config_provvoipenvia.ini'), true, INI_SCANNER_RAW);
            } else {
                $config = parse_ini_string(\Storage::get('config/provvoip/phonebook_entry__config.ini'), true, INI_SCANNER_RAW);
            }

            // with using the raw scanner type we have to convert some values
            // false is a string in this case – and boolval("false") == true

            // lambda to exchange bool strings with real boolean values
            $to_bool = function (&$item) {
                if (\Str::lower($item) == 'false') {
                    $item = false;
                } elseif (\Str::lower($item) == 'true') {
                    $item = true;
                }
            };

            // lambda to change integer strings to integer
            $to_int = function (&$item) {
                if (is_numeric($item)) {
                    $item = intval($item);
                }
            };

            $process_valid = function ($value_raw, $replacements) {
                $valids = explode(',', str_replace(' ', '', $value_raw));

                $ret = '';

                foreach ($valids as $valid) {
                    $ret .= $replacements[$valid];
                }

                return $ret;
            };

            $to_array = function ($value_raw) {
                return explode(',', str_replace(' ', '', $value_raw));
            };

            // walk through each section in config array
            // attention: we have to work with the array itself as array_walk works in place
            foreach (array_keys($config) as $section) {
                // modify booleans
                array_walk($config[$section], $to_bool);

                // modify integers and lists (but not for section char_lists
                if ($section != 'char_lists') {
                    array_walk($config[$section], $to_int);

                    foreach (array_keys($config[$section]) as $var) {

                        // replace valid groups by valid characters
                        if ($var == 'valid') {
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
    public static function view_headline()
    {
        return 'Phonebook entry';
    }

    // Name of View
    public static function get_view_header()
    {
        return 'Phonebook entry';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        return ['table' => $this->table,
                'index_header' => [$this->table.'.id'],
                'header' => trans('view.PhonebookEntry'),
                'bsclass' => $bsclass, ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        return $bsclass;
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
    public function view_belongs_to()
    {
        return $this->phonenumbermanagement;
    }

    /**
     * Helper to get options defined in lists.
     *
     * @author Patrick Reichel
     */
    public function get_options_from_list($section, $with_placeholder = false, $allow_empty = false)
    {
        $options = [];

        if (is_null(static::$config)) {
            static::read_config();
        }

        // placeholder
        // if empty values are not allowed we give a hint to the user
        if ($with_placeholder) {
            if ($allow_empty) {
                $options[''] = '';
            } else {
                $options[''] = 'n/a – to be set';
            }
        }

        // the real options
        foreach (static::$config[$section]['in_list'] as $option) {
            $desc = static::$config[$section][$option];
            if ($option != $desc) {
                // write the official TCom shortcuts (if existing) – can be useful for experienced staff
                $desc = $option.' – '.$desc;
            }
            $options[$option] = $desc;
        }

        return $options;
    }

    /**
     * Helper to get options defined in file.
     *
     * @author Patrick Reichel
     */
    public static function get_options_from_file($section)
    {
        $options = [];

        if (is_null(static::$config)) {
            static::read_config();
        }

        $entries = explode("\n", \Storage::get('config/provvoip/'.static::$config[$section]['in_file']));

        $options[''] = '';
        foreach ($entries as $entry) {
            $options[$entry] = $entry;
        }

        return $options;
    }

    // View Relation.
    public function view_has_many()
    {
        if (\Module::collections()->has('ProvVoipEnvia')) {

            // TODO: auth - loading controller from model could be a security issue ?
            $ret['envia TEL']['EnviaAPI']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
            $ret['envia TEL']['EnviaAPI']['view']['vars']['extra_data'] = \Modules\ProvVoip\Http\Controllers\PhonebookEntryController::_get_envia_management_jobs($this);
        } else {
            $ret = [];
        }

        return $ret;
    }
}
