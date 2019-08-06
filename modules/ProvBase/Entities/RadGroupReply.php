<?php

namespace Modules\ProvBase\Entities;

class RadGroupReply extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'radgroupreply';

    public $timestamps = false;
    protected $forceDeleting = true;

    // this is 0 since nmsprime.qos.id can never be that value
    public static $defaultGroup = 0;

    // https://wiki.mikrotik.com/wiki/Manual:RADIUS_Client
    // https://help.ubnt.com/hc/en-us/articles/204977464-EdgeRouter-PPPoE-Server-Rate-Limiting-Using-WISPr-RADIUS-Attributes
    public static $radiusAttributes = [
        'ds_rate_max_help' => [
            'Ascend-Xmit-Rate',
            'WISPr-Bandwidth-Max-Down',
        ],
        'us_rate_max_help' => [
            'Ascend-Data-Rate',
            'WISPr-Bandwidth-Max-Up',
        ],
    ];

    // freeradius-mysql does not use softdeletes
    public static function bootSoftDeletes()
    {
    }
}
