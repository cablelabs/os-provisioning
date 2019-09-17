<?php

namespace Modules\ProvBase\Entities;

class DocumentTemplate extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'documenttemplate';

    public $guarded = ['file_upload', 'id_for_validation'];

    public $regex_iso_date = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'documenttype_id' => 'required|exists:documenttype,id,deleted_at,NULL',
            'file_upload' => 'mimetypes:text/x-tex,application/x-tex,text/plain',   // text/plain is a fallback – laravel is not able to guess correctly ATM
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'DocumentTemplate';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-window-restore"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        // build additional where clause to show only documenttemplates that are related to enable modules
        $enabled_modules = [];
        foreach (\Module::collections() as $module) {
            array_push($enabled_modules, "'".$module->name."'");
        }
        $where_clause_enabled_modules = 'documenttype_id in (SELECT id FROM documenttype where module IN ('.implode(', ', $enabled_modules).'))';

        $ret = [
            'table' => $this->table,
            'index_header' => ['documenttype.type_view', $this->table.'.file', $this->table.'.format'],
            'header' =>  $this->documenttype ? $this->documenttype->type_view : '',
            'bsclass' => $bsclass,
            'order_by' => ['0' => 'asc'],
            'eager_loading' => ['documenttype', 'company', 'sepaaccount'],
            'where_clauses' => [$where_clause_enabled_modules],
        ];

        if (\Module::collections()->has('BillingBase')) {
            array_push($ret['index_header'], 'company.name', 'company.city', 'sepaaccount.name');
        }

        return $ret;
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        return $bsclass;
    }

    public function documenttype()
    {
        return $this->belongsTo('Modules\ProvBase\Entities\DocumentType');
    }

    public function company()
    {
        if (! \Module::collections()->has('BillingBase')) {
            return null;
        }
        return $this->belongsTo('Modules\BillingBase\Entities\Company');
    }

    public function sepaaccount()
    {
        if (! \Module::collections()->has('BillingBase')) {
            return null;
        }
        return $this->belongsTo('Modules\BillingBase\Entities\SepaAccount', 'sepaaccount_id');
    }


    /**
     * Wrapper to add postal addresses.
     *
     * @param   array   reference to data array to add addresses to (in place)
     * @param   object  reference to model
     * @param   string  model name
     * @return  null
     *
     * @author  Patrick Reichel
     */
    protected function _add_postal_addresses(&$data, &$model, &$model_name)
    {
        $data["$model_name.aggregated-postal-address-de"] = $this->_get_postal_address_de($model, '\\\\');

        return null;
    }


    /**
     * Wrapper to add greeting phrases.
     *
     * @param   array   reference to data array to add greeting phrases to (in place)
     * @param   object  reference to model
     * @param   string  model name
     * @return  null
     *
     * @author  Patrick Reichel
     */
    protected function _add_greeting_phrases(&$data, &$model, &$model_name)
    {
        $data["$model_name.aggregated-greeting-phrase-de"] = $this->_get_greeting_phrase_de($model);

        return null;
    }


    /**
     * Helper to get a complete German address from given model.
     *
     * @param   object  reference to model
     *
     * @return  mixed   formatted address as string if linebreak is given, else array, line by line
     *
     * @author  Patrick Reichel
     */
    protected function _get_postal_address_de(&$model, $linebreak = null)
    {
        $address = [];

        // add either company information or the name
        if ($model->company) {
            array_push($address, $model->company);
            if ($model->department) {
                array_push($address, $model->department);
            }
        }

        if (!$address) {
            $line = $model->salutation;
            $line .= ($model->salutation == 'Herr') ? 'n' : '';
            array_push($address, $line);
        }

        if ($model->firstname || $model->lastname) {
            $tmp = $model->academic_degree ?? null;
            $line = $tmp ? $tmp.' ' : '';
            if ($model->firstname && $model->lastname) {
                $line .= $model->firstname.' '.$model->lastname;
            } elseif ($model->firstname) {
                $line .= $model->firstname;
            } else {
                $line .= $model->lastname;
            }
            array_push($address, $line);
        }

        // add district if any
        if ($model->district) {
            array_push($address, $model->district);
        }

        // street and house number
        array_push($address, $model->street.' '.$model->house_number);

        // zipcode and city
        array_push($address, $model->zip.' '.$model->city);

        if (! is_null($linebreak)) {
            $address = implode($linebreak, $address);
        }

        return $address;
    }

    /**
     * Helper to create a German greeting line depending on salutation in model.
     *
     * @param   object  reference to model
     *
     * @return  string  greeting phrase for direct use in Documents
     *
     * @author Patrick Reichel
     */
    protected function _get_greeting_phrase_de(&$model)
    {

        switch ($model->salutation) {
            case 'Herr':
                $ret = 'Sehr geehrter Herr '.$model->lastname.',';
                break;
            case 'Frau':
                $ret = 'Sehr geehrte Frau '.$model->lastname.',';
                break;
            default:
                $ret = 'Sehr geehrte Damen und Herren,';
                break;
        }

        return $ret;
    }


    /**
     * Helper to get document related meta information.
     *
     * @param   array   $data       reference to data array
     * @return  null
     *
     * @author  Patrick  Reichel
     */
    protected function _add_document_meta_data(&$data)
    {
        $data['DocumentMeta.generation-date'] = date('c');
        $data['DocumentMeta.generation-date-de'] = date('d.m.Y');

        $months_de = [
            1 => 'Januar',
            2 => 'Februar',
            3 => 'März',
            4 => 'April',
            5 => 'Mai',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'August',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Dezember',
        ];
        $data['DocumentMeta.generation-date-de-long'] = date('d. ').$months_de[date('n')].date(' Y');

        return null;
    }


    /**
     * Extracts all data from models and returns them as array with Model.attribute as keys
     *
     * @param   array   $models     The models to extract data from
     * @return  array
     *
     * @author  Patrick Reichel
     */
    protected function _models_to_data(&$models)
    {
        $data = [];

        // assume that there is no data for any model
        foreach (\BaseModel::get_models() as $model => $path) {
            $data['DocumentMeta.'.strtolower($model).'-exists'] = 'no';
        }

        foreach ($models as $object) {
            // check if object is a model or a collection of models
            if ($object instanceof \BaseModel) {
                $this->_single_model_to_data($data, $object);
            }
            elseif ($object instanceof \Illuminate\Support\Collection) {
                $this->_model_collection_to_data($data, $object);
            }
            elseif (is_array($object)) {
                $this->_model_array_to_data($data, $object);
            }
            else {
                throw new \Exception('Unknown object given – expecting instance of BaseModel or Collection or an array');
            }
        }

        return $data;
    }

    /**
     * Helper to convert ISO dates to German dates
     *
     * @param   string  isodate
     * @return  string  German date
     *
     * @author  Patrick Reichel
     */
    public function isodate_to_german($isodate) {
        preg_match($this->regex_iso_date, $isodate, $matches);
        if (! $matches) {
            \Log::warning(__METHOD__.": $isodate is not an isodate, cannot convert");
        }
        return date("d.m.Y", strtotime($isodate));
    }


    /**
     * Extract data from a single model.
     *
     * @param   array   reference to data array
     * @param   object  reference to model object
     * @return  null
     */
    protected function _single_model_to_data(&$data, &$model)
    {
        $model_name = $model->get_model_name();
        // switch existance flag
        $data['DocumentMeta.'.strtolower($model_name).'-exists'] = 'yes';

        foreach ($model->attributes as $key => $value) {
            $latex_key = str_replace('_', '-', $key);
            $data["$model_name.$latex_key"] = $value;
            // check if ISO date to convert to local one
            preg_match($this->regex_iso_date, $value, $matches);
            if ($matches) {
                $data["$model_name.modified-$latex_key-de"] = $this->isodate_to_german($value);
            }
        };

        // add preformated postal addresses and greeting phrases
        if (in_array($model_name, ['Contract', 'Modem'])) {
            $this->_add_postal_addresses($data, $model, $model_name);
            $this->_add_greeting_phrases($data, $model, $model_name);
        };

        // make logo path absolute
        if ('Company' == $model_name) {
            $data['Company.logo'] = \Storage::path('config/billingbase/logo/'.$model->logo);
        };

        return null;
    }


    /**
     * Extract data from a model collection.
     *
     * @param   array                           reference to data array
     * @param   \Illuminate\Support\Collection  reference to laravel collection holding model objects
     * @return  null
     *
     * @author  Patrick Reichel
     */
    protected function _model_collection_to_data(&$data, &$collection)
    {
        if ($collection->isEmpty) {
            return null;
        }

        foreach ($collection as $model) {
            throw new \Exception('Not yet implemented');
        }

        return null;
    }


    /**
     * Wrapper to extract data from an array of model objects.
     *
     * @param   array   reference to data array
     * @param   array   reference to model array
     * @return  null
     *
     * @author Patrick Reichel
     */
    protected function _model_array_to_data(&$data, &$model_array)
    {
        foreach ($model_array as $key => $value) {
            if ('item_related_models' == $key) {
                $this->_item_related_model_array_to_data($data, $value);
            }
        }

        return null;
    }


    /**
     * Extract data from an array of model objects.
     * Aggregated data depends on current document type
     *
     * @param   array   reference to data array
     * @param   array   reference to model array
     * @return  null
     *
     * @author Patrick Reichel
     */
    protected function _item_related_model_array_to_data(&$data, &$models_array)
    {
        if ('contract_start' == $this->documenttype->type) {
            $this->_item_data_contract_start_de($data, $models_array);
            $this->_item_sepadata_contract_start_de($data, $models_array);
        }

        return null;
    }


    /**
     * Aggregate item related data for contract start
     *
     * @param   array   reference to data array
     * @param   array   reference to model array
     * @return  null
     *
     * @author Patrick Reichel
     */
    protected function _item_data_contract_start_de(&$data, &$models_array)
    {
        $entry = 'Posten:';
        foreach ($models_array as $models) {
            $item = $models['Item'];
            $product = $models['Product'];

            if (in_array($product->type, ['Internet', 'TV', 'Voip'])) {
                $entry .= ' \> '.$product->name.' (ab '.$this->isodate_to_german($item->valid_from).') \\\\';
            }
        }

        $data['BillingBase.aggregated-tabbed-item-data-de'] = $entry;
        return null;
    }


    /**
     * Aggregate item related sepa data for contract start
     *
     * @param   array   reference to data array
     * @param   array   reference to model array
     * @return  null
     *
     * @author Patrick Reichel
     */
    protected function _item_sepadata_contract_start_de(&$data, &$models_array)
    {
        $lines = [];
        // get different sepamandate/sepaaccount combinations
        $combinations = [];
        foreach ($models_array as $item_id => $models) {
            $key = [];
            foreach ($models as $model_name => $model) {
                if (in_array($model_name, ['SepaAccount', 'SepaMandate'])) {
                    $key[] = $model->id;
                }
            }
            $key = implode('_', $key);
            if (! array_key_exists($key, $combinations)) {
                $combinations[$key] = [];
            }
            $combinations[$key][] = $item_id;
        }

        if ($combinations) {
            $data['DocumentMeta.aggregated-tabbedsepadata-de-exists'] = 'yes';
        }

        if (1 == count($combinations)) {
            // easy going – take one entry to build data for all
            $models = reset($models_array);
            $lines[] = 'Alle Rechnungsbeträge ziehen wir per Lastschrift von folgender Bankverbindung ein:\\\\';
            $lines[] = '~\\\\';
            $lines[] = 'Institut: \> '.$models['SepaMandate']->institute.' \\\\';
            $lines[] = 'BIC: \> '.$models['SepaMandate']->bic.' \\\\';
            $lines[] = 'IBAN: \> '.$models['SepaMandate']->iban.' \\\\';
            $lines[] = 'Kontoinhaber: \> '.$models['SepaMandate']->holder.' \\\\';
            $lines[] = 'Mandatsreferenz: \> '.$models['SepaMandate']->reference.' \\\\';
            $lines[] = 'Gläubiger-ID: \> '.$models['SepaAccount']->creditorid.' \\\\';
        }
        elseif (1 < count($combinations)) {
            throw new \Exception('Processing multipe SEPA mandates at contract start not yet implemented');
        }


        $data['BillingBase.aggregated-tabbed-item-sepadata-de'] = implode("\n", $lines);

        // all items
        /* d($combinations, $models_array, $lines); */
        return null;
    }


    /**
     * Collects data for testing letterhead.
     *
     * @author Patrick Reichel
     */
    protected function _collect_data_for_letterhead_test()
    {
        $models = [];

        $contract = \Modules\ProvBase\Entities\Contract::all()->random();

        if (\Module::collections()->has('BillingBase')) {
            if ($this->sepaaccount_id) {
                $sepaaccount = \Modules\BillingBase\Entities\SepaAccount::find($this->sepaaccount_id);
                $company = $sepaaccount->company;
            }
            elseif ($this->company_id) {
                $company = \Modules\BillingBase\Entities\Company::find($this->company_id);
                $sepaaccount = $company->sepaaccounts->random();
            }
            else {
                $sepaaccount = \Modules\BillingBase\Entities\SepaAccount::all()->random();
                $company = $sepaaccount->company;
            }
            array_push($models, $contract, $company, $sepaaccount);
        } else {
            $models[] = $contract;
        }

        $data = $this->_models_to_data($models);

        // fill dummy content to extend to two-sided PDF
        $lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\\\\ ';
        $data['Document.content'] = '\opening{\textbf{\Large{Lorem ipsum}}}\\n';
        $data['Document.content'] .= str_repeat($lorem, 10);

        return $data;
    }

    /**
     * Collect data to create a document from all affected models
     *
     * @param   object  $model  The initial model to be used
     * @return  array   The collected data (with LaTeX placeholders as keys)
     *
     * @author Patrick Reichel
     */
    public function collect_data($model=null)
    {
        $test_mode = is_null($model);
        $documenttype = $this->documenttype;

        // check for special case: testing of lettehead templates
        if ('letterhead' == $documenttype->type) {
            return $this->_collect_data_for_letterhead_test();
        }

        $models = [];

        if ($test_mode) {
            // in test mode => get a random model
            $model_path = 'Modules\\'.$documenttype->module.'\\Entities\\'.$documenttype->model;
            $model = $model_path::all()->random();
        }

        $aggregated_models = [];    // holding related models

        // get all the models related to model defined in document template
        if ('PhonenumberManagement' == $documenttype->model) {
            $phonenumbermanagement = $model;
            $phonenumber = $phonenumbermanagement->phonenumber;
            $mta = $phonenumber->mta;
            $modem = $mta->modem;
            $contract = $modem->contract;
            array_push($models, $phonenumbermanagement, $phonenumber, $mta, $modem, $contract);
        }
        elseif ('Contract' == $documenttype->model) {
            $contract = $model;
            $models[] = $contract;
        }

        if (\Module::collections()->has('BillingBase')) {
            // we take company directly connected to contract as data source for letterhead
            $costcenter = $contract->costcenter;
            $sepaaccount = $costcenter->sepaaccount;
            $company = $sepaaccount->company;

            $aggregated_models['item_related_models'] = [];
            $items = $contract->items;
            foreach ($items as $item) {
                $aggregated_models['item_related_models'][$item->id]['Item'] = $item;
                $aggregated_models['item_related_models'][$item->id]['Product'] = $item->product;
                $aggregated_models['item_related_models'][$item->id]['CostCenter'] = $item->get_costcenter();
                $aggregated_models['item_related_models'][$item->id]['SepaAccount'] = $aggregated_models['item_related_models'][$item->id]['CostCenter']->sepaaccount;

                $sepaaccount_id = $aggregated_models['item_related_models'][$item->id]['SepaAccount']->id;
                $aggregated_models['item_related_models'][$item->id]['SepaMandate'] = $contract->get_valid_mandate('now', $sepaaccount_id) ?: $contract->get_valid_mandate();

                // warn user if comanies differ
                $item_company = $aggregated_models['item_related_models'][$item->id]['SepaAccount']->company;
                if (! $item_company->is($company)) {
                    \Session::push('tmp_warning_above_form', trans('provbase::messages.documentTemplate.differentCompaniesAtContract', ['cont_comp' => $company->id, 'item_comp' => $item_company->id, 'item_id' => $item->id]));
                }
            };

            array_push($models, $costcenter, $sepaaccount, $company);
        }

        $models[] = $aggregated_models;

        $data = $this->_models_to_data($models);

        $this->_add_document_meta_data($data);

        ksort($data);
        return $data;
    }


    /**
     * Check given LaTeX string for dangerous commands.
     * Hints taken from:
     *  * https://0day.work/hacking-with-latex
     *  * https://stackoverflow.com/questions/3252957/how-to-execute-shell-script-from-latex
     *
     * @param   string  $latex  The LaTeX to be sanitized
     * @return  bool    true if secure, else false
     *
     * @author Patrick Reichel
     */
    public static function is_valid_latex(&$latex)
    {
        $valid = True;

        // these are commands that can be used to read/write files
        $forbidden_commands = [
            '\file',
            '\immediate',
            '\input',
            '\newread',
            '\newwrite',
            '\openin',
            '\openout',
            '\outfile',
            '\read',
            '\readFH',
            '\write',
            '\write18',
        ];

        foreach ($forbidden_commands as $cmd) {
            $variations = [
                $cmd.'{',
                $cmd.'[',
                $cmd.' ',
                $cmd."\n",
                $cmd."\t",
            ];
            foreach ($variations as $variation) {
                if (False !== strpos($latex, $variation)) {
                    \Session::push('tmp_error_above_form', trans('provbase::messages.documentTemplate.pdfAuditFailedReason', ['cmd' => $cmd]));
                    $valid = False;
                }
            }
        }

        return $valid;
    }


    /**
     * Observers
     */
    public static function boot()
    {
        self::observe(new DocumentTemplateObserver);
        parent::boot();
    }
}

class DocumentTemplateObserver
{
    /**
     * Try to get template format from filename suffix
     *
     * @author Patrick Reichel
     */
    protected function get_format($filename)
    {
        $formats = [
            'LaTeX' => ['tex', 'latex'],
        ];
        $suffix = explode('.', $filename);
        $suffix = array_pop($suffix);
        $suffix = strtolower($suffix);
        foreach ($formats as $format => $suffixes)  {
            if (in_array($suffix, $suffixes)) {
                return $format;
            }
        }

        return 'n/a';
    }

    /**
     * @author Patrick Reichel
     */
    public function creating($documenttemplate)
    {
        $documenttemplate->filename_pattern = $documenttemplate->filename_pattern ?: $documenttemplate->documenttype->get_translated_default_filename_pattern();
        $documenttemplate->format = $this->get_format($documenttemplate->file);
        if ($documenttemplate->sepaaccount_id) {
            $documenttemplate->company_id = $documenttemplate->sepaaccount->company->id;
        }
    }

    /**
     * @author Patrick Reichel
     */
    public function updating($documenttemplate)
    {
        $documenttemplate->filename_pattern = $documenttemplate->filename_pattern ?: $documenttemplate->documenttype->get_translated_default_filename_pattern();
        $documenttemplate->format = $this->get_format($documenttemplate->file);
        if ($documenttemplate->sepaaccount_id) {
            $documenttemplate->company_id = $documenttemplate->sepaaccount->company->id;
        }
    }

    /**
     * @author Patrick Reichel
     */
    public function deleting($documenttemplate)
    {
        // check if a base template shall be deleted (which is prohibited)
        if ((0 == $documenttemplate->company_id) && (0 == $documenttemplate->sepaaccount_id)) {
            \Session::push('tmp_error_above_index_list', trans('provbase::messages.documentTemplate.cannotDeleteBaseTemplate'));
            return false;
        }
    }
}

