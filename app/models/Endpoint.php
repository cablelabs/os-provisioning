<?php

namespace Models;


class Endpoint extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		 'hostname' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'mac', 'public', 'description', 'modem_id'];

	public function modem ()
	{
		return $this->belongsTo('Models\Modem');
	}

    public static function boot()
    {
        parent::boot();

        Endpoint::observe(new EndpointObserver);
    }
}


class EndpointObserver {

    public function updated($endpoint)
    {
        exec("logger \"update on Endpoint with ID \"".$endpoint->id);

        $file = 'endpoints.conf';

        $ret = File::put($file, '');

        foreach (endpoint::where('public', 1)->with('modem')->get() as $endpoint) 
        {
            $id   = $endpoint->id;
            $mac  = $endpoint->mac;
            $modem_mac = $endpoint->modem->mac;
            $host = $endpoint->hostname;

            if ($endpoint->public)
            {
            	$data  = "\n".'subclass "Client-Public" '.$modem_mac.';';
            
	            $ret = File::append($file, $data);
	            if ($ret === false)
	            {
	                die("Error writing to file");
	            }  
	        }    
        }
    }
}