<?php

namespace Modules\ProvBase\Entities;

use DB;
use Log;
use File;
use Module;
use App\Sla;
use Request;
use Acme\php\ArrayHelper;

class Modem extends \BaseModel
{
    // get functions for some address select options
    use \App\AddressFunctionsTrait;
    use \App\Extensions\Geocoding\Geocoding;

    const TYPES = ['cm', 'tr069'];

    // The associated SQL table for this Model
    public $table = 'modem';

    public $guarded = ['formatted_support_state'];
    protected $appends = ['formatted_support_state'];

    // Add your validation rules here
    // see: http://stackoverflow.com/questions/22405762/laravel-update-model-with-unique-validation-rule-for-attribute
    public static function rules($id = null)
    {
        return [
            'mac' => 'mac',
            'ppp_username' => 'nullable|unique:modem,ppp_username,'.$id.',id,deleted_at,NULL',
            'birthday' => 'nullable|date',
            'country_code' => 'regex:/^[A-Z]{2}$/',
            'contract_id' => 'required|exists:contract,id,deleted_at,NULL',
            'configfile_id' => 'required|exists:configfile,id,deleted_at,NULL,public,yes',
            // Note: realty_id and apartment_id validations are done in ModemController@prepare_rules
            // 'realty_id' => 'nullable|empty_with:apartment_id',
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
        if (\Request::filled('modem_show_filter')) {
            \Session::put('modem_show_filter', \Request::get('modem_show_filter'));
        }
        // non-datatable request; current route is null on testing
        elseif (\Route::getCurrentRoute() && basename(\Route::getCurrentRoute()->uri) == 'Modem') {
            \Session::forget('modem_show_filter');
        }

        $ret = ['table' => $this->table,
            'index_header' => [$this->table.'.id', $this->table.'.mac', 'configfile.name', $this->table.'.model', $this->table.'.sw_rev', $this->table.'.name', $this->table.'.firstname', $this->table.'.lastname', $this->table.'.city', $this->table.'.district', $this->table.'.street', $this->table.'.house_number', $this->table.'.us_pwr', $this->table.'.geocode_source', $this->table.'.inventar_num', 'contract_valid'],
            'bsclass' => $bsclass,
            'header' => $this->label(),
            'edit' => ['us_pwr' => 'get_us_pwr', 'contract_valid' => 'get_contract_valid'],
            'eager_loading' => ['configfile', 'contract'],
            'disable_sortsearch' => ['contract_valid' => 'false'],
            'help' => [$this->table.'.model' => 'modem_update_frequency', $this->table.'.sw_rev' => 'modem_update_frequency'],
            'order_by' => ['0' => 'desc'],
            'where_clauses' => self::_get_where_clause(),
        ];

        if (Sla::firstCached()->valid()) {
            $ret['index_header'][] = $this->table.'.support_state';
            $ret['edit']['support_state'] = 'getSupportState';
            $ret['raw_columns'][] = 'support_state';
        }

        return $ret;
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        switch ($this->get_state('int')) {
            case 0:	$bsclass = 'success'; break; // online
            case 1: $bsclass = 'warning'; break; // warning
            case 2: $bsclass = 'warning'; break; // critical
            case 3: $bsclass = $this->internet_access && $this->contract->isValid('Now') ? 'danger' : 'info'; break; // offline

            default: $bsclass = 'danger'; break;
        }

        return $bsclass;
    }

    public function label()
    {
        $label = $this->mac ?: $this->ppp_username;
        $label .= $this->name ? ' - '.$this->name : '';
        $label .= $this->firstname ? ' - '.$this->firstname.' '.$this->lastname : '';

        return $label;
    }

    /**
     * Return Fontawesome emoji class, and Bootstrap text color
     * @return array
     */
    public function getFaSmileClass()
    {
        switch ($this->support_state) {
            case 'full-support':      $faClass = 'fa-smile-o'; $bsClass = 'success'; break;
            case 'verifying':         $faClass = 'fa-meh-o'; $bsClass = 'warning'; break;
            case 'not-supported':     $faClass = 'fa-frown-o'; $bsClass = 'danger'; break;
            default: $faClass = 'fa-smile'; $bsClass = 'success'; break;
        }

        return ['fa-class'=> $faClass, 'bs-class'=> $bsClass];
    }

    public function get_contract_valid()
    {
        return $this->contract->isValid('Now') ? \App\Http\Controllers\BaseViewController::translate_label('yes') : \App\Http\Controllers\BaseViewController::translate_label('no');
    }

    public function get_us_pwr()
    {
        return $this->us_pwr.' dBmV';
    }

    public function getSupportState()
    {
        return $this->formatted_support_state." <i class='pull-right fa fa-2x ".$this->getFaSmileClass()['fa-class'].' text-'.$this->getFaSmileClass()['bs-class']."'></i>";
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
     * return all Configfile Objects for CMs (for Edit view)
     */
    public function configfiles()
    {
        $types = $this->exists ? [$this->configfile->device] : self::TYPES;

        return Configfile::select(['id', 'name'])->where('public', '=', 'yes')->whereIn('device', $types)->get();
    }

    /**
     * return all Configfile Objects for CMs
     */
    public function qualities()
    {
        return DB::table('qos')->whereNull('deleted_at')->get();
    }

    /**
     * Formatted attribute of support state.
     * @return string
     */
    public function getFormattedSupportStateAttribute()
    {
        return ucfirst(str_replace('-', ' ', $this->support_state));
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
        if (! Module::collections()->has('ProvVoipEnvia')) {
            throw new \LogicException(__METHOD__.' only callable if module ProvVoipEnvia as active');
        }

        return $this->hasMany(\Modules\ProvVoipEnvia\Entities\EnviaOrder::class)->where('ordertype', 'NOT LIKE', 'order/create_attachment');
    }

    /**
     * related enviacontracts
     */
    public function enviacontracts()
    {
        if (! Module::collections()->has('ProvVoipEnvia')) {
            throw new \LogicException(__METHOD__.' only callable if module ProvVoipEnvia as active');
        } else {
            return $this->hasMany(\Modules\ProvVoipEnvia\Entities\EnviaContract::class);
        }
    }

    public function configfile()
    {
        return $this->belongsTo(Configfile::class);
    }

    public function qos()
    {
        return $this->belongsTo(Qos::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    /**
     * Return all Contracts
     * NOTE: Dont use Eloquent here as it's super slow for many models and we dont need the Eloquent instance here
     */
    public function contracts()
    {
        $contracts = DB::table('contract')->whereNull('deleted_at')->get();

        $list = [];
        foreach ($contracts as $contract) {
            $list[$contract->id] = Contract::labelFromData($contract);
        }

        return $list;
    }

    public function mtas()
    {
        return $this->hasMany(\Modules\ProvVoip\Entities\Mta::class);
    }

    public function endpoints()
    {
        return $this->hasMany(Endpoint::class);
    }

    public function netelement()
    {
        return $this->belongsTo(\Modules\HfcReq\Entities\NetElement::class);
    }

    public function apartment()
    {
        return $this->belongsTo(\Modules\PropertyManagement\Entities\Apartment::class);
    }

    public function realty()
    {
        return $this->belongsTo(\Modules\PropertyManagement\Entities\Realty::class);
    }

    public function radcheck()
    {
        return $this->hasOne(RadCheck::class, 'username', 'ppp_username');
    }

    public function radreply()
    {
        return $this->hasOne(RadReply::class, 'username', 'ppp_username');
    }

    public function radusergroups()
    {
        return $this->hasMany(RadUserGroup::class, 'username', 'ppp_username');
    }

    public function radacct()
    {
        return $this->hasMany(RadAcct::class, 'username', 'ppp_username');
    }

    public function radpostauth()
    {
        return $this->hasMany(RadPostAuth::class, 'username', 'ppp_username');
    }

    /*
     * Relation Views
     */
    public function view_belongs_to()
    {
        $relation = null;
        if (Module::collections()->has('PropertyManagement')) {
            $relation = $this->apartment;
        }

        if ($relation) {
            // Let contract be first as just the first relation is used in modem analyses - see top.blade.php
            return collect([$this->contract, $relation]);
        }

        return $this->contract;
    }

    public function view_has_many()
    {
        $ret = [];

        if (Module::collections()->has('ProvVoip')) {
            $this->setRelation('mtas', $this->mtas()->with('configfile')->get());
            $ret['Edit']['Mta']['class'] = 'Mta';
            $ret['Edit']['Mta']['relation'] = $this->mtas;
        }

        // only show endpoints (and thus the ability to create a new one) for public CPEs
        if ($this->public) {
            $ret['Edit']['Endpoint']['class'] = 'Endpoint';
            $ret['Edit']['Endpoint']['relation'] = $this->endpoints;
        }

        if (Module::collections()->has('ProvVoipEnvia')) {
            $ret['Edit']['EnviaContract']['class'] = 'EnviaContract';
            $ret['Edit']['EnviaContract']['relation'] = $this->enviacontracts;
            $ret['Edit']['EnviaContract']['options']['hide_create_button'] = 1;
            $ret['Edit']['EnviaContract']['options']['hide_delete_button'] = 1;

            $ret['Edit']['EnviaOrder']['class'] = 'EnviaOrder';
            $ret['Edit']['EnviaOrder']['relation'] = $this->_envia_orders;
            $ret['Edit']['EnviaOrder']['options']['create_button_text'] = trans('provvoipenvia::view.enviaOrder.createButton');
            $ret['Edit']['EnviaOrder']['options']['delete_button_text'] = trans('provvoipenvia::view.enviaOrder.deleteButton');

            // TODO: auth - loading controller from model could be a security issue ?
            $ret['Edit']['EnviaAPI']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
            $ret['Edit']['EnviaAPI']['view']['vars']['extra_data'] = \Modules\ProvBase\Http\Controllers\ModemController::_get_envia_management_jobs($this);
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
     * @return string
     */
    private function generate_cm_dhcp_entry($server = '')
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        // FF-00-00-00-00 to FF-FF-FF-FF-FF reserved according to RFC7042
        if (stripos($this->mac, 'ff:') === 0) {
            return '';
        }

        if (! $this->mac) {
            return '';
        }

        $ret = 'host '.$this->hostname.' { hardware ethernet '.$this->mac.'; filename "cm/'.$this->hostname.'.cfg"; ddns-hostname "'.$this->hostname.'";';

        if (Module::collections()->has('ProvVoip') && $this->mtas()->pluck('mac')->filter(function ($mac) {
            return stripos($mac, 'ff:') !== 0;
        })->count()) {
            $ret .= ' option ccc.dhcp-server-1 '.($server ?: ProvBase::first()->provisioning_server).';';
        }

        return $ret."}\n";
    }

    private function generate_cm_dhcp_entry_pub()
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        if (! $this->mac) {
            return '';
        }

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
            $modems_raw = self::whereNotNull('mac')->get();
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

        // Check for hostname to avoid deleting the wrong entry when mac exists multiple times in DB !?
        foreach ($conf as $key => $line) {
            if (strpos($line, "$this->hostname {") !== false) {
                unset($conf[$key]);
                break;
            }
        }

        if (! $delete && $data) {
            $conf[] = $data;
        }

        self::_write_dhcp_file(self::CONF_FILE_PATH, implode($conf)); //	PHP_EOL

        // public ip
        if ($this->public || ($original && $original['public'])) {
            $data_pub = $this->generate_cm_dhcp_entry_pub();

            $conf_pub = [];
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

            if (! $delete && $this->public && $data_pub) {
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

        if ($this->isTR069()) {
            $this->createGenieAcsPresets($this->configfile->text_make($this, 'tr069'));

            return;
        }

        /* Configfile */
        $dir = '/tftpboot/cm/';
        $cf_file = $dir."cm-$this->id.conf";
        $cfg_file = $dir."cm-$this->id.cfg";

        if (! $this->configfile) {
            return false;
        }

        // Evaluate network access (NA) and MaxCPE count
        // Note: NA becomes only zero when internet is disabled on contract (no valid tariff) or modem (manually) and contract has no telephony
        $cpe_cnt = ProvBase::first()->max_cpe;
        $max_cpe = $cpe_cnt ?: 2; 		// default 2
        $internet_access = 1;

        if (Module::collections()->has('ProvVoip') && (count($this->mtas))) {
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

        if (Module::collections()->has('ProvVoip') && $internet_access) {
            foreach ($this->mtas as $mta) {
                $conf .= "\tCpeMacAddress $mta->mac;\n";
            }
        }

        $text = "Main\n{\n".$conf.$this->configfile->text_make($this, 'modem')."\n}";

        if (File::put($cf_file, $text) === false) {
            die('Error writing to file');
        }

        Log::info('Trying to build configfile for modem '.$this->hostname);
        Log::debug("configfile: docsis -e $cf_file $dir../keyfile $cfg_file");

        // "&" to start docsis process in background improves performance but we can't reliably proof if file exists anymore
        exec("docsis -e $cf_file $dir../keyfile $cfg_file >/dev/null 2>&1 &", $out);

        // TODO: Error handling
        // This is not trivial because docsis is started in background:
        //      - therefore return value is always “0” (independent of the actual error code)
        //      - STDERR output is not redirected and is stored in $out – but too late for a check
        //      - same problem with checks for existance and date comparisions of .cfg files
        // As there is no solution ATM (except removing the “&” and slowing down the whole process) nothing is done here!
        //
        // As a workaround there will be an Icinga check (existance of .cfg files and comparision of the dates of .conf and .cfg files)

        // change owner in case command was called from command line via php artisan nms:configfile that changes owner to root
        system('/bin/chown -R apache /tftpboot/cm');

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
     * Create TR-069 configfile.
     * GenieACS API: https://github.com/genieacs/genieacs/wiki/API-Reference
     *
     * @author Ole Ernst
     */
    public function createGenieAcsPresets($text = null)
    {
        $text = $text ?? $this->configfile->text;
        if (! $text) {
            return;
        }

        $this->createGenieAcsProvisions($text);

        $preset = [
            'weight' => 0,
            'precondition' => json_encode([
                '_deviceId._SerialNumber' => $this->serial_num,
            ]),
            'events' => [
                '0 BOOTSTRAP' => true,
            ],
            'configurations' => [
                [
                    'type' => 'provision',
                    'name' => $this->id,
                ],
            ],
        ];

        self::callGenieAcsApi("presets/$this->id", 'PUT', json_encode($preset));

        unset($preset['events']['0 BOOTSTRAP']);
        $preset['events']['2 PERIODIC'] = true;
        $preset['configurations'][0]['name'] = "mon-{$this->configfile->id}";

        self::callGenieAcsApi("presets/mon-$this->id", 'PUT', json_encode($preset));
    }

    /**
     * Refresh the online state of all PPP device by checking if their last
     * accounting update was within the last $defaultInterimIntervall seconds
     *
     * @author Ole Ernst
     */
    public static function refreshPPP()
    {
        $hf = array_flip(config('hfcreq.hfParameters'));

        $online = RadAcct::where(
            'acctupdatetime',
            '>=',
            \Carbon\Carbon::now()->subSeconds(RadGroupReply::$defaultInterimIntervall)
        )->pluck('username');

        DB::beginTransaction();
        // make all ppp devices offline
        // toBase() is needed since updated_at is ambiguous
        self::join('configfile', 'configfile.id', 'modem.configfile_id')
            ->where('configfile.device', 'tr069')
            ->whereNull('configfile.deleted_at')
            ->toBase()
            ->update(array_merge(array_combine($hf, [0, 0, 0, 0]), ['modem.updated_at' => now()]));

        // set all ppp devices online, which sent us an accounting update
        // in the last $defaultInterimIntervall seconds
        // for now we set them to a sensible DOCIS US power level to make them green
        self::whereIn('ppp_username', $online)->update(array_combine($hf, [40, 36, 0, 36]));
        DB::commit();
    }

    /**
     * Create Provision from configfile.text.
     *
     * @author Roy Schneider
     * @param string $text
     * @return bool
     */
    public function createGenieAcsProvisions($text)
    {
        $prefix = '';

        // during bootstrap always clear the info we have about the device
        $prov = [
            "clear('Device', Date.now());",
            "clear('InternetGatewayDevice', Date.now());",
        ];

        foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
            $vals = str_getcsv(trim($line), ';');
            if (! count($vals) || ! in_array($vals[0], ['add', 'clr', 'commit', 'del', 'get', 'jmp', 'reboot', 'set', 'fw', 'raw'])) {
                continue;
            }

            if (! isset($vals[1])) {
                $vals[1] = '';
            }

            $path = trim("$prefix.$vals[1]", '.');

            switch ($vals[0]) {
                case 'add':
                    if (isset($vals[2])) {
                        $prov[] = "declare('$path.[$vals[2]]', {value: Date.now()}, {path: 1});";
                    }
                    break;
                case 'clr':
                    $prov[] = "clear('$path', Date.now());";
                    break;
                case 'commit':
                    $prov[] = 'commit();';
                    break;
                case 'del':
                    $prov[] = "declare('$path.[]', null, {path: 0})";
                    break;
                case 'get':
                    $prov[] = "declare('$path.*', {value: Date.now()});";
                    break;
                case 'jmp':
                    $prefix = trim($vals[1], '.');
                    break;
                case 'reboot':
                    if (! $vals[1]) {
                        $vals[1] = 0;
                    }
                    $prov[] = "declare('Reboot', null, {value: Date.now() - ($vals[1] * 1000)});";
                    break;
                case 'set':
                    if (isset($vals[2])) {
                        $prov[] = "declare('$path', {value: Date.now()} , {value: '$vals[2]'});";
                    }
                    break;
                case 'fw':
                    if (! empty($vals[1]) && ! empty($vals[2])) {
                        $prov[] = "declare('Downloads.[FileType:$vals[1]]', {path: 1}, {path: 1});";
                        $prov[] = "declare('Downloads.[FileType:$vals[1]].FileName', {value: 1}, {value: '$vals[2]'});";
                        $prov[] = "declare('Downloads.[FileType:$vals[1]].Download', {value: 1}, {value: Date.now()});";
                    }
                    break;
                case 'raw':
                    $prov[] = "$vals[1]";
                    break;
            }
        }

        self::callGenieAcsApi("provisions/$this->id", 'PUT', implode("\r\n", $prov));
    }

    /**
     * Call API of GenieACS via PHP Curl.
     *
     * @author Roy Schneider
     * @param string $route
     * @param string $customRequest
     * @param string $data
     * @return mixed $result
     */
    public static function callGenieAcsApi($route, $customRequest, $data = null, $header = [])
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => "http://localhost:7557/$route",
            CURLOPT_RETURNTRANSFER => $customRequest == 'GET' ? true : false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => $customRequest,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $header,
        ]);

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // No such device
        if ($status == 202) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get decoded json object of device from GenieACS via API.
     *
     * @param string $projection
     * @return mixed
     *
     * @author Ole Ernst
     */
    public function getGenieAcsModel($projection = null)
    {
        $route = "devices/?query={\"_deviceId._SerialNumber\":\"$this->serial_num\"}";
        if ($projection) {
            $route .= "&projection=$projection";
        }

        $model = json_decode(self::callGenieAcsApi($route, 'GET'));

        if (! $model) {
            return false;
        }

        $model = reset($model);

        if (! $projection) {
            return $model;
        }

        foreach (explode('.', $projection) as $idx) {
            if (! isset($model->{$idx})) {
                return false;
            }
            $model = $model->{$idx};
        }

        return $model;
    }

    /**
     * Delete GenieACS presets.
     *
     * @author Roy Schneider
     */
    public function deleteGenieAcsPreset()
    {
        self::callGenieAcsApi("presets/$this->id", 'DELETE');
        self::callGenieAcsApi("presets/mon-$this->id", 'DELETE');
    }

    /**
     * Delete GenieACS provision.
     *
     * @author Roy Schneider
     */
    public function deleteGenieAcsProvision()
    {
        self::callGenieAcsApi("provisions/$this->id", 'DELETE');
    }

    /**
     * Get NETGW a CM is registered on
     *
     * @param  string 	ip 		address of cm
     * @return object 	NETGW
     *
     * @author Nino Ryschawy
     */
    public static function get_netgw($ip)
    {
        $validator = new \Acme\Validators\ExtendedValidator;

        $ippools = IpPool::where('type', '=', 'CM')->get();

        foreach ($ippools as $pool) {
            if ($validator->validateIpInRange(0, $ip, [$pool->net, $pool->netmask])) {
                $netgw_id = $pool->netgw_id;
                break;
            }
        }

        if (isset($netgw_id)) {
            return NetGw::find($netgw_id);
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
        foreach (DB::table('modem')->whereNull('deleted_at')->pluck('id') as $id) {
            $tmp = self::get_firmware_tree($id);
            if (! $tmp) {
                continue;
            }

            DB::statement("UPDATE modem SET model = '$tmp[0] $tmp[1]', sw_rev = '$tmp[2]' where id='$id'");
        }
    }

    /**
     * Restarts modem through snmpset
     */
    public function restart_modem($mac_changed = false, $modem_reset = false, $factoryReset = false)
    {
        // Log
        Log::info(($factoryReset ? 'factoryReset' : 'restart').' modem '.$this->hostname);

        if ($this->isTR069()) {
            $id = $this->getGenieAcsModel('_id');
            if (! $id) {
                \Session::push('tmp_warning_above_form', trans('messages.modem_restart_error'));

                return;
            }

            $id = rawurlencode($id);
            $action = $factoryReset ? 'factoryReset' : 'reboot';
            $success = self::callGenieAcsApi("devices/$id/tasks?timeout=3000&connection_request", 'POST', "{ \"name\" : \"$action\" }");

            if (! $success) {
                \Session::push('tmp_warning_above_form', trans('messages.modem_restart_error'));

                return;
            }

            \Session::push('tmp_info_above_form', trans('messages.modem_restart_success_direct'));

            return;
        }

        // if hostname cant be resolved we dont want to have an php error
        try {
            $config = ProvBase::first();
            $fqdn = $this->hostname.'.'.$config->domain_name;
            $ip = gethostbyname($fqdn);
            $netgw = self::get_netgw($ip);
            $mac = $mac_changed ? $this->getOriginal('mac') : $this->mac;

            if ($modem_reset) {
                throw new \Exception('Reset Modem directly');
            }

            if ($fqdn == $ip) {
                \Log::error("Could not restart $this->hostname. DNS server can not resolve hostname.");

                return;
            }

            if (! $netgw) {
                throw new \Exception('NetGw could not be determined for modem');
            }

            if (! in_array($netgw->company, ['Casa', 'Cisco', 'Motorola'])) {
                throw new \Exception("Modem restart via NetGw vendor $netgw->company not yet implemented");
            }

            if ($netgw->company == 'Cisco') {
                $param = [
                    '1.3.6.1.4.1.9.9.116.1.3.1.1.9.'.implode('.', array_map('hexdec', explode(':', $mac))),
                    'i',
                    '1',
                ];
            }

            if ($netgw->company == 'Casa') {
                $param = [
                    '1.3.6.1.4.1.20858.10.12.1.3.1.7.'.implode('.', array_map('hexdec', explode(':', $mac))),
                    'i',
                    '1',
                ];
            }

            if ($netgw->company == 'Motorola') {
                $param = [
                    '1.3.6.1.4.1.4981.2.2.2.0',
                    'x',
                    implode(' ', explode(':', $mac)),
                ];
            }

            snmpset($netgw->ip, $netgw->get_rw_community(), $param[0], $param[1], $param[2], 300000, 1);

            // success message
            \Session::push('tmp_info_above_form', trans('messages.modem_restart_success_netgw'));
        } catch (\Exception $e) {
            \Log::error("Could not delete $this->hostname from NETGW ('".$e->getMessage()."'). Let's try to restart it directly.");

            try {
                // restart modem - DOCS-CABLE-DEV-MIB::docsDevResetNow
                snmpset($fqdn, $config->rw_community, '1.3.6.1.2.1.69.1.1.3.0', 'i', '1', 300000, 1);

                // success message - make it a warning as sth is wrong when it's not already restarted by NETGW??
                \Session::push('tmp_info_above_form', trans('messages.modem_restart_success_direct'));
            } catch (\Exception $e) {
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
     * Perform a factory reset on a TR-069 device
     *
     * @author: Ole Ernst
     */
    public function factoryReset()
    {
        return $this->restart_modem(false, false, true);
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
        } catch (\Exception $e) {
            try {
                $log = snmprealwalk($fqdn, $com, '.1.3.6.1.2.1.69.1.5.8.1');
            } catch (\Exception $e) {
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

            $time = \Carbon\Carbon::create(...array_slice(array_map('hexdec', $time), 0, 6));
            $log[$time_key][$k] = $time;
        }

        // translate severity level of log entry to datatable colors
        $color_key = array_keys($log)[2];
        $text_key = array_keys($log)[3];

        $ignore = ['T3 time', 'T4 time'];
        $trans = ['', 'danger', 'danger', 'danger', 'danger', 'warning', 'success', '', 'info'];
        foreach ($log[$color_key] as $k => $color_idx) {
            $log[$color_key][$k] = \Str::contains($log[$text_key][$k], $ignore) ? '' : $trans[$color_idx];
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

    /**
     * Get Pre-equalization data of a modem via cacti
     *
     * @return: Array
     * @author: John Adebayo
     */
    private function _threenibble($hexcall)
    {
        $ret = [];
        $counter = 0;

        foreach ($hexcall as $hex) {
            $counter++;
            if ($counter < 49) {
                $hex = str_split($hex, 1);
                if (ctype_alpha($hex[1]) || $hex[1] > 7) {
                    $hex[0] = 'F';
                    $hex = implode('', $hex);
                    $hex = preg_replace('/[^0-9A-Fa-f]/', '', $hex);
                    $hex = strrev("$hex");
                    $dec = last(unpack('s', pack('h*', "$hex")));
                    array_push($ret, $dec);
                } else {
                    $hex[0] = 0;
                    $hex = implode('', $hex);
                    $hex = preg_replace('/[^0-9A-Fa-f]/', '', $hex);
                    $hex = strrev("$hex");
                    $dec = last(unpack('s', pack('h*', "$hex")));
                    array_push($ret, $dec);
                }
            }
        }

        return $ret;
    }

    private function _fournibble($hexcall)
    {
        $ret = [];
        $counter = 0;
        foreach ($hexcall as $hex) {
            $counter++;
            if ($counter < 49) {
                $hex = preg_replace('/[^0-9A-Fa-f]/', '', $hex);
                $hex = strrev("$hex");
                $dec = last(unpack('s', pack('h*', "$hex")));
                array_push($ret, $dec);
            }
        }

        return $ret;
    }

    private function _nePwr($decimal, $maintap)
    {
        $pwr = [];
        $ans = implode('', array_keys($decimal, max($decimal)));
        if ($maintap == $ans) {
            $a2 = $decimal[$maintap];
            $b2 = $decimal[$maintap + 1];
            foreach (array_chunk($decimal, 2) as $val) {
                $a1 = $val[0];
                $b1 = $val[1];
                $pwr[] = ($a1 * $a2 - $b1 * $b2) / ($a2 ** 2 + $b2 ** 2);
                $pwr[] = ($a2 * $b1 + $a1 * $b2) / ($a2 ** 2 + $b2 ** 2);
            }
        } else {
            for ($i = 0; $i < 48; $i++) {
                $pwr[] = 0;
            }
        }

        return $pwr;
    }

    private function _energy($pwr, $maintap, $energymain)
    {
        $ene_db = [];
        //calculating the magnitude
        $pwr = array_chunk($pwr, 2);
        foreach ($pwr as $val) {
            $temp = 10 * log10($val[0] ** 2 + $val[1] ** 2);
            if (! (is_finite($temp))) {
                $temp = -100;
            }
            $ene_db[] = round($temp, 2);
        }

        return $ene_db;
    }

    private function _tdr($ene, $energymain, $freq)
    {
        if ($ene[$energymain] == -100) {
            $tdr = 0;
        } else {
            // propgagtion speed in cable networks (87% speed of light)
            $v = 0.87 * 299792458;
            unset($ene[$energymain]);
            $highest = array_keys($ene, max($ene));
            $highest = implode('', $highest);
            $tap_diff = abs($energymain - $highest);
            // 0.8 - Roll-off of filter; /2 -> round-trip (back and forth)
            $tdr = $v * $tap_diff / (0.8 * $freq) / 2;
            $tdr = round($tdr, 1);
        }

        return $tdr;
    }

    private function _chart($ene)
    {
        $chart = [];
        $min = min($ene);
        foreach ($ene as $value) {
            $chart[] = round($min);
        }

        return $chart;
    }

    private function _fft($pwr)
    {
        $rea = [];
        $imag = [];
        $pwr = array_chunk($pwr, 2);
        foreach ($pwr as $val) {
            $rea[] = $val[0];
            $imag[] = $val[1];
        }

        for ($i = 0; $i < 104; $i++) {
            array_push($rea, 0);
            array_push($imag, 0);
        }

        for ($i = 0; $i < 248; $i++) {
            array_push($rea, array_shift($rea));
            array_push($imag, array_shift($imag));
        }

        $ans = \Brokencube\FFT\FFT::run($rea, $imag);
        ksort($ans[0]);
        ksort($ans[1]);
        for ($i = 0; $i < 64; $i++) {
            array_push($ans[0], array_shift($ans[0]));
            array_push($ans[1], array_shift($ans[1]));
        }

        $answer = array_map(function ($v1, $v2) {
            return 20 * log10(sqrt($v1 ** 2 + $v2 ** 2));
        }, $ans[0], $ans[1]);

        // stores the maximum amplitude value of the fft waveform
        $x = max($answer);
        $y = abs(min($answer));
        $maxamp = $x >= $y ? $x : $y;

        if (! (is_finite($maxamp))) {
            $maxamp = 0;
        }

        return [$answer, $maxamp];
    }

    private function _xaxis()
    {
        $axis = [];
        for ($i = -0.5; $i <= 0.5; $i += 0.0078125) {
            $axis[] = $i;
        }

        return $axis;
    }

    public function get_preq_data()
    {
        $domain = ProvBase::first()->domain_name;
        $file = "/usr/share/cacti/rra/$this->hostname.$domain.json";

        if (! file_exists($file)) {
            return ['No pre-equalization data found'];
        }

        $preq = json_decode(file_get_contents($file), true);
        if (empty($preq['data']) || empty($preq['width']) || (! isset($preq['data'][199]))) {
            return ['No pre-equalization data found'];
        }

        $ret = [];

        $freq = $preq['width'];
        $hexs = str_split($preq['data'], 8);
        $or_hexs = array_shift($hexs);
        $maintap = 2 * $or_hexs[1] - 2;
        $energymain = $maintap / 2;
        array_splice($hexs, 0, 0);
        $hexs = implode('', $hexs);
        $hexs = str_split($hexs, 4);
        $hexcall = $hexs;
        $counter = 0;
        foreach ($hexs as $hex) {
            $hsplit = str_split($hex, 1);
            $counter++;
            if (is_numeric($hsplit[0]) && $hsplit[0] == 0 && $counter >= 46) {
                $decimal = $this->_threenibble($hexcall);
                break;
            } elseif (ctype_alpha($hsplit[0]) || $hsplit[0] != 0 && $counter >= 46) {
                $decimal = $this->_fournibble($hexcall);
                break;
            }
        }

        $pwr = $this->_nePwr($decimal, $maintap);
        $ene = $this->_energy($pwr, $maintap, $energymain);
        $chart = $this->_chart($ene);
        $fft = $this->_fft($pwr);
        $tdr = $this->_tdr($ene, $energymain, $freq);
        $index = $this->_xaxis();

        $ret['power'] = $pwr;
        $ret['energy'] = $ene;
        $ret['chart'] = $chart;
        $ret['tdr'] = $tdr;
        $ret['max'] = $fft[1];
        $ret['fft'] = $fft[0];
        $ret['axis'] = $index;

        return $ret;
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

        if ($this->us_pwr > config('hfccustomer.threshhold.single.us.critical')) {
            if ($return_type == 'string') {
                return 'critical';
            } else {
                return 2;
            }
        }

        if ($this->us_pwr > config('hfccustomer.threshhold.single.us.warning')) {
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
        if (! Module::collections()->has('ProvVoip')) {
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
        if (! Module::collections()->has('ProvVoip')) {
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
        if (! Module::collections()->has('ProvVoipEnvia')) {
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
        foreach (DB::table('modem')->select('id', 'x', 'y')->where('deleted_at', null)->get() as $modem) {
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
     *		  -1 for reset via NETGW (faster),
     *		   0 if no restart is needed
     *
     * @author Ole Ernst, Nino Ryschawy
     *
     * NOTE: returns 1 when modem is created
     */
    public function needs_restart()
    {
        $diff = $this->getDirty();

        // in case mac was changed, reset via netgw - or take original mac
        if (array_key_exists('mac', $diff)) {
            return 1;
        }

        if (multi_array_key_exists(['contract_id', 'public', 'internet_access', 'configfile_id', 'qos_id'], $diff)) {
            return -1;
        }

        return 0;
    }

    /**
     * Store address from Realty internally in modem table too, as it is used in many places (e.g. EnviaAPI)
     *
     * @param \Modules\PropertyManagement\Entities\Realty
     * @param array  of Contract IDs to update multiple Contracts by one DB query
     */
    public function updateAddressFromProperty($realty = null, $ids = [])
    {
        if (! Module::collections()->has('PropertyManagement')) {
            return;
        }

        if (! $realty) {
            $realty = $this->apartment ? $this->apartment->realty : null;
        }

        if (! $realty) {
            return;
        }

        self::whereIn('id', $ids ?: [$this->id])->update([
            'street' => $realty->street,
            'house_number' => $realty->house_nr,
            'zip' => $realty->zip,
            'city' => $realty->city,
            'district' => $realty->district,
        ]);
    }

    /**
     * Get list of apartments for select field of edit view
     *
     * @author Nino Ryschawy
     * @return array
     */
    public function getApartmentsList()
    {
        if (! Module::collections()->has('PropertyManagement')) {
            return [];
        }

        if (Request::has('contract_id')) {
            $this->contract_id = Request::get('contract_id');
        }

        // Contracts indirectly related to an apartment that are not canceled
        // under these can be a potential new contract of an apartment that already has a modem with a canceled contract
        $contractSubQuery = Contract::join('modem', 'modem.contract_id', 'contract.id')
            ->join('apartment', 'modem.apartment_id', 'apartment.id')
            ->whereNull('contract.contract_end')
            ->whereNull('modem.deleted_at')->whereNull('contract.deleted_at')->whereNull('apartment.deleted_at')
            ->select('contract.id', 'apartment.id as apartmentId'
                // , 'contract.number', 'contract.firstname', 'contract.lastname', 'contract.contract_start', 'contract.contract_end',
                // 'modem.mac', 'contract.created_at',
                );

        /* All apartments that either
            (1) do not have a modem assigned
            (2) or that have already a modem assigned that belongs to the same contract as this modem belongs to
            (3) or that have a contract that is already canceled
        */
        $apartmentsSubQuery = \Modules\PropertyManagement\Entities\Apartment::join('realty', 'realty.id', 'apartment.realty_id')
            ->leftJoin('modem', 'modem.apartment_id', 'apartment.id')
            ->leftJoin('contract', 'contract.id', 'modem.contract_id')
            ->whereNull('apartment.deleted_at')
            ->where(function ($query) {
                $query
                ->whereNull('modem.id')
                ->orWhere('modem.contract_id', $this->contract_id)
                ->orWhereNotNull('contract.contract_end');
            })
            ->select('realty.street', 'realty.house_nr', 'realty.city', 'apartment.id as apartmentId', 'apartment.number as anum', 'floor',
                'modem.id as modemId', 'contract.id as cId'
                // , 'realty.zip', 'realty.district', 'realty.number as rnum',
                // 'contract.number as cnum', 'contract.firstname', 'contract.lastname', 'contract.contract_start', 'contract.contract_end',
                );

        // All the apartments (that have no contract or a/many canceled contract(s)) from the subquery are left joined
        // with the possible new contracts to filter (dont show) apartments that indeed have a canceled contract but have
        // already a new valid one assigned
        // TODO: From Laravel v5.6 on it is possible to use fromSub()
        $apartments = DB::table(DB::raw("({$apartmentsSubQuery->toSql()}) as apartments"))
            ->mergeBindings($apartmentsSubQuery->getQuery())
            ->select('apartments.street', 'apartments.house_nr', 'apartments.city', 'apartments.apartmentId as id',
                'apartments.anum as number', 'floor'
                // 'apartments.cnum', 'apartments.firstname', 'apartments.lastname', 'apartments.contract_start', 'apartments.contract_end',
                // 'newContract.number as newContractNr'
                )
            ->leftJoin(DB::raw("({$contractSubQuery->toSql()}) as newContract"), 'newContract.apartmentId', '=', 'apartments.apartmentId')
            ->mergeBindings($contractSubQuery->getQuery())
            ->where(function ($query) {
                $query
                ->whereNull('apartments.modemId')
                ->orWhere(function ($query) {
                    $query
                    ->whereNotNull('apartments.cId')
                    ->where('apartments.cId', $this->contract_id);
                })
                ->orWhere(function ($query) {
                    $query
                    ->whereNull('newContract.id')
                    ->orWhere('newContract.id', $this->contract_id);
                });
            })
            ->orderBy('apartments.street')->orderBy('apartments.house_nr')->orderBy('apartments.anum')
            ->get();

        $arr[null] = null;
        foreach ($apartments as $apartment) {
            $arr[$apartment->id] = \Modules\PropertyManagement\Entities\Apartment::labelFromData($apartment);
        }

        return $arr;
    }

    /**
     * Check if modem throughput is provisioned via PPP(oE)
     *
     * @return  true if PPP(oE) is used
     *          false if PPP(oE) is not used
     *
     * @author Ole Ernst
     */
    public function isPPP()
    {
        return boolval($this->ppp_username);
    }

    /**
     * Check if modem is provisioned via TR069
     *
     * @return  true if TR069 is used
     *          false if TR069 is not used
     *
     * @author Ole Ernst
     */
    public function isTR069()
    {
        return $this->configfile->device === 'tr069';
    }

    /**
     * Synchronize radcheck with modem table, if PPPoE is used.
     *
     * @author Ole Ernst
     */
    private function updateRadCheck()
    {
        if ($this->deleted_at || ! $this->isPPP() || ! $this->internet_access) {
            $this->radcheck()->delete();

            return;
        }

        // renew RadCheck, if non-exisiting or not as expected
        if ($this->radcheck()->count() != 2) {
            $this->radcheck()->delete();
            $psw = $this->ppp_password;
            if (! $psw) {
                $psw = \Acme\php\Password::generate_password();
                // update ppp_password without invoking the observer
                self::where('id', $this->id)->update(['ppp_password' => $psw]);
                // set $this->ppp_password as well to keep model in sync, e.g. getDirty()
                $this->ppp_password = $psw;
            }

            $check = new RadCheck;
            $check->username = $this->ppp_username;
            $check->attribute = 'Cleartext-Password';
            $check->op = ':=';
            $check->value = $psw;
            $check->save();

            $check = new RadCheck;
            $check->username = $this->ppp_username;
            $check->attribute = 'Pool-Name';
            $check->op = ':=';
            $check->value = $this->public ? 'CPEPub' : 'CPEPriv';
            $check->save();

            return;
        }

        // update existing RadCheck, if password was changed
        if (array_key_exists('ppp_password', $this->getDirty())) {
            $check = $this->radcheck;
            $check->value = $this->ppp_password;
            $check->save();
            $this->make_configfile();
            $this->factoryReset();
        }
    }

    /**
     * Synchronize radusergroups with modem table, if PPPoE is used.
     *
     * @author Ole Ernst
     */
    private function updateRadUserGroups()
    {
        if ($this->deleted_at || ! $this->isPPP() || ! $this->internet_access) {
            $this->radusergroups()->delete();

            return;
        }

        // renew RadUserGroups, if non-exisiting or not as expected
        if ($this->radusergroups()->count() != 2) {
            $this->radusergroups()->delete();

            // default and QoS-specific group
            foreach (array_unique([RadGroupReply::$defaultGroup, $this->qos_id]) as $groupname) {
                if ($groupname === null) {
                    continue;
                }
                $group = new RadUserGroup;
                $group->username = $this->ppp_username;
                $group->groupname = $groupname;
                $group->save();
            }

            return;
        }

        // update existing RadUserGroups, if qos was changed
        if (array_key_exists('qos_id', $this->getDirty())) {
            $this->radusergroups()
                ->where('groupname', '!=', RadGroupReply::$defaultGroup)
                ->update(['groupname' => $this->qos_id]);
            $this->restart_modem();
        }
    }

    /**
     * Synchronize the freeradius tables with NMSPrime.
     * This function should be called on created(), updated() and deleted()
     * in the modem observer.
     *
     * @author Ole Ernst
     */
    public function updateRadius()
    {
        $this->updateRadCheck();
        $this->updateRadUserGroups();
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

        if (Module::collections()->has('PropertyManagement')) {
            $modem->updateAddressFromProperty();
        }

        $hostname = ($modem->isPPP() ? 'ppp-' : 'cm-').$modem->id;
        $modem->hostname = $hostname;

        // always set hostname, even if updating() fails (e.g php warning)
        // this is needed for a consistent dhcpd config
        Modem::where('id', $modem->id)->update(['hostname' => $hostname]);

        $modem->updateRadius();

        $modem->save();  // forces to call the updating() and updated() method of the observer !

        if (Module::collections()->has('ProvMon')) {
            Log::info("Create cacti diagrams for modem: $modem->hostname");
            \Artisan::call('nms:cacti', ['--netgw-id' => 0, '--modem-id' => $modem->id]);
        }
    }

    public function updating($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        // reminder: on active envia TEL module: moving modem to other contract is not allowed!
        // check if this is running if you decide to implement moving of modems to other contracts
        // watch Ticket LAR-106
        if (Module::collections()->has('ProvVoipEnvia')) {
            // updating is also called on create – so we have to check this
            if ((! $modem->wasRecentlyCreated) && ($modem->isDirty('contract_id'))) {
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
        if (Module::collections()->has('HfcCustomer')) {
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

        if (multi_array_key_exists(['contract_id', 'public', 'internet_access', 'configfile_id', 'qos_id', 'mac', 'serial_num'], $diff)) {
            Modem::create_ignore_cpe_dhcp_file();
            $modem->make_dhcp_cm();
            $modem->restart_modem(array_key_exists('mac', $diff));
            $modem->make_configfile();
        }

        $modem->updateRadius();

        // ATTENTION:
        // If we ever think about moving modems to other contracts we have to delete envia TEL related stuff, too –
        // check contract_ext* and installation_address_change_date
        // moving then should only be allowed without attached phonenumbers and terminated envia TEL contract!
        // cleaner in Patrick's opinion would be to delete and re-create the modem

        if (array_key_exists('apartment_id', $diff)) {
            $modem->updateAddressFromProperty();
        }
    }

    public function deleted($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        if ($modem->isTR069()) {
            $modem->deleteGenieAcsProvision();
            $modem->deleteGenieAcsPreset();
            $modem->factoryReset();
        }

        $modem->updateRadius();

        Modem::create_ignore_cpe_dhcp_file();
        $modem->make_dhcp_cm(true);
        $modem->restart_modem();
        $modem->delete_configfile();
    }
}
