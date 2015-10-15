<?php

namespace Models;

class CmtsGw extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'type', 'ip', 'community_rw', 'community_ro', 'company', 'state', 'monitoring'];
	// columns in database that shall not be able to alter
	// protected $guarded = [];

    /**
     * Relationships:
     */

    public function ippools ()
    {
        return $this->hasMany('IpPool');
    }
}