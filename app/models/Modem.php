<?php

namespace Models;

use File;
use Log;
use Models\Qos;

class Modem extends \BaseModel {

    // The associated SQL table for this Model
    protected $table = 'modem';


	// Add your validation rules here
    // see: http://stackoverflow.com/questions/22405762/laravel-update-model-with-unique-validation-rule-for-attribute
    public static function rules($id = null)
    {
        return array(
            'mac' => 'required|mac|unique:modem,mac,'.$id
        );
    }

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'name', 'contract_id', 'mac', 'status', 'public', 'network_access', 'serial_num', 'inventar_num', 'description', 'parent', 'configfile_id', 'qos_id'];


    /**
     * return all Configfile Objects for CMs
     */
    private function configfiles ()
    {
        return Configfile::where('device', '=', 'CM')->where('public', '=', 'yes')->get();
    }


    public function html_list_array ()
    {
        $ret = array (
                'configfiles' => $this->html_list($this->configfiles(), 'name'),
                'qualities' => $this->html_list(Qos::all(), 'name')
            );
        return $ret;
    }


    /**
     * all Relationships:
     */
    public function configfile ()
    {
        return $this->belongsTo('Models\Configfile');
    }

    public function qos()
    {
        return $this->belongsTo("Models\Qos");
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
     * Define global constants for dhcp config files of modems (private and public)
     */
    const CONF_FILE_PATH = '/etc/dhcp/nms/modems-host.conf';
    const CONF_FILE_PATH_PUB = '/etc/dhcp/nms/modems-clients-public.conf';


    /**
     * Returns the config file entry string for a cable modem in dependency of private or public ip
     *
     * @author Nino Ryschawy
     */
    private function generate_cm_update_entry($id, $mac)
    {
            return "\n".'host cm-'.$id.' { hardware ethernet '.$mac.'; filename "cm/cm-'.$id.'.cfg"; ddns-hostname "cm-'.$id.'"; }';
    }
    private function generate_cm_update_entry_pub($id, $mac)
    {
            return "\n".'subclass "Client-Public" '.$mac.'; # CM id:'.$id;
    }


    /**
     * Deletes the configfiles with all modem dhcp entries - used to refresh the config through artisan nms:dhcp command
     *
     * @author Nino Ryschawy
     */
    public function del_dhcp_conf_files()
    {
        if (file_exists(self::CONF_FILE_PATH)) unlink(self::CONF_FILE_PATH);
        if (file_exists(self::CONF_FILE_PATH_PUB)) unlink(self::CONF_FILE_PATH_PUB);
    }



    /**
     * Make DHCP config files for all CMs including EPs - used in dhcpCommand after deleting 
     * the config files with all entries
     *
     * @author Torsten Schmidt
     */
    public function make_dhcp_cm_all ()
    {        
        foreach (Modem::all() as $modem) 
        {
            $id    = $modem->id;
            $mac   = $modem->mac;

            if ($id == 0)
                continue;
            
            // all        
            $data = $modem->generate_cm_update_entry($id, $mac);
            $ret = File::append(self::CONF_FILE_PATH, $data);
            if ($ret === false)
                die("Error writing to file");

            // public ip
            if ($modem->public)
            {
                $data = $modem->generate_cm_update_entry_pub($id, $mac);
                $ret = File::append(self::CONF_FILE_PATH_PUB, $data);
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

    /**
     * Deletes Configfile of a modem
     */
    public function delete_configfile()
    {
        $file['1'] = 'cm-'.$this->id.'cfg';
        $file['2'] = 'cm-'.$this->id.'conf';

        foreach ($file as $f) 
        {
            if (file_exists($f)) unlink($file);
        }
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
        $modem->make_dhcp_cm_all();
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
        $modem->make_dhcp_cm_all();
        $modem->make_configfile();
    }

    public function deleted($modem)
    {
        $modem->make_dhcp_cm_all();
        $modem->delete_configfile();
    } 

    // Delete all Endpoints under CM ..
    public function deleting ($modem)
    {
        /* depracted:
        Endpoint::where('modem_id', '=', $modem->id)->delete();
        */
    }
}
