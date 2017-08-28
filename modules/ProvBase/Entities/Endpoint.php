<?php

namespace Modules\ProvBase\Entities;

use File;

class Endpoint extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'endpoint';

    public static function rules($id = null)
    {
        return array(
            'mac' => 'required|mac|unique:endpoint,mac,'.$id.',id,deleted_at,NULL',
            'hostname' => 'unique:endpoint,hostname,'.$id.',id,deleted_at,NULL'
        );
    }


    // Name of View
    public static function view_headline()
    {
        return 'Endpoints';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-map-marker"></i>';
    }


    // link title in index view
    public function view_index_label()
    {
        $bsclass = 'success';


        return ['index' => [$this->hostname, $this->mac, $this->description],
                'index_header' => ['Hostname', 'MAC Address', 'Description'],
                'bsclass' => $bsclass,
                'header' => $this->hostname];
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
        Endpoint::observe(new \App\SystemdObserver);
    }

    /**
     * Make DHCP config files for EPs
     */
    public static function make_dhcp ()
    {
        $dir = '/etc/dhcp/nms/';
        $file_ep = $dir.'endpoints-host.conf';

        $data = '';

        foreach (Endpoint::all() as $ep)
        {
            $id     = $ep->id;
            $mac    = $ep->mac;
            $host   = $ep->hostname;

            $data .= 'host ep-'.$id.' { hardware ethernet '.$mac.'; ddns-hostname "'.$host.'"; }'."\n";
        }

        $ret = File::put($file_ep, $data);
        if ($ret === false)
            die("Error writing to file");

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
