<?php

namespace Modules\HfcReq\Entities;

class HfcReq extends \BaseModel
{
    // The associated SQL table for this Model
    protected $table = 'hfcreq';

    // Add your validation rules here
    public function rules()
    {
        return [
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'HfcReq Config';
    }

    // link title in index view
    public function view_index_label()
    {
        return 'HfcReq';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-random"></i>';
    }
}
