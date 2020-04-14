<?php

namespace Modules\ProvBase\Entities;

class RadAcct extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'radacct';

    public $timestamps = false;
    protected $forceDeleting = true;

    // freeradius-mysql does not use softdeletes
    public static function bootSoftDeletes()
    {
    }
}
