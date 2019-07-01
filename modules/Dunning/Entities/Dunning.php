<?php

namespace Modules\Dunning\Entities;

class Dunning extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'dunning';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'fee' => 'numeric',
        ];
    }

    /**
     * View related stuff
     */

    // Name of View
    public static function view_headline()
    {
        return 'Dunning';
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
