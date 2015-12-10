<?php

namespace Modules\ProvBase\Entities;

use File;

class Endpoint extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'endpoint';

    public static function rules($id = null)
    {
        return array(
            'mac' => 'required|mac|unique:endpoint,mac,'.$id,
            'hostname' => 'unique:endpoint,hostname,'.$id
        );
    }

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'name', 'mac', 'public', 'description'];

    
    // Name of View
    public static function get_view_header()
    {
        return 'Endpoints';
    }

    // link title in index view
    public function get_view_link_title()
    {
        return $this->hostname;
    }



    /**
     * all Relationships:
     */


    /**
     * BOOT:
     * - init modem observer
     */
    public static function boot()
    {
        parent::boot();

        Endpoint::observe(new EndpointObserver);
        Endpoint::observe(new \SystemdObserver);
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
        
        // chown for future writes in case this function was called from CLI via php artisan nms:dhcp that changes owner to 'root'
        system('/bin/chown -R apache /etc/dhcp/');
        
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