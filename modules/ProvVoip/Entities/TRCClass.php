<?php

namespace Modules\ProvVoip\Entities;

class TRCClass extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'trcclass';

    // Don't forget to fill this array
    protected $fillable = [
        'trc_id',
        'trc_short',
        'trc_description',
    ];

    public function phonenumbermanagements()
    {
        return $this->hasMany('Modules\ProvVoip\Entities\PhonenumberManagement');
    }

    public static function trcclass_list_for_form_select()
    {
        $result = [];

        foreach (self::orderBy('trc_id')->get() as $trc) {
            $id = $trc->id;
            $short = $trc->trc_short;
            $desc = $trc->trc_description;

            $result[$id] = $short.': '.$desc;
        }

        return $result;
    }
}
