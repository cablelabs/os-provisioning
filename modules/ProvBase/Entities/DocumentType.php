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
}
