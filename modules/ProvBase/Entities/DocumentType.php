<?php

namespace Modules\ProvBase\Entities;

class DocumentType extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'documenttype';

    // Name of View
    public static function view_headline()
    {
        return 'DocumentTypes';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-tag"></i>';
    }

    // There are no validation rules
    public static function rules($id = null)
    {
        return [
        ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        return $bsclass;
    }

    public function documenttemplates()
    {
        return $this->hasMany('Modules\ProvBase\Entities\DocumentTemplate');
    }

    /**
     * Returns data for use in controller edit selects.
     *
     * @author Patrick Reichel
     */
    public static function get_types_for_edit_view() {

        $ret = [];
        $doctypes = DocumentType::all();
        foreach ($doctypes as $doctype) {
            if (($doctype->usable) && ($doctype->type != 'upload')) {

                // add if the doctype module is enabled
                $module = $doctype->module;
                if (\Module::collections()->has($module)) {
                    $ret[$doctype->id] = $doctype->type_view;
                }
            }
        }
        asort($ret);
        return $ret;
    }

    public function get_translated_default_filename_pattern() {
        $pattern = $this->default_filename_pattern;

        preg_match_all('#\[[^\]]*]#', $pattern, $matches);
        while ($tmp = array_pop($matches)) {
            foreach ($tmp as $match) {
                $pattern = str_replace($match, str_replace(' ', '_', $this->type_view), $pattern);
            }
        }

        return $pattern;
    }
}

