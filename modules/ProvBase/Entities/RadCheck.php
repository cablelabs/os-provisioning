<?php

namespace Modules\ProvBase\Entities;

class RadCheck extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'radcheck';

    public $timestamps = false;
    protected $forceDeleting = true;

    // freeradius-mysql does not use softdeletes
    public static function bootSoftDeletes()
    {
    }
}
