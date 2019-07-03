<?php

namespace Modules\ProvBase\Entities;

class DocumentTemplate extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'documenttemplate';

    public $guarded = ['file_upload', 'id_for_validation'];

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

