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

	public function endpoints ()
	{
		return $this->hasMany('Models\Endpoint');
	}

    public function configfile ()
    {
        return $this->belongsTo('Models\Configfile');
    }

    public static function boot()
    {
        parent::boot();

        Modem::observe(new ModemObserver);
    }
}

class ModemObserver {

    public function updated($modem)
    {
        exec("logger \"update on Modem\"");

        $dir     = '../config/';
        $file_cm = $dir.'modems.conf';
        $file_ep = $dir.'endpoints.conf';

        $ret = File::put($file_cm, '');
        $ret = File::put($file_ep, '');

        foreach (Modem::all() as $modem) 
        {
            $id   = $modem->id;
            $mac  = $modem->mac;
            $host = $modem->hostname;

            $data_cm = "\n".'host modem-'.$id.' { hardware ethernet '.$mac.'; filename "cm-'.$id.'.cfg"; option host-name "modem-'.$id.'"; }';
         
            /* CM */
            $ret = File::append($file_cm, $data_cm);
            if ($ret === false)
            {
                die("Error writing to file");
            }

            /* Endpoint */
            if ($modem->public)
            {
                $data_ep  = "\n".'subclass "Client-Public" '.$mac.'; # CM id:'.$id;
            
                $ret = File::append($file_ep, $data_ep);
                if ($ret === false)
                {
                    die("Error writing to file");
                }  
            }       
        }

    }

    public function deleting ($modem)
    {
    	$id = $modem->id;
    	
    	Endpoint::where('modem_id', '=', $id)->delete();
    } 


}
