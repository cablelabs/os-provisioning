<?php

namespace Models;

use File;
use Log;

class Modem extends \Eloquent {

	// Add your validation rules here
    // see: http://stackoverflow.com/questions/22405762/laravel-update-model-with-unique-validation-rule-for-attribute
    public static function rules($id = null)
    {
        return array(
            'hostname' => 'required|string',
            'mac' => 'required|unique:modems,mac,'.$id
        );
    }

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'contract_id', 'mac', 'status', 'public', 'network_access', 'serial_num', 'inventar_num', 'description', 'parent', 'configfile_id'];


    /**
     * all Relationships:
     */
	public function endpoints ()
	{
		return $this->hasMany('Models\Endpoint');
	}

    public function configfile ()
    {
        return $this->belongsTo('Models\Configfile');
    }


    /**
     * BOOT:
     * - init modem observer
     */
    public static function boot()
    {
        parent::boot();

        Modem::observe(new ModemObserver);
    }

    /**
     * Make DHCP config files for all CMs including EPs
     */
    public function make_dhcp ()
    {
        $dir     = '../config/';
        $file_cm = $dir.'modems.conf';
        $file_ep = $dir.'endpoints.conf';

        $ret = File::put($file_cm, '');
        $ret = File::put($file_ep, '');
        
        foreach (Modem::with('endpoints', 'configfile')->get() as $modem) 
        {
            $id    = $modem->id;
            $mac   = $modem->mac;
            $host  = $modem->hostname;
            
            /* CM */
            $data_cm = "\n".'host modem-'.$id.' { hardware ethernet '.$mac.'; filename "cm-'.$id.'.cfg"; option host-name "modem-'.$id.'"; }'; 
            $ret = File::append($file_cm, $data_cm);
            if ($ret === false)
                die("Error writing to file");

            /* Endpoint */
            if ($modem->public)
            {
                $data_ep  = "\n".'subclass "Client-Public" '.$mac.'; # CM id:'.$id;
             
                $ret = File::append($file_ep, $data_ep);
                if ($ret === false)
                    die("Error writing to file");
                
            }  
        }
    }

    /**
     * Make Configfile for a single CM
     */
    public function make_configfile ()
    {
        $modem = $this;
        $id    = $modem->id;
        $mac   = $modem->mac;
        $host  = $modem->hostname;

        /* Configfile */
        $dir     = '../config/';
        $cf_file = $dir."cm-$id.conf";

        $cf = $modem->configfile;

        if (!$cf)
            return false;

        $text = "Main\n{\n\t".$cf->text_make($modem)."\n}";
        $ret  = File::put($cf_file, $text);
            
        exec("cd /var/www/lara/config && ./docsis -e $cf_file keyfile cm-$id.cfg");
    }

    /**
     * Make all Configfiles
     */
    public function make_configfile_all()
    {
        foreach (Modem::all() as $modem) 
            $modem->make_configfile();
    }
}


/**
 * Modem Observer Class
 * Handles changes on CMs
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class ModemObserver 
{
    public function created($modem)
    {
        $modem->make_dhcp();
        $modem->make_configfile();
    }

    public function updated($modem)
    {
        $modem->make_dhcp();
        $modem->make_configfile();
    }

    public function deleted($endpoint)
    {
        $modem->make_dhcp();
    } 

    // Delete all Endpoints under CM ..
    public function deleting ($modem)
    {
        Endpoint::where('modem_id', '=', $modem->id)->delete();
    }
}
