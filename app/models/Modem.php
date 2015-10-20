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
            'mac' => 'required|mac|unique:modems,mac,'.$id
        );
    }

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'name', 'contract_id', 'mac', 'status', 'public', 'network_access', 'serial_num', 'inventar_num', 'description', 'parent', 'configfile_id', 'quality_id'];


    /**
     * all Relationships:
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
     * Define global constant for dhcp config file of modems
     */
    const CONF_FILE_PATH = '/etc/dhcp/nms/modems-host.conf';


    /**
     * Make DHCP config files for all CMs including EPs
     *
     * @author Nino Ryschawy
     */
    private function generate_cm_update_entry($id, $mac)
    {
        return "\n".'host cm-'.$id.' { hardware ethernet '.$mac.'; filename "cm/cm-'.$id.'.cfg"; ddns-hostname "cm-'.$id.'"; }';
    }


    /**
     * Deletes the configfile with all modem dhcp entries for refresh the config through artisan nms:dhcp command
     *
     * @author Nino Ryschawy
     */
    public function del_dhcp_conf_file()
    {
        if (file_exists(self::CONF_FILE_PATH)) unlink(self::CONF_FILE_PATH);
    }


    /**
     * Creates or updates DHCP config file entry for a single CM in the dhcp CM config file
     *
     * @author Nino Ryschawy
     */
    public function make_dhcp_cm()
    {
        $id    = $this->id;
        $mac   = $this->mac;

        if ($id == 0)
            return -1;

        // search file for already existing host-string
        $search_str = 'host cm-'.$id;
        $updated = false;
        $ret = 1;

        if (file_exists(self::CONF_FILE_PATH))
        {
            $lines = file(self::CONF_FILE_PATH);

            foreach ($lines as $key => $line) 
            {
                // entry already exists - update!
                if (strpos($line, $search_str) !== false )
                {
                    $lines[$key] = $this->generate_cm_update_entry($id, $mac);
                    $updated = true;
                }
            }
        }

        if ($updated)
        {
            // write file
            $data = implode(array_values($lines));
            $file_cm = fopen(self::CONF_FILE_PATH, 'w');

            fwrite($file_cm, $data);
            fclose($file_cm);
        }
        else
        {
            // add new entry
            $data = $this->generate_cm_update_entry($id, $mac);
            $ret = File::append(self::CONF_FILE_PATH, $data);
            if ($ret === false)
                die("Error writing to file");
        }        

        return ($ret > 0 ? true : false);
    }


    /**
     * Make DHCP config files for all CMs including EPs
     *
     * @author Torsten Schmidt
     */
    public function make_dhcp_cm_all ()
    {
        $ret = File::put(self::CONF_FILE_PATH, '');
        
        foreach (Modem::all() as $modem) 
        {
            $id    = $modem->id;
            $mac   = $modem->mac;

            if ($id == 0)
                continue;
            
            $data = $modem->generate_cm_update_entry($id, $mac);
            $ret = File::append(self::CONF_FILE_PATH, $data);
            if ($ret === false)
                die("Error writing to file"); 
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
        $m = Modem::all();
        foreach ($m as $modem) 
        {
            if ($modem->id == 0)
                continue;
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
        $modem->make_dhcp_cm();
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
        $modem->make_dhcp_cm();
        $modem->make_configfile();
    }

    public function deleted($modem)
    {
        $modem->make_dhcp_cm();
    } 

    // Delete all Endpoints under CM ..
    public function deleting ($modem)
    {
        /* depracted:
        Endpoint::where('modem_id', '=', $modem->id)->delete();
        */
    }
}
