<?php

namespace Modules\ProvBase\Entities;

class Document extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'document';

    // Name of View
    public static function view_headline()
    {
        return 'Documents';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-tag"></i>';
    }

    // There are no validation rules
    public function rules()
    {
        return [
        ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        return $bsclass;
    }
}

