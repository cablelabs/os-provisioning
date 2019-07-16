<?php

namespace Modules\ProvBase\Entities;

use Log;
use File;
use Exception;
use GlobalConfig;
use Acme\php\ArrayHelper;

class Modem extends \BaseModel
{
    // get functions for some address select options
    use \App\AddressFunctionsTrait;

    // The associated SQL table for this Model
    public $table = 'modem';

    // Add your validation rules here
    // see: http://stackoverflow.com/questions/22405762/laravel-update-model-with-unique-validation-rule-for-attribute
    public static function rules($id = null)
    {
        return [
            'mac' => 'required|mac|unique:modem,mac,'.$id.',id,deleted_at,NULL',
            'birthday' => 'date',
            'country_code' => 'regex:/^[A-Z]{2}$/',
            'contract_id' => 'required|exists:contract,id,deleted_at,NULL',
            'configfile_id' => 'required|exists:configfile,id,deleted_at,NULL,device,cm,public,yes',
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'Modems';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-hdd-o"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        // we need to put the filter into the session,
        // as the upcoming datatables AJAX request won't carry the input parameters
        if (\Input::has('modem_show_filter')) {
            \Session::put('modem_show_filter', \Input::get('modem_show_filter'));
        }
        // non-datatable request; current route is null on testing
        elseif (\Route::getCurrentRoute() && basename(\Route::getCurrentRoute()->uri) == 'Modem') {
            \Session::forget('modem_show_filter');
        }

        return ['table' => $this->table,
                'index_header' => [$this->table.'.id', $this->table.'.mac', 'configfile.name', $this->table.'.model', $this->table.'.sw_rev', $this->table.'.name', $this->table.'.firstname', $this->table.'.lastname', $this->table.'.city', $this->table.'.district', $this->table.'.street', $this->table.'.house_number', $this->table.'.us_pwr', $this->table.'.geocode_source', $this->table.'.inventar_num', 'contract_valid'],
                'bsclass' => $bsclass,
                'header' => $this->id.' - '.$this->mac.($this->name ? ' - '.$this->name : ''),
                'edit' => ['us_pwr' => 'get_us_pwr', 'contract_valid' => 'get_contract_valid'],
                'eager_loading' => ['configfile', 'contract'],
                'disable_sortsearch' => ['contract_valid' => 'false'],
                'help' => [$this->table.'.model' => 'modem_update_frequency', $this->table.'.sw_rev' => 'modem_update_frequency'],
                'order_by' => ['0' => 'desc'],
                'where_clauses' => self::_get_where_clause(),
            ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        switch ($this->get_state('int')) {
            case 0:	$bsclass = 'success'; break; // online
            case 1: $bsclass = 'warning'; break; // warning
            case 2: $bsclass = 'warning'; break; // critical
            case 3: $bsclass = $this->internet_access && $this->contract->check_validity('Now') ? 'danger' : 'info'; break; // offline

            default: $bsclass = 'danger'; break;
        }

        return $bsclass;
    }

    public function get_contract_valid()
    {
        return $this->contract->check_validity('Now') ? \App\Http\Controllers\BaseViewController::translate_label('yes') : \App\Http\Controllers\BaseViewController::translate_label('no');
    }

    public function get_us_pwr()
    {
        return $this->us_pwr.' dBmV';
    }

    /**
     * Get WHERE clause for datatable filtering.
     *
     * @author Ole Ernst
     */
    private static function _get_where_clause()
    {
        $filter = \Session::get('modem_show_filter');

        if ($filter) {
            return ["sw_rev = '$filter'"];
        } else {
            return [];
        }
    }

    /**
     * return all Configfile Objects for CMs
     */
    public function configfiles()
    {
        return \DB::table('configfile')->select(['id', 'name'])->whereNull('deleted_at')->where('device', '=', 'CM')->where('public', '=', 'yes')->get();
        // return Configfile::select(['id', 'name'])->where('device', '=', 'CM')->where('public', '=', 'yes')->get();
    }

    /**
     * return all Configfile Objects for CMs
     */
    public function qualities()
    {
        return \DB::table('qos')->whereNull('deleted_at')->get();
    }

    /**
     * all Relationships:
     */

    /**
     * Get relation to envia orders.
     *
     * @author Patrick Reichel
     */
    protected function _envia_orders()
    {
        if (! \Module::collections()->has('ProvVoipEnvia')) {
            throw new \LogicException(__METHOD__.' only callable if module ProvVoipEnvia as active');
        }

        return $this->hasMany('Modules\ProvVoipEnvia\Entities\EnviaOrder')->where('ordertype', 'NOT LIKE', 'order/create_attachment');
    }

    /**
     * related enviacontracts
     */
    public function enviacontracts()
    {
        if (! \Module::collections()->has('ProvVoipEnvia')) {
            throw new \LogicException(__METHOD__.' only callable if module ProvVoipEnvia as active');
        } else {
            return $this->hasMany('Modules\ProvVoipEnvia\Entities\EnviaContract');
        }
    }

    public function configfile()
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

    /**
     * Return all Contracts
     * NOTE: Dont use Eloquent here as it's super slow for many models and we dont need the Eloquent instance here
     */
    public function contracts()
    {
        // Contract::select(['id', 'lastname'])->get();
        return \DB::table('contract')->whereNull('deleted_at')->get();
    }

    public function mtas()
    {
        return $this->hasMany('Modules\ProvVoip\Entities\Mta');
    }

    public function endpoints()
    {
        return $this->hasMany('Modules\ProvBase\Entities\Endpoint');
    }

    // TODO: deprecated! use netelement function instead - search for all places where this function is used
    public function tree()
    {
        return $this->belongsTo('Modules\HfcReq\Entities\NetElement');
    }

    public function nelelement()
    {
        return $this->belongsTo('Modules\HfcReq\Entities\NetElement', 'netelement_id');
    }

    /*
     * Relation Views
     */
    public function view_belongs_to()
    {
        return $this->contract;
    }

    public function view_has_many()
    {
        $ret = [];

        // we use a dummy here as this will be overwritten by ModemController::editTabs()
        if (\Module::collections()->has('ProvVoip')) {
            $ret['Edit']['Mta']['class'] = 'Mta';
            $ret['Edit']['Mta']['relation'] = $this->mtas;
        }

        // only show endpoints (and thus the ability to create a new one) for public CPEs
        if ($this->public) {
            $ret['Edit']['Endpoint']['class'] = 'Endpoint';
            $ret['Edit']['Endpoint']['relation'] = $this->endpoints;
        }

        if (\Module::collections()->has('ProvVoipEnvia')) {
            $ret['Edit']['EnviaContract']['class'] = 'EnviaContract';
            $ret['Edit']['EnviaContract']['relation'] = $this->enviacontracts;
            $ret['Edit']['EnviaContract']['options']['hide_create_button'] = 1;
            $ret['Edit']['EnviaContract']['options']['hide_delete_button'] = 1;

            $ret['Edit']['EnviaOrder']['class'] = 'EnviaOrder';
            $ret['Edit']['EnviaOrder']['relation'] = $this->_envia_orders;
            $ret['Edit']['EnviaOrder']['options']['delete_button_text'] = 'Cancel order at envia TEL';

            // TODO: auth - loading controller from model could be a security issue ?
            $ret['Edit']['envia TEL API']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
            $ret['Edit']['envia TEL API']['view']['vars']['extra_data'] = \Modules\ProvBase\Http\Controllers\ModemController::_get_envia_management_jobs($this);
        }

        return $ret;
    }

    /**
     * BOOT:
     * - init modem observer
     */
    public static function boot()
    {
        Log::debug(__METHOD__.' started');

        parent::boot();

        self::observe(new \App\SystemdObserver);
        self::observe(new ModemObserver);
    }

    /**
     * Define global constants for dhcp config files of modems (private and public)
     */
    const CONF_FILE_PATH = '/etc/dhcp-nmsprime/modems-host.conf';
    const CONF_FILE_PATH_PUB = '/etc/dhcp-nmsprime/modems-clients-public.conf';
    const IGNORE_CPE_FILE_PATH = '/etc/dhcp-nmsprime/ignore-cpe.conf';

    /**
     * Returns the config file entry string for a cable modem in dependency of private or public ip
     *
     * TODO: use object context instead of parameters (Torsten)
     *
     * @author Nino Ryschawy
     */
    private function generate_cm_dhcp_entry($server = '')
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        // FF-00-00-00-00 to FF-FF-FF-FF-FF reserved according to RFC7042
        if (stripos($this->mac, 'ff:') === 0) {
            return;
        }

        $ret = 'host cm-'.$this->id.' { hardware ethernet '.$this->mac.'; filename "cm/cm-'.$this->id.'.cfg"; ddns-hostname "cm-'.$this->id.'";';

        if (\Module::collections()->has('ProvVoip') && $this->mtas()->pluck('mac')->filter(function ($mac) {
            return stripos($mac, 'ff:') !== 0;
        })->count()) {
            $ret .= ' option ccc.dhcp-server-1 '.($server ?: ProvBase::first()->provisioning_server).';';
        }

        return $ret."}\n";
    }

    private function generate_cm_dhcp_entry_pub()
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        // FF-00-00-00-00 to FF-FF-FF-FF-FF reserved according to RFC7042
        if (stripos($this->mac, 'ff:') !== 0) {
            return 'subclass "Client-Public" '.$this->mac.'; # CM id:'.$this->id."\n";
        }
    }

    /**
     * Deletes the configfiles with all modem dhcp entries - used to refresh the config through artisan nms:dhcp command
     *
     * @author Nino Ryschawy
     */
    public static function clear_dhcp_conf_files()
    {
        File::put(self::CONF_FILE_PATH, '');
        File::put(self::CONF_FILE_PATH_PUB, '');
    }

    /**
     * Make DHCP config files for all CMs including EPs - used in dhcpCommand after deleting
     * the config files with all entries
     *
     * @author Torsten Schmidt
     */
    public static function make_dhcp_cm_all()
    {
        Log::info('dhcp: update '.self::CONF_FILE_PATH.', '.self::CONF_FILE_PATH_PUB);

        $data = '';
        $data_pub = '';
        $server = ProvBase::first()->provisioning_server;

        self::clear_dhcp_conf_files();

        foreach (self::all() as $modem) {
            if ($modem->id == 0) {
                continue;
            }

            // all
            $data .= $modem->generate_cm_dhcp_entry($server);

            // public ip
            if ($modem->public) {
                $data_pub .= $modem->generate_cm_dhcp_entry_pub();
            }
        }

        $ret = File::put(self::CONF_FILE_PATH, $data);
        if ($ret === false) {
            die('Error writing to file');
        }

        $ret = File::append(self::CONF_FILE_PATH_PUB, $data_pub);
        if ($ret === false) {
            die('Error writing to file');
        }

        // chown for future writes in case this function was called from CLI via php artisan nms:dhcp that changes owner to 'root'
        system('/bin/chown -R apache /etc/dhcp-nmsprime/');
    }

    /**
     * Creates file to ignore booting of CPE/MTA from unknown CM for multi NMS environments.
     * Creates a blank file if NMSPrime is the only provisioning system.
     *
     * @author Patrick Reichel
     */
    public static function create_ignore_cpe_dhcp_file()
    {

        // only add content if multiple dhcp servers exist
        if (! ProvBase::first()->multiple_provisioning_systems) {
            $content = "# Ignoring no devices – multiple_provisioning_systems not set in ProvBase\n";
        } else {
            // get all not deleted modems
            // attention: do not use “where('internet_access', '>', '0')” to shrink the list
            //   ⇒ MTAs shall get IPs even if internet_access is disabled!
            $modems_raw = \DB::select('SELECT hostname, mac FROM modem WHERE deleted_at IS NULL');
            $modems = [];
            foreach ($modems_raw as $modem) {
                $modems[\Str::lower($modem->mac)] = $modem->hostname;
            }
            ksort($modems);

            // get all configfiles with NetworkAccess enabled
            exec('grep "^[[:blank:]]*NetworkAccess[[:blank:]]*1" /tftpboot/cm/*.conf', $enabled_configs, $ret);
            if ($ret > 0) {
                \Log::error('Error getting config files with NetworkAccess enabled in '.__METHOD__);
            }

            $hostnames = [];
            foreach ($enabled_configs as $config) {
                $_ = explode('.conf', $config)[0];
                $_ = explode('/', $_);
                array_push($hostnames, array_pop($_));
            }

            $remote_id_lines = [];
            foreach ($modems as $mac => $hostname) {
                if (in_array($hostname, $hostnames)) {
                    $line = "\t\t(option agent.remote-id != ".\Str::lower($mac).')';
                    array_push($remote_id_lines, $line);
                }
            }

            $remote_id_block = implode(" and\n", $remote_id_lines);
            $lines = [
                '# ignore all non-modems not attached to modems provsioned by NMSPrime',
                'class "ignore_cpe" {',
                "\tmatch  if",
                "\t\t(substring(option vendor-class-identifier,0,6) != \"docsis\") and",
                $remote_id_block,
                "\t;",
                '',
                "\t# log ignored devices",
                "\tlog(info, concat(",
                "\t\t\"IGNORING device \", binary-to-ascii(16, 8, \":\", substring(hardware,1,6)),",
                "\t\t\" at unknown modem \", binary-to-ascii(16, 8, \":\", option agent.remote-id))",
                "\t);",
                '',
                "\tignore booting;",
                '}',
            ];
            $content = implode("\n", $lines);
        }

        // lock
        $fp = fopen(self::CONF_FILE_PATH, 'r+');

        if (! flock($fp, LOCK_EX)) {
            Log::error('Could not get exclusive lock for '.self::CONF_FILE_PATH);
        }

        self::_write_dhcp_file(self::IGNORE_CPE_FILE_PATH, $content);

        // unlock
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * Add DHCP config for a single CM including EPs to the appropriate DHCPD Config File
     * Used in ModemObserver@updated/deleted for created/updated/deleted events
     *
     * NOTES:
     * This is way faster (0,01s (also on 2k modems) vs 2,8s for 348 Modems via make_dhcp_cm_all) than everytime creating files for all modems
     * It's also more secure as it uses flock() to avoid dhcpd restart errors due to race conditions
     * MaybeTODO: embed part between lock & unlock into try catch block to avoid forever locked files in case of exception !?
     * Attention!: MAC Address must be unique in database to work correctly !!!
     *
     * @param 	delete  	set to true if you want to remove the entry from the configfile
     *
     * @author Nino Ryschawy
     */
    public function make_dhcp_cm($delete = false, $mta_added = false)
    {
        Log::debug(__METHOD__.' started');

        // Note: hostname is changed when modem was created
        if (! $this->isDirty(['hostname', 'mac', 'public']) && ! $delete && ! $mta_added) {
            return;
        }

        // Log
        Log::info('DHCPD Configfile Update for Modem: '.$this->id);

        $data = $this->generate_cm_dhcp_entry();
        $original = $this->getOriginal();
        $replace = $original ? $original['mac'] : $this->mac;

        if (! file_exists(self::CONF_FILE_PATH)) {
            // try to add file if it doesnt exist
            Log::info('Missing DHCPD Configfile '.self::CONF_FILE_PATH);
            if (File::put(self::CONF_FILE_PATH, '') === false) {
                Log::alert('Error writing to DHCPD Configfile: '.self::CONF_FILE_PATH);

                return;
            }
        }

        // lock
        $fp = fopen(self::CONF_FILE_PATH, 'r+');

        if (! flock($fp, LOCK_EX)) {
            Log::error('Could not get exclusive lock for '.self::CONF_FILE_PATH);
        }

        $conf = file(self::CONF_FILE_PATH);

        // TODO: check for hostname to avoid deleting the wrong entry when mac exists multiple times in DB !?
        foreach ($conf as $key => $line) {
            if (strpos($line, "$this->hostname {") !== false) {
                unset($conf[$key]);
                break;
            }
        }

        if (! $delete) {
            $conf[] = $data;
        }

        self::_write_dhcp_file(self::CONF_FILE_PATH, implode($conf)); //	PHP_EOL

        // public ip
        if ($this->public || ($original && $original['public'])) {
            $data_pub = $this->generate_cm_dhcp_entry_pub();

            if (file_exists(self::CONF_FILE_PATH_PUB)) {
                $conf_pub = file(self::CONF_FILE_PATH_PUB);
            } else {
                Log::info('Missing DHCPD Configfile '.self::CONF_FILE_PATH_PUB);
                if (File::put(self::CONF_FILE_PATH_PUB, '') === false) {
                    Log::alert('Error writing to DHCPD Configfile: '.self::CONF_FILE_PATH_PUB);
                }
            }

            foreach ($conf_pub as $key => $line) {
                if (strpos($line, $replace) !== false) {
                    unset($conf_pub[$key]);
                    break;
                }
            }

            // $conf_pub = str_replace($replace_pub, '', $conf_pub);
            if (! $delete && $this->public) {
                $conf_pub[] = $data_pub;
            }

            self::_write_dhcp_file(self::CONF_FILE_PATH_PUB, implode($conf_pub));
        }

        // unlock
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    public static function _write_dhcp_file($filename, $data)
    {
        if (File::put($filename, $data) === false) {
            Log::critcal('Failed to modify DHCPD Configfile '.$filename);
        }
    }

    /**
     * Make Configfile for a single CM
     */
    public function make_configfile()
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        /* Configfile */
        $dir = '/tftpboot/cm/';
        $cf_file = $dir."cm-$this->id.conf";
        $cfg_file = $dir."cm-$this->id.cfg";

        if (! $this->configfile) {
            return false;
        }

        // Evaluate network access (NA) and MaxCPE count
        // Note: NA becomes only zero when internet is disabled on contract (no valid tariff) or modem (manually) and contract has no telephony
        $cpe_cnt = \Modules\ProvBase\Entities\ProvBase::first()->max_cpe;
        $max_cpe = $cpe_cnt ?: 2; 		// default 2
        $internet_access = 1;

        if (\Module::collections()->has('ProvVoip') && (count($this->mtas))) {
            if ($this->internet_access || $this->contract->has_telephony || $this->contract->internet_access) {
                if (! $this->internet_access) {
                    $max_cpe = 0;

                    if (! $this->contract->has_telephony) {
                        $internet_access = 0;
                    }
                }

                $max_cpe += $this->mtas->count();
            } else {
                $internet_access = $max_cpe = 0;
            }
        } elseif (! $this->internet_access) {
            $internet_access = 0;
        }

        // MaxCPE MUST be between 1 and 254 according to the standard
        if ($max_cpe < 1) {
            $max_cpe = 1;
            $internet_access = 0;
        }
        if ($max_cpe > 254) {
            $max_cpe = 254;
        }

        // make text and write to file
        $conf = "\tNetworkAccess $internet_access;\n";

        // don't use auto generated MaxCPE if it is explicitly set in the configfile
        // see https://stackoverflow.com/a/643136 for stripping multiline comments
        if (! \Str::contains(preg_replace('!/\*.*?\*/!s', '', $this->configfile->text), 'MaxCPE')) {
            $conf .= "\tMaxCPE $max_cpe;\n";
        }

        if (\Module::collections()->has('ProvVoip') && $internet_access) {
            foreach ($this->mtas as $mta) {
                $conf .= "\tCpeMacAddress $mta->mac;\n";
            }
        }

        $text = "Main\n{\n".$conf.$this->configfile->text_make($this, 'modem')."\n}";

        if (File::put($cf_file, $text) === false) {
            die('Error writing to file');
        }

        Log::debug("configfile: docsis -e $cf_file $dir../keyfile $cfg_file");

        // "&" to start docsis process in background improves performance but we can't reliably proof if file exists anymore
        // docsis tool always returns 0
        exec("docsis -e $cf_file $dir../keyfile $cfg_file >/dev/null 2>&1 &", $out);

        // change owner in case command was called from command line via php artisan nms:configfile that changes owner to root
        system('/bin/chown -R apache /tftpboot/cm');

        Log::info('Configfile updated for Modem: '.$this->hostname);

        return true;
    }

    /**
     * Make all Configfiles
     */
    public function make_configfile_all()
    {
        $m = self::all();
        foreach ($m as $modem) {
            if ($modem->id == 0) {
                continue;
            }
            if (! $modem->make_configfile()) {
                Log::warning('failed to build/write configfile for modem cm-'.$modem->id);
            }
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

        foreach ($file as $f) {
            if (file_exists($f)) {
                unlink($f);
            }
        }
    }

    /**
     * Get CMTS a CM is registered on
     *
     * @param  string 	ip 		address of cm
     * @return object 	CMTS
     *
     * @author Nino Ryschawy
     */
    public static function get_cmts($ip)
    {
        $validator = new \Acme\Validators\ExtendedValidator;

        $ippools = IpPool::where('type', '=', 'CM')->get();

        foreach ($ippools as $pool) {
            if ($validator->validateIpInRange(0, $ip, [$pool->net, $pool->netmask])) {
                $cmts_id = $pool->cmts_id;
                break;
            }
        }

        if (isset($cmts_id)) {
            return Cmts::find($cmts_id);
        }
    }

    /**
     * Get all used firmwares of specified modem(s)
     *
     * @param string $id Modem identifier (all modems if $id is null)
     * @return array Hierarchical object (vendor->model->sw_rev) of all used firmwares
     *
     * @author Ole Ernst
     */
    public static function get_firmware_tree($id = null)
    {
        $ret = [];
        foreach (glob("/var/lib/cacti/rra/cm-$id*.json") as $file) {
            if (filemtime($file) < time() - 86400 || // ignore json files, which haven't been updated within a day
                ! ($json = file_get_contents($file)) ||
                ! ($json = json_decode($json)) ||
                ! isset($json->descr)) {
                continue;
            }

            preg_match_all('/VENDOR: ([^;]*);.*SW_REV: (.*); MODEL: (.*)>>/', $json->descr, $match);
            $vendor = array_pop($match[1]) ?: 'n/a';
            $sw_rev = array_pop($match[2]) ?: 'n/a';
            $model = array_pop($match[3]) ?: 'n/a';

            if ($id) {
                return [$vendor, $model, $sw_rev];
            }

            if (! isset($ret[$vendor][$model][$sw_rev])) {
                $ret[$vendor][$model][$sw_rev] = 0;
            }

            $ret[$vendor][$model][$sw_rev] += 1;
        }

        return $ret;
    }

    /**
     * Update firmware and model strings of all modems
     *
     * @author Ole Ernst
     */
    public static function update_model_firmware()
    {
        foreach (\DB::table('modem')->whereNull('deleted_at')->pluck('id') as $id) {
            $tmp = self::get_firmware_tree($id);
            if (! $tmp) {
                continue;
            }

            \DB::statement("UPDATE modem SET model = '$tmp[0] $tmp[1]', sw_rev = '$tmp[2]' where id='$id'");
        }
    }

    /**
     * Restarts modem through snmpset
     */
    public function restart_modem($mac_changed = false, $modem_reset = false)
    {
        // Log
        Log::info('restart modem '.$this->hostname);

        // if hostname cant be resolved we dont want to have an php error
        try {
            $config = ProvBase::first();
            $fqdn = $this->hostname.'.'.$config->domain_name;
            $cmts = self::get_cmts(gethostbyname($fqdn));
            $mac = $mac_changed ? $this->getOriginal('mac') : $this->mac;
            $mac_oid = implode('.', array_map('hexdec', explode(':', $mac)));

            if ($modem_reset) {
                throw new Exception('Reset Modem directly');
            }

            if ($cmts && $cmts->company == 'Cisco') {
                // delete modem entry in cmts - CISCO-DOCS-EXT-MIB::cdxCmCpeDeleteNow
                snmpset($cmts->ip, $cmts->get_rw_community(), '1.3.6.1.4.1.9.9.116.1.3.1.1.9.'.$mac_oid, 'i', '1', 300000, 1);
            } elseif ($cmts && $cmts->company == 'Casa') {
                // reset modem via cmts, deleting is not possible - CASA-CABLE-CMCPE-MIB::casaCmtsCmCpeResetNow
                snmpset($cmts->ip, $cmts->get_rw_community(), '1.3.6.1.4.1.20858.10.12.1.3.1.7.'.$mac_oid, 'i', '1', 300000, 1);
            } else {
                throw new Exception('CMTS company not set');
            }
            // success message
            \Session::push('tmp_info_above_form', trans('messages.modem_restart_success_cmts'));
        } catch (Exception $e) {
            \Log::error("Could not delete $this->hostname from CMTS ('".$e->getMessage()."'). Let's try to restart it directly.");

            try {
                // restart modem - DOCS-CABLE-DEV-MIB::docsDevResetNow
                snmpset($fqdn, $config->rw_community, '1.3.6.1.2.1.69.1.1.3.0', 'i', '1', 300000, 1);

                // success message - make it a warning as sth is wrong when it's not already restarted by CMTS??
                \Session::push('tmp_info_above_form', trans('messages.modem_restart_success_direct'));
            } catch (Exception $e) {
                \Log::error("Could not restart $this->hostname directly ('".$e->getMessage()."')");

                if (((strpos($e->getMessage(), 'php_network_getaddresses: getaddrinfo failed: Name or service not known') !== false) ||
                    (strpos($e->getMessage(), 'snmpset(): No response from') !== false)) ||
                    // this is not necessarily an error, e.g. the modem was deleted (i.e. Cisco) and user clicked on restart again
                    (strpos($e->getMessage(), 'noSuchName') !== false)) {
                    \Session::push('tmp_warning_above_form', trans('messages.modem_restart_error'));
                } else {
                    // Inform and log for all other exceptions
                    \Session::push('tmp_error_above_form', \App\Http\Controllers\BaseViewController::translate_label('Unexpected exception').': '.$e->getMessage());
                }
            }
        }
    }

    /**
     * Get eventlog of a modem via snmp
     *
     * @return: Array of rows of the eventlog table
     * @author: Ole Ernst
     */
    public function get_eventlog()
    {
        $conf = ProvBase::first();
        $fqdn = $this->hostname.'.'.$conf->domain_name;
        $com = $conf->ro_community;

        snmp_set_quick_print(true);
        snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
        snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);

        try {
            $log = snmp2_real_walk($fqdn, $com, '.1.3.6.1.2.1.69.1.5.8.1');
        } catch (Exception $e) {
            try {
                $log = snmprealwalk($fqdn, $com, '.1.3.6.1.2.1.69.1.5.8.1');
            } catch (Exception $e) {
                return;
            }
        }
        $log = ArrayHelper::snmpwalk_fold($log);

        // filter unnecessary entries
        $log = array_filter($log, function ($k) {
            $tmp = explode('.', $k);
            $tmp = array_pop($tmp);
            if ($tmp > 2 && $tmp < 8 && $tmp != 6) {
                return true;
            }
        }, ARRAY_FILTER_USE_KEY);

        // show time column in a human-readable format
        $time_key = array_keys($log)[0];
        foreach ($log[$time_key] as $k => $time) {
            $time = explode(' ', trim($time, '" '));
            $time[0] .= $time[1];
            unset($time[1]);
            $time = array_map('hexdec', $time);
            $time = sprintf('%02d.%02d.%04d %02d:%02d:%02d.%d', $time[3], $time[2], $time[0], $time[4], $time[5], $time[6], $time[7]);
            $log[$time_key][$k] = $time;
        }

        // translate severity level of log entry to datatable colors
        $color_key = array_keys($log)[2];
        foreach ($log[$color_key] as $k => $color_idx) {
            $trans = ['', 'danger', 'danger', 'danger', 'danger', 'warning', 'success', '', 'info'];
            $log[$color_key][$k] = $trans[$color_idx];
        }

        // add table headers
        $ret[] = ['Time', '#', 'Text'];

        // reshape array into the right format
        foreach (array_reverse(array_keys(reset($log))) as $idx) {
            foreach ($log as $k => $v) {
                $ret[$idx][] = $v[$idx];
            }
        }

        return $ret;
    }

    /*
     * Refresh Modem State
     *
     * This function will update the modem upstream/downstream power level/SNR
     * if online, otherwise it will set the value to 0.
     *
     * NOTE: This function will be called via artisan command modem-refresh. This command
     *       is added to laravel scheduling api to refresh all modem states every 5min.
     *
     * NOTE: The function is written in a generic manner to fetch more than just upstream power level
     *       For more see array $oids inside ..
     *
     * @param timeout: snmp timeout
     * @return: result of snmpget as array of [oid1 => value1, ..]
     * @author: Torsten Schmidt
     */
    public function refresh_state($timeout = 100 * 1000)
    {
        \Log::debug('Refresh Modem State', [$this->hostname]);

        // Load Global Config
        $config = ProvBase::first();
        $community_ro = $config->ro_community;
        $domain = $config->domain_name;

        // Set SNMP default mode
        // TODO: use a seperate funciton
        snmp_set_quick_print(true);
        snmp_set_oid_numeric_print(true);
        snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
        snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);

        // OID Array to parse
        // Style: ['modem table field 1' => 'oid1', 'modem table field 2' => 'oid2, ..']
        $oids = ['us_pwr' => '.1.3.6.1.2.1.10.127.1.2.2.1.3.2',
                 /*'us_snr' => 'not possible using modem oids',*/
                 'ds_pwr' => '.1.3.6.1.2.1.10.127.1.1.1.1.6',
                 'ds_snr' => '.1.3.6.1.2.1.10.127.1.1.4.1.5',
                 ];

        $this->observer_disable();

        // if hostname cant be resolved we dont want to have an php error
        try {
            // SNMP request
            $session = new \SNMP(\SNMP::VERSION_2c, $this->hostname.'.'.$domain, $community_ro, $timeout);
            $results = $r = $session->get($oids);

            // parse and update results
            foreach (array_reverse($oids) as $field => $oid) {
                $this->{$field} = array_pop($r) / 10;
            }	// TODO: added generic concept for multiplying options @Torsten Schmidt

            // save
            $this->save();
        } catch (Exception $e) {
            // catch error
            // set fields to 0
            foreach (array_reverse($oids) as $field => $oid) {
                $this->{$field} = 0;
            }

            // save
            $this->save();

            return false;
        }

        return $results;
    }

    /**
     * Refresh Modem State using cached value of Cacti
     *
     * This function will update the upstream/downstream power level/SNR
     * if online. Because the last value of Cacti is used, the update is much quicker and doesn't
     * generate a superfluous SNMP request.
     *
     * NOTE: This function will be called via artisan command modem-refresh. This command
     *       is added to laravel scheduling api to refresh all modem states every 5min.
     *
     * @return: maximum power level of all upstream channels or -1 on error
     * @author: Ole Ernst
     */
    public function refresh_state_cacti()
    {
        // cacti is not installed
        if (! \Module::collections()->has('ProvMon')) {
            return -1;
        }

        try {
            $path = \DB::connection('mysql-cacti')->table('host')
                ->join('data_local', 'host.id', '=', 'data_local.host_id')
                ->join('data_template_data', 'data_local.id', '=', 'data_template_data.local_data_id')
                ->where('host.description', '=', $this->hostname)
                ->orderBy('data_local.id')
                ->select('data_template_data.data_source_path')->first();
        } catch (\PDOException $e) {
            // Code 1049 == Unknown database '%s' -> cacti is not installed yet
            if ($e->getCode() == 1049) {
                return -1;
            }
            // don't catch other PDOExceptions
            throw $e;
        }

        // no rrd file for current modem found in DB
        if (! $path) {
            return -1;
        }

        $file = str_replace('<path_rra>', '/usr/share/cacti/rra', $path->data_source_path);
        // file does not exist
        if (! File::exists($file)) {
            return -1;
        }

        $output = [];
        exec("rrdtool lastupdate $file", $output);
        // unexpected number of lines from rrdtool
        if (count($output) != 3) {
            return -1;
        }

        $keys = explode(' ', trim(array_shift($output)));
        $vals = explode(' ', trim(explode(':', array_pop($output))[1]));
        $arr = array_combine($keys, $vals);

        $res = ['us_pwr' => $arr['avgUsPow'],
                'us_snr' => $arr['avgUsSNR'],
                'ds_pwr' => $arr['avgDsPow'],
                'ds_snr' => $arr['avgDsSNR'],
                ];

        $status = \DB::connection('mysql-cacti')->table('host')
            ->where('description', '=', $this->hostname)
            ->select('status')->first()->status;
        // modem is offline, if we use last value of cacti instead of setting it
        // to zero, it would seem as if the modem is still online
        if ($status == 1) {
            array_walk($res, function (&$val) {
                $val = 0;
            });
        }

        $this->observer_disable();
        foreach ($res as $key => $val) {
            $this->{$key} = round($val);
        }
        $this->save();

        return $res;
    }

    /*
     * Return actual modem state as string or int
     *
     * @param return_type: ['string' (default), 'int']
     * @return: string [ok, warning, critical, offline] or int [0 -> ok, 1 -> warning, 2 -> critical, 3 -> offline]
     * @author: Torsten Schmidt
     */
    public function get_state($return_type = 'string')
    {
        if ($this->us_pwr == 0) {
            if ($return_type == 'string') {
                return 'offline';
            } else {
                return 3;
            }
        }

        if ($this->us_pwr > \Modules\HfcCustomer\Entities\ModemHelper::$single_critical_us) {
            if ($return_type == 'string') {
                return 'critical';
            } else {
                return 2;
            }
        }

        if ($this->us_pwr > \Modules\HfcCustomer\Entities\ModemHelper::$single_warning_us) {
            if ($return_type == 'string') {
                return 'warning';
            } else {
                return 1;
            }
        }

        if ($return_type == 'string') {
            return 'ok';
        } else {
            return 0;
        }
    }

    /*
     * Geocoding API
     * Translate a address (like: Deutschland, Marienberg, Waldrand 4) in a geoposition (x,y)
     *
     * @author: Torsten Schmidt
     *
     * TODO: move to a seperate extensions class
     */

    // private variable to hold the last Geocoding response state
    // use geocode_last_status()
    private $geocode_state = null;

    /**
     * Modem Geocoding Function
     * Geocode the modem address value in a geoposition and update values to x,y. Please
     * note that the function is working in object context, so no addr parameters are required.
     *
     * @param save: Update Modem x,y value with a save() to DB. Notice this calls Observer !
     * @return: true on success, false if coding fails. For error log see geocode_last_status()
     * @author: Torsten Schmidt
     *
     * TODO: split in a general geocoding function and a modem specific one
     */
    public function geocode($save = true)
    {
        $geodata = null;

        // first try to get geocoding from OSM
        try {
            $geodata = $this->_geocode_osm_nominatim();
        } catch (Exception $ex) {
            $msg = 'Error in geocoding against OSM Nominatim: '.$ex->getMessage();
            \Session::push('tmp_error_above_form', $msg);
            Log::error("$msg (".get_class($ex).' in '.$ex->getFile().' line '.$ex->getLine().')');
        }

        // fallback: ask google maps
        if (! $geodata) {
            try {
                $geodata = $this->_geocode_google_maps($save);
            } catch (Exception $ex) {
                $msg = 'Error in geocoding against google maps: '.$ex->getMessage();
                \Session::push('tmp_error_above_form', $msg);
                Log::error("$msg (".get_class($ex).' in '.$ex->getFile().' line '.$ex->getLine().')');
            }
        }

        if ($geodata) {
            $this->y = $geodata['latitude'];
            $this->x = $geodata['longitude'];
            $this->geocode_source = $geodata['source'];
            $this->geocode_state = 'OK';

            Log::info('Geocoding successful, result: '.$this->y.','.$this->x.' (source: '.$geodata['source'].')');
        } else {
            // no geodata determined
            if (! \App::runningInConsole()) {
                // if running interactively: delete probably outdated geodata and inform user
                $this->y = '';
                $this->x = '';
                $this->geocode_source = 'n/a';
                $message = "Could not determine geo coordinates ($this->geocode_state) – please add manually";
                \Session::push('tmp_error_above_form', $message);
            } else {
                // if running from console: preserve existing geodata (could have been be imported or manually set in older times)
                $this->geocode_source = 'n/a (unchanged existing data)';
            }
            Log::warning('geocoding failed');
        }

        if ($save) {
            $this->save();
        }

        return $geodata;
    }

    /**
     * Some housenumbers need special handling. This method splits them to the needed parts.
     *
     * @author Patrick Reichel
     */
    protected function _split_housenumber_for_geocoding($house_number)
    {
        // regex from https://stackoverflow.com/questions/10180730/splitting-string-containing-letters-and-numbers-not-separated-by-any-particular
        return preg_split("/(,?\s+)|((?<=[-\/a-z])(?=\d))|((?<=\d)(?=[-\/a-z]))/i", strtolower($this->house_number));
    }

    /**
     * Get geodata from OpenStreetMap
     *
     * @author Patrick Reichel
     */
    protected function _geocode_osm_nominatim()
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        // don't ask API in testing mode (=faked data)
        if (env('APP_ENV') == 'testing') {
            Log::debug('Testing mode – will not ask OSM Nominatim with faked data');

            return;
        }

        $geodata = null;
        $base_url = 'https://nominatim.openstreetmap.org/search';

        if (! filter_var(env('OSM_NOMINATIM_EMAIL'), FILTER_VALIDATE_EMAIL)) {
            $message = 'Unable to ask OpenStreetMap Nominatim API for geocoding – OSM_NOMINATIM_EMAIL not set';
            \Session::push('tmp_warning_above_form', $message);
            Log::warning($message);

            return false;
        }

        $country_code = $this->country_code ?: GlobalConfig::first()->default_country_code;

        // problem: data is inconsistent in OSM – housenumbers with additional character can have two formats:
        // “104 a” or “104a”; there is an 3-years-open bug report: https://trac.openstreetmap.org/ticket/5256
        // so we have to try both variants if the first one does not return a result
        $parts = $this->_split_housenumber_for_geocoding($this->house_number);

        if (count($parts) < 2) {
            $housenumber_variants = [$parts[0]];
        } else {
            $housenumber_variants = [
                implode('', $parts),	// more often used according to bug report
                implode(' ', $parts),
            ];
        }

        foreach ($housenumber_variants as $housenumber_prepared) {
            // see https://wiki.openstreetmap.org/wiki/DE:Nominatim#Parameter for details
            // we are using the structured format (faster, saves server ressources – but marked experimental)
            $params = [
                'street' => "$housenumber_prepared $this->street",
                'postalcode' => $this->zip,
                'city' => $this->city,
                'country' => $country_code,
                'email' => env('OSM_NOMINATIM_EMAIL'),	// has to be set (https://operations.osmfoundation.org/policies/nominatim); else 403 Forbidden
                'format' => 'json',			// return format
                'dedupe' => '1',			// only one geolocation (even if address is split to multiple places)?
                'polygon' => '0',			// include surrounding polygons?
                'addressdetails' => '0',	// not available using API
                'limit' => '1',				// only request one result
            ];

            $url = $base_url.'?';
            if ($params) {
                $tmp_params = [];
                foreach ($params as $key => $value) {
                    array_push($tmp_params, (urlencode($key).'='.urlencode($value)));
                }
                $url .= implode('&', $tmp_params);
            }

            Log::info('Trying to geocode modem '.$this->id." against $url");

            $geojson = file_get_contents($url);
            $geodata_raw = json_decode($geojson, true);

            $matches = ['building', 'house', 'amenity', 'shop', 'tourism'];
            foreach ($geodata_raw as $entry) {
                $class = array_get($entry, 'class', '');
                $type = array_get($entry, 'type', '');
                $display_name = array_get($entry, 'display_name', '');
                $lat = array_get($entry, 'lat', null);
                $lon = array_get($entry, 'lon', null);

                // check if returned entry is of certain type (e.g. “highway” indicates fuzzy match)
                if ((in_array($class, $matches) || in_array($type, $matches)) && $lat && $lon) {

                    // as both variants can appear in resulting address: check for all of them
                    foreach ($housenumber_variants as $variant) {
                        if (\Str::contains(strtolower($display_name), $variant)) {	// don't check for startswith; sometimes a company name is added before the house number
                            $geodata = [
                                'latitude' => $lat,
                                'longitude' => $lon,
                                'source' => 'OSM Nominatim',
                            ];
                            break;
                        }
                    }
                }
            }

            // if this try results in a match: exit the loop
            if ($geodata) {
                break;
            }

            // sleep to respect usage policy
            if (count($housenumber_variants > 1)) {
                sleep(1);
            }
        }

        if (! $geodata) {
            Log::warning('OSM Nominatim geocoding for modem '.$this->id.' failed');

            return false;
        }

        return $geodata;
    }

    /**
     * Get geodata from google maps
     *
     * @author Torsten Schmidt, Patrick Reichel
     * @return array 	Geodata [lat, lon, source]
     */
    protected function _geocode_google_maps()
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        // don't ask API in testing mode (=faked data)
        if (env('APP_ENV') == 'testing') {
            Log::debug('Testing mode – will not ask Google Geocoding API with faked data');

            return;
        }

        $geodata = null;

        $country_code = $this->country_code ?: GlobalConfig::first()->default_country_code;

        // beginning on 2018-06-11 geocode api can only be used with an api key (otherwise returning error)
        // ⇒ https://cloud.google.com/maps-platform/user-guide
        if (date('c') > '2018-06-10') {
            if (! env('GOOGLE_API_KEY')) {
                $message = 'Unable to ask Google Geocoding API – GOOGLE_API_KEY not set';
                \Session::push('tmp_warning_above_form', $message);
                Log::warning($message);

                return false;
            }
            $key = '&key='.$_ENV['GOOGLE_API_KEY'];
        } else {
            // Load google key if .ENV is set
            $key = '';
            if (env('GOOGLE_API_KEY')) {
                $key = '&key='.$_ENV['GOOGLE_API_KEY'];
            }
        }

        // url encode the address
        $address = urlencode($this->street.' '.$this->house_number.', '.$this->zip.', '.$country_code);

        // google map geocode api url
        $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address={$address}$key";

        Log::info('Trying to geocode modem '.$this->id." against $url");

        // get the json response
        $resp_json = file_get_contents($url);
        $resp = json_decode($resp_json, true);

        $status = array_get($resp, 'status', 'n/a');

        // response status will be 'OK', if able to geocode given address
        if ($status == 'OK') {

            // get the important data
            $lati = array_get($resp, 'results.0.geometry.location.lat', null);
            $longi = array_get($resp, 'results.0.geometry.location.lng', null);
            $formatted_address = array_get($resp, 'results.0.formatted_address', null);
            $location_type = array_get($resp, 'results.0.geometry.location_type', null);
            $partial_match = array_get($resp, 'results.0.partial_match', null);

            $matches = ['ROOFTOP'];
            $interpolated_matches = ['ROOFTOP', 'RANGE_INTERPOLATED'];
            // verify if data is complete and a real match
            if (
                $lati &&
                $longi &&
                $formatted_address &&
                ! $partial_match &&
                in_array($location_type, $matches)
            ) {
                $geodata = [
                    'latitude' => $lati,
                    'longitude' => $longi,
                    'source' => 'Google Geocoding API',
                ];

                return $geodata;
            }
            // check if partial match (interpolated geocoords seem to be pretty good!)
            // mark source as tainted to give the user a hint
            elseif (
                $lati &&
                $longi &&
                $formatted_address &&
                $partial_match &&
                in_array($location_type, $interpolated_matches)
            ) {
                $geodata = [
                    'latitude' => $lati,
                    'longitude' => $longi,
                    'source' => 'Google Geocoding API (interpolated)',
                ];

                return $geodata;
            } else {
                $this->geocode_state = 'DATA_VERIFICATION_FAILED';
                Log::warning('Google geocoding for modem '.$this->id.' failed: '.$this->geocode_state);

                return;
            }
        } else {
            $this->geocode_state = $status;
            Log::warning('Google geocoding for modem '.$this->id.' failed: '.$this->geocode_state);

            return;
        }
    }

    /**
     * Check if modem has phonenumbers attached
     *
     * @author Patrick Reichel
     *
     * @return true if phonenumbers attached to one of the modem's MTA, else False
     */
    public function has_phonenumbers_attached()
    {

        // if there is no voip module ⇒ there can be no numbers
        if (! \Module::collections()->has('ProvVoip')) {
            return false;
        }

        foreach ($this->mtas as $mta) {
            foreach ($mta->phonenumbers->all() as $phonenumber) {
                return true;
            }
        }

        // no numbers found
        return false;
    }

    /**
     * Helper to get all phonenumbers related to contract.
     *
     * @author Patrick Reichel
     */
    public function related_phonenumbers()
    {

        // if voip module is not active: there can be no phonenumbers
        if (! \Module::collections()->has('ProvVoip')) {
            return [];
        }

        $phonenumbers_on_modem = [];

        // else: search all mtas on all modems
        foreach ($this->mtas as $mta) {
            foreach ($mta->phonenumbers as $phonenumber) {
                array_push($phonenumbers_on_modem, $phonenumber);
            }
        }

        return $phonenumbers_on_modem;
    }

    /*
     * Return Last Geocoding State / ERROR
     */
    public function geocode_last_status()
    {
        return $this->geocode_state;
    }

    /*
     * Observer Handling
     *
     * To disable the update observers run observer_disable() on object context.
     * This is useful to avoid running per modem observers on general (unimportant)
     * changes. If obersers are enabled, every change on modem object will for example
     * restart the modem.
     */
    public function observer_disable()
    {
        $this->observer_enabled = false;
    }

    /**
     * Calculates the great-circle distance between this and $modem, with
     * the Haversine formula.
     *
     * see https://stackoverflow.com/questions/514673/how-do-i-open-a-file-from-line-x-to-line-y-in-php#tab-top
     *
     * @return float Distance between points in [m] (same as earthRadius)
     */
    private function _haversine_great_circle_distance($modem)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($this->y);
        $lonFrom = deg2rad($this->x);
        $latTo = deg2rad($modem->y);
        $lonTo = deg2rad($modem->x);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        // earth radius
        return $angle * 6371000;
    }

    /**
     * Clean modem from all envia TEL related data – call this e.g. if you delete the last number from this modem.
     * We have to do this to avoid problems in case we want to install this modem at another customer
     *
     * @author Patrick Reichel
     */
    public function remove_envia_related_data()
    {

        // first: check if envia module is enabled
        // if not: do nothing – this database fields could be in use by another voip provider module!
        if (! \Module::collections()->has('ProvVoipEnvia')) {
            return;
        }

        $this->contract_external_id = null;
        $this->contract_ext_creation_date = null;
        $this->contract_ext_termination_date = null;
        $this->installation_address_change_date = null;
        $this->save();
    }

    public function proximity_search($radius)
    {
        $ids = [0];
        foreach (\DB::table('modem')->select('id', 'x', 'y')->where('deleted_at', null)->get() as $modem) {
            if ($this->_haversine_great_circle_distance($modem) < $radius) {
                array_push($ids, $modem->id);
            }
        }

        return $ids;
    }

    /**
     * Check if modem actually needs to be restarted. This is only the case if a
     * relevant attribute was modified.
     *
     * @return 1 if reset via Modem or original mac is needed (mac was changed)
     *		  -1 for reset via CMTS (faster),
     *		   0 if no restart is needed
     *
     * @author Ole Ernst, Nino Ryschawy
     *
     * NOTE: returns 1 when modem is created
     */
    public function needs_restart()
    {
        $diff = $this->getDirty();

        // in case mac was changed, reset via cmts - or take original mac
        if (array_key_exists('mac', $diff)) {
            return 1;
        }

        if (multi_array_key_exists(['contract_id', 'public', 'internet_access', 'configfile_id', 'qos_id'], $diff)) {
            return -1;
        }

        return 0;
    }
}

/**
 * Modem Observer Class
 * Handles changes on CMs, can handle:
 *
 * 'creating', 'created', 'updating', 'updated',
 * 'deleting', 'deleted', 'saving', 'saved',
 * 'restoring', 'restored',
 */
class ModemObserver
{
    public function created($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        $modem->hostname = 'cm-'.$modem->id;
        $modem->save();	 // forces to call the updating() and updated() method of the observer !
        Modem::create_ignore_cpe_dhcp_file();

        if (\Module::collections()->has('ProvMon')) {
            Log::info("Create cacti diagrams for modem: $modem->hostname");
            \Artisan::call('nms:cacti', ['--cmts-id' => 0, '--modem-id' => $modem->id]);
        }
    }

    public function updating($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        // reminder: on active envia TEL module: moving modem to other contract is not allowed!
        // check if this is running if you decide to implement moving of modems to other contracts
        // watch Ticket LAR-106
        if (\Module::collections()->has('ProvVoipEnvia')) {
            if (
                // updating is also called on create – so we have to check this
                (! $modem->wasRecentlyCreated)
                &&
                ($modem['original']['contract_id'] != $modem->contract_id)
            ) {
                // returning false should cancel the updating: verify this! There has been some problems with deleting modems – we had to put the logic in Modem::delete() probably caused by our Base* classes…
                // see: http://laravel-tricks.com/tricks/cancelling-a-model-save-update-delete-through-events
                return false;
            }
        }

        if (! $modem->observer_enabled) {
            return;
        }

        // get changed values
        $diff = $modem->getDirty();

        // if testing: do not try to geocode or position modems (faked data; slows down the process)
        if (\App::runningUnitTests()) {
            return;
        }

        // Use Updating to set the geopos before a save() is called.
        // Notice: that we can not call save() in update(). This will re-trigger
        //         the Observer and re-call update() -> endless loop is the result.
        if (
            ($modem->wasRecentlyCreated)    // new modem
            ||
            (multi_array_key_exists(['street', 'house_number', 'zip', 'city'], $diff))  // address changed
        ) {
            $modem->geocode(false);
        } elseif (multi_array_key_exists(['x', 'y'], $diff)) {  // manually changed geodata
            if (! \App::runningInConsole()) {    // change geocode_source only from MVC (and do not overwrite data from geocode command)
                // set origin to username
                $user = \Auth::user();
                $modem->geocode_source = $user->first_name.' '.$user->last_name;
            }
        }

        // check if more values have changed – especially “x” and “y” which refreshes MPR
        $diff = $modem->getDirty();

        // Refresh MPS rules
        // Note: does not perform a save() which could trigger observer.
        if (\Module::collections()->has('HfcCustomer')) {
            if (multi_array_key_exists(['x', 'y'], $diff)) {
                // suppress output in this case
                ob_start();
                \Modules\HfcCustomer\Entities\Mpr::ruleMatching($modem);
                ob_end_clean();
            }
        }
    }

    public function updated($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        if (! $modem->observer_enabled) {
            return;
        }

        // only restart, make dhcp and configfile and only restart dhcpd via systemdobserver when it's necessary
        $diff = $modem->getDirty();

        if (multi_array_key_exists(['contract_id', 'public', 'internet_access', 'configfile_id', 'qos_id', 'mac'], $diff)) {
            Modem::create_ignore_cpe_dhcp_file();
            $modem->make_dhcp_cm();
            $modem->restart_modem(array_key_exists('mac', $diff));
            $modem->make_configfile();
        }

        // ATTENTION:
        // If we ever think about moving modems to other contracts we have to delete envia TEL related stuff, too –
        // check contract_ext* and installation_address_change_date
        // moving then should only be allowed without attached phonenumbers and terminated envia TEL contract!
        // cleaner in Patrick's opinion would be to delete and re-create the modem
    }

    public function deleted($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        // $modem->make_dhcp_cm_all();
        Modem::create_ignore_cpe_dhcp_file();
        $modem->make_dhcp_cm(true);
        $modem->restart_modem();
        $modem->delete_configfile();
    }
}
