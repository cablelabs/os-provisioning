<?php

namespace Modules\ProvVoip\Entities;

use Modules\ProvBase\Entities\ProvBase;

class ProvVoip extends \BaseModel
{
    // The associated SQL table for this Model
    protected $table = 'provvoip';

    // Don't forget to fill this array
    // protected $fillable = ['startid_mta'];

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'ProvVoip Config';
    }

    // link title in index view
    public function view_index_label()
    {
        return 'ProvVoip';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-phone"></i>';
    }

    public static function boot()
    {
        parent::boot();

        self::observe(new ProvVoipObserver);
        self::observe(new \App\SystemdObserver);
    }
}

class ProvVoipObserver
{
    public function updated($provvoip)
    {
        ProvBase::first()->make_dhcp_glob_conf();
    }
}
