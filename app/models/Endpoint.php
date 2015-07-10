<?php

namespace Models;


class Endpoint extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		 'hostname' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'mac', 'public', 'description', 'modem_id'];

    /**
     * all Relationships:
     */
	public function modem ()
	{
		return $this->belongsTo('Models\Modem');
	}
    
    /**
     * BOOT:
     * - init modem observer
     */
    public static function boot()
    {
        parent::boot();

        Endpoint::observe(new EndpointObserver);
    }
}


class EndpointObserver {
    
    public function created($endpoint)
    {
        // $endpoint->modem->make_dhcp();
    }

    public function updated($endpoint)
    {
        // $endpoint->modem->make_dhcp();
    }

    public function deleted($endpoint)
    {
        // $endpoint->modem->make_dhcp();
    }
}