<?php

namespace Modules\ProvBase\Entities;

class DocumentTemplate extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'documenttemplate';

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

    // There are no validation rules
    public static function rules($id = null)
    {
        return [
            'name' => 'regex:/^[0-9A-Za-z\.\-\_]+$/|required',
            'type' => 'required',
        ];
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
        return $this->belongsTo('Modules\BillingBase\Entities\SepaAccount');
    }
}
