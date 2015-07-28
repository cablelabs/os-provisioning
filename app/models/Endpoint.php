<?php

namespace Models;

use File;

class Endpoint extends \Eloquent {


    public static function rules($id = null)
    {
        return array(
            'mac' => 'required|mac|unique:endpoints,mac,'.$id,
            'hostname' => 'unique:endpoints,hostname,'.$id
        );
    }

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'name', 'mac', 'public', 'description'];

    /**
     * all Relationships:
     */

/* depracted:
	public function modem ()
	{
		return $this->belongsTo('Models\Modem');
	}
*/

    /**
     * BOOT:
     * - init modem observer
     */
    public static function boot()
    {
        parent::boot();

        Endpoint::observe(new EndpointObserver);
    }

    /**
     * Make DHCP config files for EPs
     */
    public function make_dhcp ()
    {
        $dir = '/etc/dhcp/nms/';
        $file_ep = $dir.'endpoints-host.conf';

        $ret = File::put($file_ep, '');
        
        foreach (Endpoint::all() as $ep) 
        {
            $id     = $ep->id;
            $mac    = $ep->mac;
            $host   = $ep->hostname;
            
            $data_ep = "\n".'host ep-'.$id.' { hardware ethernet '.$mac.'; ddns-hostname "'.$host.'"; }'; 
            $ret = File::append($file_ep, $data_ep);
            if ($ret === false)
                die("Error writing to file");
        }
        
        return ($ret > 0 ? true : false);
    }    
}


class EndpointObserver {
    
    public function created($endpoint)
    {
        $endpoint->make_dhcp();
        
        if ($endpoint->hostname == '')
        {
            $endpoint->hostname = 'ep-'.$endpoint->id;
            $endpoint->save();
        }
    }

    public function updating($endpoint)
    {
        if ($endpoint->hostname == '')
        {
            $endpoint->hostname = 'ep-'.$endpoint->id;
        }
    }

    public function updated($endpoint)
    {
        $endpoint->make_dhcp();
    }

    public function deleted($endpoint)
    {
        $endpoint->make_dhcp();
    }
}