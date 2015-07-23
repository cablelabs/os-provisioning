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
            'mac' => 'required|unique:modems,mac,'.$id
        );
    }

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'name', 'contract_id', 'mac', 'status', 'public', 'network_access', 'serial_num', 'inventar_num', 'description', 'parent', 'configfile_id', 'quality_id'];


    /**
     * all Relationships:
     */

/* depracted:
	public function endpoints ()
	{
		return $this->hasMany('Models\Endpoint');
	}
*/

    public function configfile ()
    {
        return $this->belongsTo('Models\Configfile');
    }

    public function quality()
    {
        return $this->belongsTo("Models\Quality");
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
        $dir = '/etc/dhcp/nms/';
        $file_cm = $dir.'modems-host.conf';
        $file_ep = $dir.'modems-clients-public.conf';

        $ret = File::put($file_cm, '');
        $ret = File::put($file_ep, '');
        
        foreach (Modem::all() as $modem) 
        {
            $id    = $modem->id;
            $mac   = $modem->mac;
            $host  = $modem->hostname;
            
            /* CM */
            $data_cm = "\n".'host cm-'.$id.' { hardware ethernet '.$mac.'; filename "cm/cm-'.$id.'.cfg"; ddns-hostname "cm-'.$id.'"; }'; 
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

        return ($ret > 0 ? true : false);
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
        $dir     = '/tftpboot/cm/';
        $cf_file = $dir."cm-$id.conf";

        $cf = $modem->configfile;

        if (!$cf)
            return false;

        $text = "Main\n{\n\t".$cf->text_make($modem)."\n}";
        $ret  = File::put($cf_file, $text);

        
        if ($ret === false)
                die("Error writing to file");
         
        Log::info("/usr/local/bin/docsis -e $cf_file $dir/../keyfile $dir/cm-$id.cfg");   
        exec("/usr/local/bin/docsis -e $cf_file $dir/../keyfile $dir/cm-$id.cfg", $out, $ret);

        return ($ret == 0 ? true : false);
    }

    /**
     * Make all Configfiles
     */
    public function make_configfile_all()
    {
        foreach (Modem::all() as $modem) 
        {
            if (!$modem->make_configfile())
                Log::warning("failed to build/write configfile for modem cm-".$modem->id);
        }

        return true;
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
        $modem->hostname = 'cm-'.$modem->id;
        $modem->save();
    }

    public function updating($modem)
    {
        $modem->hostname = 'cm-'.$modem->id;
    }

    public function updated($modem)
    {
        $modem->make_dhcp();
        $modem->make_configfile();
    }

    public function deleted($modem)
    {
        $modem->make_dhcp();
    } 

    // Delete all Endpoints under CM ..
    public function deleting ($modem)
    {
        /* depracted:
        Endpoint::where('modem_id', '=', $modem->id)->delete();
        */
    }
}
