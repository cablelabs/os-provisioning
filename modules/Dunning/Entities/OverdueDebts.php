<?php

namespace Modules\OverdueDebts\Entities;

class OverdueDebts extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'overduedebts';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'fee' => 'nullable|numeric',
        ];
    }

    /**
     * View related stuff
     */

    // Name of View
    public static function view_headline()
    {
        return 'OverdueDebts';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa-envelope"></i>';
    }

    // link title in index view
    public function view_index_label()
    {
        return $this->view_headline();
    }
}
