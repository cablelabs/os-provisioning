<?php

namespace Modules\ProvBase\Entities;

use File;
use Log;
use Exception;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvBase\Entities\ProvBase;

class Modem extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'modem';


	// Add your validation rules here
    // see: http://stackoverflow.com/questions/22405762/laravel-update-model-with-unique-validation-rule-for-attribute
    public static function rules($id = null)
    {
        return array(
            'mac' => 'required|mac|unique:modem,mac,'.$id
        );
    }

    
    // Name of View
    public static function get_view_header()
    {
        return 'Modems';
    }

    // link title in index view
    public function get_view_link_title()
    {
        return $this->hostname.' - '.$this->mac;
    }


    /**
     * return all Configfile Objects for CMs
     */
    public function configfiles ()
    {
        return Configfile::where('device', '=', 'CM')->where('public', '=', 'yes')->get();
    }

    /**
     * return all Configfile Objects for CMs
     */
    public function qualities ()
    {
        return QoS::all();
    }


    /**
     * all Relationships:
     */
    public function configfile ()
    {
        return $this->belongsTo('Modules\ProvBase\Entities\Configfile');
    }

    public function qos()
    {
        return $this->belongsTo("Modules\ProvBase\Entities\Qos");
    }

    public function contract()
    {
        return $this->belongsTo('Modules\ProvBase\Entities\Contract', 'contract_id');
    }

    public function contracts()
    {
        return Contract::get();
    }

    public function mtas()
    {
        if ($this->module_is_active('ProvVoip'))
            return $this->hasMany('Modules\ProvVoip\Entities\Mta');

        return null;
    }
    
    public function tree()
    {
        if ($this->module_is_active('HfcBase'))
            return $this->belongsTo('Modules\HfcBase\Entities\Tree');

        return null;
    }


    /*
     * Relation Views
     */
    public function view_belongs_to ()
    {
        return $this->contract;
    }

    public function view_has_many()
    {
        if ($this->module_is_active('ProvVoip'))
            return array(
                    'Mta' => $this->mtas
                );

        return array();
    }


    /**
     * BOOT:
     * - init modem observer
     */
    public static function boot()
    {
        parent::boot();

        Modem::observe(new \SystemdObserver);
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
        $this->del_dhcp_conf_files();
        
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

        // chown for future writes in case this function was called from CLI via php artisan nms:dhcp that changes owner to 'root'
        system('/bin/chown -R apache /etc/dhcp/');

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
        $dir        = '/tftpboot/cm/';
        $cf_file    = $dir."cm-$id.conf";
        $cfg_file   = $dir."cm-$id.cfg";

        $cf = $modem->configfile;

        if (!$cf)
            return false;

        $text = "Main\n{\n\t".$cf->text_make($modem, "modem")."\n}";
        $ret  = File::put($cf_file, $text);


        if ($ret === false)
                die("Error writing to file");

        Log::info("/usr/local/bin/docsis -e $cf_file $dir/../keyfile $cfg_file");
        if (file_exists($cfg_file))
            unlink($cfg_file);

        // "&" to start docsis process in background improves performance but we can't reliably proof if file exists anymore
        exec("/usr/local/bin/docsis -e $cf_file $dir/../keyfile $cfg_file >/dev/null 2>&1", $out);

        // change owner in case command was called from command line via php artisan nms:configfile that changes owner to root
        system('/bin/chown -R apache /tftpboot/cm');

        // docsis tool always returns 0 -> so we need to proof if that way
        if (file_exists($cfg_file))
            return true;
        return false;
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
        $dir = '/tftpboot/cm/';
        $file['1'] = $dir.'cm-'.$this->id.'.cfg';
        $file['2'] = $dir.'cm-'.$this->id.'.conf';

        foreach ($file as $f) 
        {
            if (file_exists($f)) unlink($f);
        }
    }

    /**
     * Restarts modem through snmpset
     */    
    public function restart_modem()
    {
        $config = ProvBase::first();
        $community_rw = $config->rw_community;
        $domain = $config->domain_name;

        // if hostname cant be resolved we dont want to have an php error
        try
        {
            // restart modem - TODO: get community string and domain name from global config page, NOTE: OID from MIB: DOCS-CABLE-DEV-MIB::docsDevResetNow
            snmpset($this->hostname.'.'.$domain, $community_rw, "1.3.6.1.2.1.69.1.1.3.0", "i", "1", 300000, 1);
        }
        catch (Exception $e)
        {
            // only ignore error with this error message (catch exception with this string) 
            if (((strpos($e->getMessage(), "php_network_getaddresses: getaddrinfo failed: Name or service not known") !== false) || (strpos($e->getMessage(), "snmpset(): No response from") !== false)))
            {
                // check if observer is called from HTML Update, otherwise skip
                if (\Request::method() == 'PUT') 
                {
                    // redirect back with corresponding message over flash, needs to be saved as it's normally only saved when the session middleware terminates successfully
                    $resp = \Redirect::back()->with('message', 'Could not restart Modem! (not online? - error in configfile?)'); 
                    \Session::driver()->save();         // \ is like writing "use Session;" before class statement
                    $resp->send();

                    /* 
                     * TODO: replace exit
                     * This is a security hassard. All Code (Observer etc) which should run after this code will not be executed !
                     */
                    exit();
                }
            }
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
        $modem->save();     // forces to call the updated method of the observer
    }

    public function updated($modem)
    {
        $modem->make_dhcp_cm_all();
        $modem->make_configfile();
        $modem->restart_modem();
    }

    public function deleted($modem)
    {
        $modem->make_dhcp_cm_all();
        $modem->delete_configfile();
        $modem->restart_modem();
    }
}
