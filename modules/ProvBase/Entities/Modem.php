<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Modules\ProvBase\Entities;

use DB;
use Str;
use File;
use Module;
use App\Sla;
use Request;
use Session;
use Storage;
use Acme\php\ArrayHelper;
use Illuminate\Support\Facades\Log;
use Modules\ProvBase\Traits\HasConfigfile;
use Modules\ProvBase\Http\Controllers\ModemController;

class Modem extends \BaseModel
{
    use HasConfigfile;
    use \App\AddressFunctionsTrait; // get functions for some address select options
    use \App\extensions\geocoding\Geocoding;

    public const TYPES = ['cm', 'tr069'];
    public const CWMP_EVENTS = [
        'BOOTSTRAP',
        'BOOT',
        'PERIODIC',
        'SCHEDULED',
        'VALUE CHANGE',
        'KICKED',
        'CONNECTION REQUEST',
        'TRANSFER COMPLETE',
        'DIAGNOSTICS COMPLETE',
        'REQUEST DOWNLOAD',
        'AUTONOMOUS TRANSFER COMPLETE',
        'DU STATE CHANGE COMPLETE',
        'AUTONOMOUS DU STATE CHANGE COMPLETE',
        'WAKEUP',
    ];
    public const CONFIGFILE_PREFIX = 'cm';
    public const CONFIGFILE_DIRECTORY = '/tftpboot/cm/';
    public const CONF_FILE_PATH = '/etc/dhcp-nmsprime/modems-host.conf';
    protected const CONF_FILE_PATH_PUB = '/etc/dhcp-nmsprime/modems-clients-public.conf';
    protected const IGNORE_CPE_FILE_PATH = '/etc/dhcp-nmsprime/ignore-cpe.conf';
    protected const BLOCKED_CPE_FILE_PATH = '/etc/dhcp-nmsprime/blocked.conf';
    protected $domainName = '';

    // The associated SQL table for this Model
    public $table = 'modem';

    public $guarded = ['formatted_support_state'];
    protected $appends = ['formatted_support_state'];

    /**
     * Contains all implemented index filters, also used as whitelist
     *
     * @var array
     */
    public const AVAILABLE_FILTERS = [
        'sw_rev',
    ];

    public function rules()
    {
        $id = $this->id ?: 0;

        $rules = [
            'mac' => ['mac', "unique:modem,mac,{$id},id,deleted_at,NULL"],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'country_code' => ['regex:/^[A-Z]{2}$/'],
            'contract_id' => ['required', 'exists:contract,id,deleted_at,NULL'],
            'configfile_id' => ['required', 'exists:configfile,id,deleted_at,NULL,public,yes'],
            'serial_num' => ["unique:modem,serial_num,{$id},id,deleted_at,NULL"],
            'ppp_username' => ["unique:modem,ppp_username,{$id},id,deleted_at,NULL"],
            'installation_address_change_date' => ['nullable', 'date_format:Y-m-d'],
        ];

        if (! Module::collections()->has('BillingBase')) {
            $rules['qos_id'] = ['required', 'exists:qos,id,deleted_at,NULL'];
        }

        if (request('configfile_id')) {
            $configfile = Configfile::find(Request::get('configfile_id'));
        }

        if (isset($configfile) && $configfile->device == 'tr069') {
            $rules['mac'][] = 'nullable';
            $rules['ppp_password'][] = 'required';
            // we wan't to show the required rule first, before any other validation error
            array_unshift($rules['ppp_username'], 'required');
            array_unshift($rules['serial_num'], 'required');

            return $rules;
        }

        $rules['mac'][] = 'required';
        $rules['ppp_username'][] = 'nullable';
        $rules['serial_num'][] = 'nullable';

        return $rules;
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

        $ret = ['table' => $this->table,
            'index_header' => [$this->table.'.id', $this->table.'.mac', $this->table.'.serial_num', 'configfile.name', $this->table.'.model', $this->table.'.sw_rev', $this->table.'.name', $this->table.'.ppp_username', 'qos.name', $this->table.'.firstname', $this->table.'.lastname', $this->table.'.city', $this->table.'.district', $this->table.'.street', $this->table.'.house_number', $this->table.'.apartment_nr', $this->table.'.geocode_source', $this->table.'.inventar_num', 'contract_valid'],
            'bsclass' => $bsclass,
            'header' => $this->label(),
            'edit' => ['contract_valid' => 'get_contract_valid'],
            'eager_loading' => ['configfile', 'contract', 'qos'],
            'disable_sortsearch' => ['contract_valid' => 'false'],
            'help' => [$this->table.'.model' => 'modem_update_frequency', $this->table.'.sw_rev' => 'modem_update_frequency'],
            'globalFilter' => ['sw_rev' => e(session('filter_data', ''))],
        ];

        if (Module::collections()->has('ProvMon')) {
            $hfParameters = [$this->table.'.us_pwr', $this->table.'.us_snr', $this->table.'.ds_pwr', $this->table.'.ds_snr', $this->table.'.phy_updated_at'];

            $ret['index_header'] = array_merge($ret['index_header'], $hfParameters);
        }

        if (false && Sla::firstCached()->valid()) {
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
            case 0: $bsclass = 'success'; break; // online
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
        $label .= $this->ppp_username ? ' - '.$this->ppp_username : '';

        return $label;
    }

    /**
     * Return Fontawesome emoji class, and Bootstrap text color
     *
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
        return $this?->contract->isValid('Now') ? \App\Http\Controllers\BaseViewController::translate_label('yes') : \App\Http\Controllers\BaseViewController::translate_label('no');
    }

    public function getSupportState()
    {
        return $this->formatted_support_state." <i class='pull-right fa fa-2x ".$this->getFaSmileClass()['fa-class'].' text-'.$this->getFaSmileClass()['bs-class']."'></i>";
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
     *
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

    public function qos()
    {
        return $this->belongsTo(Qos::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    /**
     * Format Contracts for edit view select field and allow for seeaching.
     *
     * @param  string|null  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function select2Contracts(?string $search): \Illuminate\Database\Eloquent\Builder
    {
        return Contract::select('id')
            ->selectRaw('CONCAT(number, \' - \', firstname, \' \', lastname) as text')
            ->when($search, function ($query, $search) {
                foreach (['number', 'firstname', 'lastname'] as $field) {
                    $query = $query->orWhere($field, 'like', "%{$search}%");
                }

                return $query;
            });
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

    public function provNetelement()
    {
        return $this->belongsTo(\Modules\HfcReq\Entities\NetElement::class, 'id', 'prov_device_id');
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
        return $this->hasMany(RadCheck::class, 'username', 'ppp_username');
    }

    public function radcheckPassword()
    {
        return $this->hasOne(RadCheck::class, 'username', 'ppp_username')
            ->where('attribute', 'Cleartext-Password');
    }

    public function radcheckPool()
    {
        return $this->hasOne(RadCheck::class, 'username', 'ppp_username')
            ->where('attribute', 'Pool-Name');
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

    public function options()
    {
        return $this->hasMany(ModemOption::class);
    }

    public function nextPassiveElement()
    {
        return $this->belongsTo(\Modules\HfcReq\Entities\NetElement::class, 'id', 'next_passive_id');
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
            // Let contract be first as just the first relation is used in modem analysis - see top.blade.php
            return collect([$this->contract, $relation]);
        }

        return $this->contract;
    }

    public function view_has_many()
    {
        $ret = [];
        $tabName = trans_choice('view.Header_Modem', 1);

        if (Module::collections()->has('ProvVoip')) {
            $this->setRelation('mtas', $this->mtas()->with('configfile')->get());
            $mtaName = $this->isTR069() ? trans('view.SipDevices') : 'MTAs';

            $ret[$tabName][$mtaName]['class'] = 'Mta';
            $ret[$tabName][$mtaName]['relation'] = $this->mtas;
        }

        $ret[$tabName]['Endpoint']['class'] = 'Endpoint';
        $ret[$tabName]['Endpoint']['relation'] = $this->endpoints;

        $ret[$tabName]['Option']['class'] = 'ModemOption';
        $ret[$tabName]['Option']['relation'] = $this->options;

        if (Module::collections()->has('ProvVoipEnvia')) {
            $ret[$tabName]['EnviaContract']['class'] = 'EnviaContract';
            $ret[$tabName]['EnviaContract']['relation'] = $this->enviacontracts;
            $ret[$tabName]['EnviaContract']['options']['hide_create_button'] = 1;
            $ret[$tabName]['EnviaContract']['options']['hide_delete_button'] = 1;

            $ret[$tabName]['EnviaOrder']['class'] = 'EnviaOrder';
            $ret[$tabName]['EnviaOrder']['relation'] = $this->_envia_orders;
            $ret[$tabName]['EnviaOrder']['options']['create_button_text'] = trans('provvoipenvia::view.enviaOrder.createButton');
            $ret[$tabName]['EnviaOrder']['options']['delete_button_text'] = trans('provvoipenvia::view.enviaOrder.deleteButton');

            // TODO: auth - loading controller from model could be a security issue ?
            $ret[$tabName]['EnviaAPI']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
            $ret[$tabName]['EnviaAPI']['view']['vars']['extra_data'] = ModemController::_get_envia_management_jobs($this);
        }

        $this->addViewHasManyTickets($ret, $tabName);

        return $ret;
    }

    public function analysisTabs()
    {
        if (
            $this->provNetelement &&
            $this->provNetelement->netelementtype->base_type_id !== array_search('NetGw', \Modules\HfcReq\Entities\NetElementType::$undeletables)
         ) {
            return $this->provNetelement->tabs();
        }

        $i18nModem = trans_choice('view.Header_Modem', 1);
        // Always show analysis tab and return error page when ProvMon is not installed/active
        $tabs = [
            ['name' => $i18nModem, 'icon' => 'pencil', 'route' => 'Modem.edit', 'link' => $this->id],
            ['name' => $i18nModem.'-'.trans('view.analysis'), 'icon' => 'area-chart', 'route' => 'Modem.analysis', 'link' => $this->id],
        ];

        if (Module::collections()->has('ProvMon')) {
            $tabs[array_key_last($tabs)]['route'] = 'ProvMon.index';
        }

        if ($this->configfile->device == 'cm') {
            $tabs[] = ['name' => 'CPE-'.trans('view.analysis'), 'icon' => 'area-chart', 'route' => 'Modem.cpeAnalysis', 'link' => $this->id];

            if (Module::collections()->has('ProvVoip') && isset($this->mtas) && isset($this->mtas[0])) {
                $mtaName = $this->isTR069() ? 'SIP' : 'MTA';
                $tabs[] = ['name' => $mtaName.'-'.trans('view.analysis'), 'icon' => 'area-chart', 'route' => 'Modem.mtaAnalysis', 'link' => $this->id];
            }
        }

        return $tabs;
    }

    /**
     * BOOT:
     * - init modem observer
     */
    public static function boot()
    {
        parent::boot();

        Log::debug(__METHOD__.' started');

        self::observe(new \App\Observers\SystemdObserver);
        self::observe(new \Modules\ProvBase\Observers\ModemObserver);
    }

    /**
     * Returns the config file entry string for a cable modem in dependency of private or public ip
     *
     * @param  object  $conf  Global conf only loaded once to speed up DHCP config building in DhcpCommand
     *
     * @author Nino Ryschawy
     *
     * @return string
     */
    private function generate_cm_dhcp_entry($conf = null)
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        // FF-00-00-00-00 to FF-FF-FF-FF-FF reserved according to RFC7042
        if (stripos($this->mac, 'ff:') === 0 || ! $this->mac) {
            return '';
        }

        $ret = 'host '.$this->hostname.' { hardware ethernet '.$this->mac.'; filename "cm/'.$this->hostname.'.cfg"; ddns-hostname "'.$this->hostname.'";';

        if (Module::collections()->has('ProvVoip') && $this->mtas->pluck('mac')->filter(function ($mac) {
            return stripos($mac, 'ff:') !== 0;
        })->count()) {
            if (! Module::collections()->has('ProvHA')) {
                $provServer = $conf ? $conf->provisioning_server : ProvBase::first()->provisioning_server;
                $ret .= " option ccc.dhcp-server-1 $provServer;";
            } else {
                $provha = $conf ?: \Modules\ProvHA\Entities\ProvHA::first();
                $master = $provha->master;
                $slave = explode(',', $provha->slaves)[0] ?: null;
                if ('master' == config('provha.hostinfo.ownState')) {
                    $ret .= " option ccc.dhcp-server-1 $master;";
                    if ($slave) {
                        $ret .= " option ccc.SecondaryDHCPServer $slave;";
                    }
                } elseif ('slave' == config('provha.hostinfo.ownState')) {
                    $ret .= " option ccc.dhcp-server-1 $slave;";
                    $ret .= " option ccc.SecondaryDHCPServer $master;";
                }
            }
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

        self::clear_dhcp_conf_files();

        $chunksize = 1000;
        $count = self::count();
        $rest = $count % $chunksize;
        $num = round($count / $chunksize) + ($rest ? 1 : 0);
        $conf = Module::collections()->has('ProvHA') ? \Modules\ProvHA\Entities\ProvHA::first() : ProvBase::first();

        echo '0/'.$count."\r";
        self::with('mtas')->chunk($chunksize, function ($modems) use ($count, $conf, $chunksize) {
            static $i = 1;
            $data = $data_pub = '';

            foreach ($modems as $modem) {
                // All
                $data .= $modem->generate_cm_dhcp_entry($conf);

                // Public ip
                if ($modem->public) {
                    $data_pub .= $modem->generate_cm_dhcp_entry_pub();
                }
            }

            echo $i * $chunksize.'/'.$count."\r";
            $i++;

            file_put_contents(self::CONF_FILE_PATH, $data, FILE_APPEND | LOCK_EX);
            file_put_contents(self::CONF_FILE_PATH_PUB, $data_pub, FILE_APPEND | LOCK_EX);
        });
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
            // self::IGNORE_CPE_FILE_PATH is already up-to-date
            if ($content == file_get_contents(self::IGNORE_CPE_FILE_PATH)) {
                return;
            }
        } else {
            // get all not deleted modems
            // attention: do not use “where('internet_access', '>', '0')” to shrink the list
            //   ⇒ MTAs shall get IPs even if internet_access is disabled!
            $modems_raw = self::whereNotNull('mac')->get();
            $modems = [];
            foreach ($modems_raw as $modem) {
                $modems[Str::lower($modem->mac)] = $modem->hostname;
            }
            ksort($modems);

            // get all configfiles with NetworkAccess enabled
            exec('grep "^[[:blank:]]*NetworkAccess[[:blank:]]*1" /tftpboot/cm/*.conf', $enabled_configs, $ret);
            if ($ret > 0) {
                Log::error('Error getting config files with NetworkAccess enabled in '.__METHOD__);
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
                    $line = "\t\t(option agent.remote-id != ".Str::lower($mac).')';
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

        self::_write_dhcp_file(self::IGNORE_CPE_FILE_PATH, $content);
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
                if ($replace && strpos($line, $replace) !== false) {
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
     * Create DHCP config file that blocks the CPEs of modems without internet access
     */
    public static function createDhcpBlockedCpesFile()
    {
        $comment = '# All Modems (remote agent IDs) without internet access';
        $modems = [];

        foreach (self::where('internet_access', 0)->whereNotNull('mac')->get() as $modem) {
            $modems[] = $modem->getDhcpBlockedCpeSublass();
        }

        File::put(self::BLOCKED_CPE_FILE_PATH, $comment."\n".implode("\n", $modems), true);
    }

    /**
     * Add/Remove modem MAC to/from DHCP blocking file - to not hand out IP addresses to CPEs behind that modem
     */
    public function blockCpeViaDhcp($unblock = false, $macChanged = false)
    {
        // Add (Block)
        if (! $unblock) {
            exec('grep -i '.$this->mac.' '.self::BLOCKED_CPE_FILE_PATH, $out, $ret);

            // not found
            if ($ret) {
                file_put_contents(self::BLOCKED_CPE_FILE_PATH, "\n".$this->getDhcpBlockedCpeSublass(), FILE_APPEND | LOCK_EX);

                Log::info("DHCP - Add modem $this->id ($this->mac) to list for blocked CPEs");
            }

            if (! $macChanged) {
                // Remove original MAC if MAC was changed
                return;
            }
        }

        // Remove (Unblock)
        $mac = $macChanged ? $this->getOriginal('mac') : $this->mac;

        exec('grep -vi '.$mac.' '.self::BLOCKED_CPE_FILE_PATH.' > '.self::BLOCKED_CPE_FILE_PATH.'.tmp');
        rename(self::BLOCKED_CPE_FILE_PATH.'.tmp', self::BLOCKED_CPE_FILE_PATH);
        chown(self::BLOCKED_CPE_FILE_PATH, 'apache');

        Log::info("DHCP - Remove modem $this->id ($mac) from list for blocked CPEs");
    }

    /**
     * Get Subclass statement for DHCP config to block all CPE's behind a modem
     *
     * @return string
     */
    private function getDhcpBlockedCpeSublass()
    {
        return 'subclass "blocked" '.$this->mac.'; # CM id: '.$this->id;
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

        $text = $this->configfile->text_make($this, 'modem');

        // don't use auto generated MaxCPE if it is explicitly set in the configfile
        // see https://stackoverflow.com/a/643136 for stripping multiline comments
        if (! Str::contains(preg_replace('!/\*.*?\*/!s', '', $text), 'MaxCPE')) {
            $conf .= "\tMaxCPE $max_cpe;\n";
        }

        if (Module::collections()->has('ProvVoip') && $internet_access && ! Str::contains($text, 'CpeMacAddress skip')) {
            foreach ($this->mtas as $mta) {
                $conf .= "\tCpeMacAddress $mta->mac;\n";
            }
        }

        $text = "Main\n{\n".$conf.$text."\n}";

        if (File::put($cf_file, $text) === false) {
            exit('Error writing to file');
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

        // touch flagfile e.g. used in ProvHA
        Storage::makeDirectory('/data/provbase');
        Storage::put('/data/provbase/configfiles_changed', null);

        return true;
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

        preg_match('/#SETTINGS:(.+)/', $this->configfile->text, $json);
        $settings = json_decode($json[1] ?? null, true);

        $events = [];
        if (isset($settings['EVENT'])) {
            foreach ($settings['EVENT'] as $value) {
                if (! $value) {
                    continue;
                }

                $events[$value] = true;
            }
        } else {
            $events['0 BOOTSTRAP'] = true;
        }

        $this->createGenieAcsProvisions($text, $events);

        $preset = [
            'weight' => 0,
            'precondition' => "DeviceID.SerialNumber = \"{$this->serial_num}\"",
            'events' => $events,
            'configurations' => [
                [
                    'type' => 'provision',
                    'name' => "prov-$this->id",
                    'args' => null,
                ],
            ],
        ];

        self::callGenieAcsApi("presets/prov-$this->id", 'PUT', json_encode($preset));

        // generate monitoring presets...
        $preset['configurations'][0]['name'] = "mon-{$this->configfile->id}";

        foreach (explode(',', config('provbase.cwmpMonitoringEvents')) as $event) {
            if (! isset(self::CWMP_EVENTS[$event])) {
                continue;
            }
            unset($preset['events']);
            $preset['events'][$event.' '.self::CWMP_EVENTS[$event]] = true;
            self::callGenieAcsApi("presets/mon-{$this->id}-{$event}", 'PUT', json_encode($preset));
        }
    }

    /**
     * Colorize Modem index table when ProvMon module is missing
     * only for first $count modems as this takes a huge amount of time
     * Use obvious code generated/fixed amount of ds_pwr
     *
     * @param  int  $count  max count of modems to check
     */
    public static function setCableModemsOnlineStatus($count = 0)
    {
        $onlineModems = [];
        $conf = ProvBase::first();
        $hf = array_flip(config('hfcreq.hfParameters'));

        $modemQuery = self::join('configfile as c', 'modem.configfile_id', 'c.id')
            ->where('c.device', 'cm');

        if ($count) {
            $modemQuery->limit($count);
        }

        foreach ($modemQuery->get() as $modem) {
            if ($modem->onlineStatus($conf)['online']) {
                $onlineModems[] = $modem->id;
            }
        }

        Log::info('Set modems online status');

        DB::beginTransaction();
        $modemQuery->update(array_merge(array_combine($hf, [0, 0, 0, 0]), ['modem.updated_at' => now()]));
        self::whereIn('id', $onlineModems)->update(array_combine($hf, [40, 36, 0, 36]));
        DB::commit();
    }

    /**
     * Create Provision from configfile.text.
     *
     * @author Roy Schneider
     *
     * @param  string  $text
     * @param  array  $events
     * @return bool
     */
    public function createGenieAcsProvisions($text, $events = [])
    {
        $prefix = '';

        // during bootstrap always clear the info we have about the device
        $prov = [];
        if (count($events) == 1 && array_key_exists('0 BOOTSTRAP', $events)) {
            $prov = [
                "clear('Device', Date.now());",
                "clear('InternetGatewayDevice', Date.now());",
            ];
        }

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
                        $alias = (empty($vals[3]) || empty($vals[4])) ? '' : ".[$vals[3]].$vals[4]";
                        $prov[] = "declare('$path$alias', {value: Date.now()} , {value: '$vals[2]'});";
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

        self::callGenieAcsApi("provisions/prov-$this->id", 'PUT', implode("\n", $prov));
    }

    /**
     * Call API of GenieACS via PHP Curl.
     *
     * @author Roy Schneider
     *
     * @param  string  $route
     * @param  string  $customRequest
     * @param  string  $data
     * @return mixed $result
     */
    public static function callGenieAcsApi($route, $customRequest, $data = null, $header = [])
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => "http://localhost:7557/$route",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => $customRequest,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $header,
        ]);

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // GenieACS general error
        if ($status == 202) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get decoded json object of device from GenieACS via API.
     *
     * @param  string  $projection
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
     * Get all GenieACS tasks for this device.
     *
     * @author Roy Schneider
     *
     * @return string
     */
    public function getGenieAcsTasks()
    {
        $genieId = rawurlencode($this->getGenieAcsModel('_id'));

        return self::callGenieAcsApi("tasks?query={\"device\":\"$genieId\"}", 'GET');
    }

    /**
     * Delete GenieACS presets.
     *
     * @author Roy Schneider
     */
    public function deleteGenieAcsPreset()
    {
        self::callGenieAcsApi("presets/prov-$this->id", 'DELETE');

        foreach (array_keys(self::CWMP_EVENTS) as $event) {
            self::callGenieAcsApi("presets/mon-{$this->id}-{$event}", 'DELETE');
        }
    }

    /**
     * Delete GenieACS provision.
     *
     * @author Roy Schneider
     */
    public function deleteGenieAcsProvision()
    {
        self::callGenieAcsApi("provisions/prov-$this->id", 'DELETE');
    }

    /**
     * Delete GenieACS device.
     *
     * @author Roy Schneider
     */
    public function deleteGenieAcsDevice()
    {
        $genieId = rawurlencode($this->getGenieAcsModel('_id'));
        self::callGenieAcsApi("devices/$genieId", 'DELETE');
    }

    /**
     * Delete all GenieACS tasks for this device.
     *
     * @author Roy Schneider
     */
    public function deleteGenieAcsTasks()
    {
        foreach ((array) json_decode($this->getGenieAcsTasks(), true) as $task) {
            self::callGenieAcsApi("tasks/{$task['_id']}", 'DELETE');
        }
    }

    /**
     * Get NETGW a CM is registered on
     *
     * @param  string 	ip 		address of cm
     * @return object NETGW
     *
     * @author Nino Ryschawy
     */
    public static function get_netgw($ip)
    {
        $validator = new \App\extensions\validators\ExtendedValidator;

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
     * @return array Hierarchical object (vendor->model->sw_rev) of all used firmwares
     *
     * @author Ole Ernst
     */
    public static function get_firmware_tree()
    {
        $ret = [];

        foreach (self::whereNotNull('sw_rev')->groupBy('sw_rev')->select(DB::raw('model, sw_rev, COUNT(*) as count'))->get() as $modem) {
            $model = explode(' ', $modem->model, 2);
            $ret[reset($model)][end($model)][$modem->sw_rev] = $modem->count;
        }

        return $ret;
    }

    /**
     * Restarts modem through snmpset
     */
    public function restart_modem($mac_changed = false, $modem_reset = false, $factoryReset = false)
    {
        // Log
        Log::info(($factoryReset ? 'factoryReset' : 'restart').' modem '.$this->hostname);

        if (! $factoryReset && $this->successfulRadiusModemDisconnect()) {
            return;
        }

        if ($this->isTR069()) {
            $id = rawurlencode($this->getGenieAcsModel('_id'));
            if (! $id) {
                Session::push('tmp_error_above_form', trans('messages.modem_restart_error'));

                return;
            }

            $action = $factoryReset ? 'factoryReset' : 'reboot';

            if (json_decode(self::callGenieAcsApi("tasks?query={\"device\":\"$id\",\"name\":\"$action\"}", 'GET'))) {
                // A factoryReset/reboot of device has already been scheduled, no need to spawn another task
                return;
            }

            $timeout = config('provbase.cwmpConnectionRequestTimeout');
            $conReq = config('provbase.cwmpConnectionRequest') ? '&connection_request' : '';
            $success = self::callGenieAcsApi("devices/$id/tasks?timeout={$timeout}{$conReq}", 'POST', "{ \"name\" : \"$action\" }");
            if (! $success) {
                Session::push('tmp_error_above_form', trans('messages.modem_restart_error'));

                return;
            }

            Session::push('tmp_info_above_form', trans('messages.modem_restart_success_direct'));

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
                Session::push('tmp_warning_above_form', trans('messages.modem_restart_warning_dns'));
                throw new \Exception(trans('messages.modem_restart_warning_dns'));
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
                    'lng',
                    implode(' ', explode(':', $mac)),
                ];
            }

            snmpset($netgw->ip, $netgw->get_rw_community(), $param[0], $param[1], $param[2], 300000, 1);

            // success message
            Session::push('tmp_info_above_form', trans('messages.modem_restart_success_netgw'));
        } catch (\Exception $e) {
            Log::error("Could not delete $this->hostname from NETGW ('".$e->getMessage()."'). Let's try to restart it directly.");

            try {
                // restart modem - DOCS-CABLE-DEV-MIB::docsDevResetNow
                snmpset($fqdn, $config->rw_community, '1.3.6.1.2.1.69.1.1.3.0', 'i', '1', 300000, 1);

                // success message - make it a warning as sth is wrong when it's not already restarted by NETGW??
                Session::push('tmp_info_above_form', trans('messages.modem_restart_success_direct'));
            } catch (\Exception $e) {
                Log::error("Could not restart $this->hostname directly ('".$e->getMessage()."')");

                if (((strpos($e->getMessage(), 'php_network_getaddresses: getaddrinfo failed: Name or service not known') !== false) ||
                    (strpos($e->getMessage(), 'snmpset(): No response from') !== false)) ||
                    // this is not necessarily an error, e.g. the modem was deleted (i.e. Cisco) and user clicked on restart again
                    (strpos($e->getMessage(), 'noSuchName') !== false)) {
                    Session::push('tmp_error_above_form', trans('messages.modem_restart_error'));
                } else {
                    // Inform and log for all other exceptions
                    Session::push('tmp_error_above_form', \App\Http\Controllers\BaseViewController::translate_label('Unexpected exception').': '.$e->getMessage());
                }
            }
        }
    }

    /**
     * Disconnect PPPoE devices via Disconnect Request against NAS
     *
     * This is comparable to clear cable modem reset/delete, since only the
     * session is stopped the devices itself won't reboot, rather just reconnect
     *
     * @return true if succesfully disconnected, otherwise false
     *
     * @author Ole Ernst
     */
    private function successfulRadiusModemDisconnect(): bool
    {
        if (! $this->isPPP()) {
            return false;
        }

        $cur = $this->radacct()->latest('radacctid')->first();

        // no active PPP session or necessary fields aren't set
        if (! $cur || $cur->acctstoptime || ! $cur->nasipaddress || ! $cur->acctsessionid || ! $cur->username) {
            return false;
        }

        $netgw = NetGw::where('ip', $cur->nasipaddress)->first();

        // no NetGw of PPP session found, NetGw has no NAS assigned, NAS has no secret or Change of Authorization port not set
        if (! $netgw || ! $netgw->nas || ! $netgw->nas->secret || ! $netgw->coa_port) {
            Session::push('tmp_warning_above_form', trans('messages.modem_disconnect_radius_warning'));

            return false;
        }

        // https://tools.ietf.org/html/rfc5176#section-3
        // https://wiki.freeradius.org/protocol/Disconnect-Messages#example-disconnect-request
        $cmd = "echo 'Acct-Session-Id={$cur->acctsessionid}, User-Name={$cur->username}, NAS-IP-Address={$cur->nasipaddress}' | radclient -r1 -t1 -s {$netgw->ip}:{$netgw->coa_port} disconnect {$netgw->nas->secret}";

        exec($cmd, $out, $ret);

        if ($ret !== 0) {
            Session::push('tmp_error_above_form', implode('<br>', $out));

            return false;
        }

        Session::push('tmp_info_above_form', trans('messages.modem_disconnect_radius_success'));

        return true;
    }

    /**
     * Perform a factory reset on a TR-069 device
     *
     * @author: Ole Ernst
     */
    public function factoryReset()
    {
        // Don't perform automatic factory reset
        if (! ProvBase::first()->auto_factory_reset) {
            return;
        }

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
            $log[$color_key][$k] = Str::contains($log[$text_key][$k], $ignore) ? '' : $trans[$color_idx];
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
    public function get_preq_data()
    {
        $domain = $this->domainName ?: ProvBase::first()->domain_name;
        $file = "/usr/share/cacti/rra/$this->hostname.$domain.json";

        if (! file_exists($file) || time() - filemtime($file) > 450) {
            return ['No pre-equalization data found'];
        }

        $preq = json_decode(file_get_contents($file), true);

        if (empty($preq['energy']) || empty($preq['fft']) || empty($preq['feature'])) {
            return ['No pre-equalization data found'];
        }

        $halfband = (! empty($preq['width']) && intval($preq['width']) !== 0 ? intval($preq['width']) : 3200000) / 2000000;
        $lowestEnergyValue = floor((int) min($preq['energy']));
        $preq['axis'] = range(-$halfband, $halfband, 2 * $halfband / count($preq['fft']));
        $preq['chart'] = array_fill(
            0,
            count($preq['energy']),
            $lowestEnergyValue - 9 - (($lowestEnergyValue - 9) % 10),
        );

        return $preq;
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
        $distance = $radius * ((1 / ((2 * M_PI / 360) * 6378.137)) / 1000);

        return self::select('id', 'lng', 'lat')
            ->where('lng', '>', $this->lng - $distance / cos($this->lat * (M_PI / 180)))
            ->where('lng', '<', $this->lng + $distance / cos($this->lat * (M_PI / 180)))
            ->where('lat', '>', $this->lat - $distance)
            ->where('lat', '<', $this->lat + $distance)
            ->get()
            ->filter(fn ($modem) => distanceLatLong($this->lat, $this->lng, $modem->lat, $modem->lng) < $radius)
            ->pluck('id');
    }

    /**
     * Check if modem actually needs to be restarted. This is only the case if a
     * relevant attribute was modified.
     *
     * @return 1 if reset via Modem or original mac is needed (mac was changed)
     *           -1 for reset via NETGW (faster),
     *           0 if no restart is needed
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
     *
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
            $arr[$apartment->id] = $apartment->label();
        }

        return $arr;
    }

    /**
     * Check if modem throughput is provisioned via PPP(oE)
     *
     * @return true if PPP(oE) is used
     *              false if PPP(oE) is not used
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
     * @return true if TR069 is used
     *              false if TR069 is not used
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
        if ($count = $this->radcheck()->count() != 2) {
            if ($count) {
                $this->radcheck()->delete();
            }

            $check = new RadCheck;
            $check->username = $this->ppp_username;
            $check->attribute = 'Cleartext-Password';
            $check->op = ':=';
            $check->value = $this->ppp_password;
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
            $check = $this->radcheckPassword;
            $check->value = $this->ppp_password;
            $check->save();
            $this->make_configfile();
            $this->factoryReset();
        }

        // update existing RadCheck, if public flag was changed
        if (array_key_exists('public', $this->getDirty())) {
            $check = $this->radcheckPool;
            $check->value = $this->public ? 'CPEPub' : 'CPEPriv';
            $check->save();
            $this->restart_modem();
        }
    }

    /**
     * Synchronize radusergroups with modem table, if PPPoE is used.
     *
     * @author Ole Ernst
     */
    private function updateRadUserGroups()
    {
        if ($this->deleted_at || ! $this->isPPP() || ! $this->internet_access || ! $this->qos_id) {
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

            if (! $this->wasRecentlyCreated) {
                $this->restart_modem();
            }
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

    /**
     * Get base data for Modem analysis page
     *
     * @return array
     */
    public function getAnalysisBaseData($api = false)
    {
        $this->domainName = ProvBase::first()->domain_name;
        $mac = strtolower($this->mac);
        $eventlog = null;
        $tickets = $this->tickets;
        $genieCmds = [];

        // Configfile tab
        if ($this->isTR069()) {
            $prov = json_decode(self::callGenieAcsApi("provisions?query={\"_id\":\"prov-{$this->id}\"}", 'GET'));

            if ($prov && isset($prov[0]->script)) {
                $configfile['text'] = preg_split('/\r\n|\r|\n/', $prov[0]->script);

                preg_match_all('/^cmd;(.*)$/m', $this->configfile->text, $match);
                foreach ($match[1] as $match) {
                    $match = explode(';', trim($match));
                    if (count($match) != 3) {
                        continue;
                    }
                    $genieCmds[array_shift($match)][] = $match;
                }

                $genieCmds = array_map(function ($cmd) {
                    return json_encode([
                        'name' => 'setParameterValues',
                        'parameterValues' => $cmd,
                    ]);
                }, $genieCmds);
                $genieCmds = array_flip($genieCmds);
                $genieCmds[json_encode(['name' => 'factoryReset'])] = trans('messages.factory_reset');
            } else {
                $configfile['text'] = [];
            }

            foreach (json_decode($this->getGenieAcsTasks(), true) as $task) {
                $genieCmds["tasks/{$task['_id']}"] = trans('messages.delete_task')." {$task['name']} {$task['device']}";
            }

            foreach ($genieCmds as $cmd => $name) {
                $genieCmds[] = ['task' => $cmd, 'name' => $name];
                unset($genieCmds[$cmd]);
            }
        } else {
            $configfile = self::getConfigfileText("/tftpboot/cm/$this->hostname");
        }

        $onlineStatus = $this->onlineStatus();
        // return $ip and $online
        foreach ($onlineStatus as $name => $value) {
            $$name = $value;
        }

        if (\Request::has('offline')) {
            $online = false;
        }

        if ($online) {
            if ($modemConfigfileStatus = $this->configfileStatus()) {
                $dash['modemConfigfileStatus'] = $modemConfigfileStatus;
            }

            $eventlog = $this->get_eventlog();
        }

        // time of this function should be observed - can take a huge time as well
        $dash['modemServicesStatus'] = $this->servicesStatus($configfile);

        // Log dhcp (discover, ...), tftp (configfile or firmware)
        // NOTE: This function takes a long time if syslog file is large - 0.4 to 0.6 sec
        $search = $mac ? "$mac|" : '';
        $search .= "$this->hostname[^0-9]";
        $search .= $ip ? "|$ip " : '';
        $log = getSyslogEntries($search, '| grep -v MTA | grep -v CPE | tail -n 30  | tac');
        $lease['text'] = self::searchLease($mac ? "hardware ethernet $mac" : '');
        $lease = self::validateLease($lease, null, $online && $this->isTR069());

        $radius = $this->radiusData();

        if ($api) {
            return compact('online', 'lease', 'log', 'configfile', 'eventlog', 'dash', 'ip', 'radius');
        }

        $tabs = $this->analysisTabs();
        $pills = ['log', 'lease', 'configfile', 'eventlog'];
        $view_header = 'Modem-'.trans('view.analysis');
        $this->help = 'modem_analysis';
        $modem = $this;

        return compact('online', 'lease', 'log', 'configfile', 'eventlog', 'dash', 'ip',
            'genieCmds', 'modem', 'pills', 'tabs', 'view_header', 'tickets', 'radius');
    }

    /**
     * Determine if modem runs with/has already downloaded actual configfile
     *
     * @param object Modem
     */
    public function configfileStatus()
    {
        if (Configfile::where('id', $this->configfile_id)->first()->device == 'tr069') {
            return;
        }

        $path = '/var/log/nmsprime/tftpd-cm.log';
        $ts_cf = filemtime("/tftpboot/cm/$this->hostname.cfg");

        $hostname = escapeshellarg($this->hostname);

        $ts_dl = exec("zgrep $hostname $path | tail -1 | cut -d' ' -f1");

        if (! $ts_dl) {
            // get all but the current logfile, order them descending by file modification time
            // we assume that logrotate adds "-TIMESTAMP" to the logfiles name
            $logfiles = glob("$path-*");
            usort($logfiles, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });

            foreach ($logfiles as $path) {
                // get the latest line indicating a configfile download
                $ts_dl = exec("zgrep $hostname $path | tail -1 | cut -d' ' -f1");

                if ($ts_dl) {
                    break;
                }
            }
        }

        if (! $ts_dl) {
            // inform the user that last download was to long ago to check if the configfile is up-to-date
            return ['bsclass' => 'info', 'text' => trans('messages.modemAnalysis.missingLD')];
        }

        if ($ts_dl <= $ts_cf) {
            return ['bsclass' => 'warning', 'text' => trans('messages.modemAnalysis.cfOutdated')];
        }
    }

    /**
     * Get contents, mtime of configfile and warn if it is outdated
     *
     * @author  Ole Ernst
     *
     * @param   path    String  Path of the configfile excluding its extension
     * @return array
     */
    public static function getConfigfileText($path)
    {
        if (! is_file("$path.conf") || ! is_file("$path.cfg")) {
            return;
        }

        if (filemtime("$path.conf") > filemtime("$path.cfg")) {
            $conf['warn'] = trans('messages.configfile_outdated');
        }

        $conf['mtime'] = strftime('%c', filemtime("$path.cfg"));

        exec("cd /tmp; docsis -d $path.cfg", $conf['text']);
        $conf['text'] = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $conf['text']);

        return $conf;
    }

    /**
     * Get IP of Modem and ping it for Analysis page.
     *
     * @param   object \Modules\Provbase\Entities\Provbase - to reduce amount of DB queries when looping over all modems
     *
     * @author  Roy Schneider
     *
     * @return array
     */
    public function onlineStatus($conf = null)
    {
        $hostname = $this->hostname.'.';

        if ($this->domainName) {
            $hostname .= $this->domainName;
        } elseif ($conf) {
            $hostname .= $conf->domain_name;
        } else {
            $hostname .= ProvBase::first()->domain_name;
        }

        $ip = gethostbyname($hostname);
        $ip = ($ip == $hostname) ? null : $ip;

        if ($this->isPPP()) {
            $cur = $this->radacct()->latest('radacctid')->first();
            if ($cur && ! $cur->acctstoptime) {
                $ip = $hostname = $cur->framedipaddress;
            }

            // workaround for tr069 devices, which block ICMP requests,
            // but listen on the HTTP(s) / SSH ports
            $con = null;
            foreach ([80, 443, 22] as $port) {
                try {
                    $con = fsockopen($ip, $port, $errno, $errstr, 1);
                } catch (\Exception $e) {
                    continue;
                }

                if ($con) {
                    fclose($con);

                    return ['ip' => $ip, 'online' => true];
                }
            }
        }

        // Ping: Only check if device is online
        // takes approx 0.1 sec
        exec('sudo ping -c1 -i0 -w1 '.$hostname, $ping, $ret);

        return ['ip' => $ip, 'online' => $ret ? false : true];
    }

    /**
     * Returns the lease entry that contains the search parameter
     *
     * TODO: make a seperate class for dhcpd
     * lease stuff (search, replace, ..)
     *
     * @return array of lease entry strings
     */
    public static function searchLease(string $search): array
    {
        $ret = [];

        if (! $search) {
            return $ret;
        }

        // Quickly filter lease file - as of now a lease entry has max 27 lines - we just extract some more lines in case sth changes there and it's not adapated here
        $filename = '/var/lib/dhcpd/dhcpd.leases';
        exec('grep -A40 -B40 '.escapeshellarg($search).' '.$filename, $filteredContent);

        // start each lease with a line that begins with "lease" and end with a line that begins with "{"
        preg_match_all('/^lease(.*?)(^})/ms', implode("\n", $filteredContent), $leases);

        // fetch all lines matching $search
        foreach (array_unique($leases[0]) as $s) {
            if (strpos($s, $search)) {
                $s = str_replace('  ', '&nbsp;&nbsp;', $s);

                // push matching results
                array_push($ret, preg_replace('/\r|\n/', '<br/>', $s));
            }
        }

        if (count($ret) <= 1) {
            return $ret;
        }

        // handle multiple lease entries
        // actual strategy: if possible grep active lease, otherwise return all entries
        //                  in reverse ordered format from dhcpd.leases
        foreach ($ret as $text) {
            if (preg_match('/starts \d ([^;]+);.*;binding state active;/', $text, $match)) {
                $start[] = $match[1];
                $lease[] = $text;
            }
        }

        if (isset($start)) {
            // return the most recent active lease
            natsort($start);
            end($start);

            return [$lease[key($start)]];
        }

        return $ret;
    }

    /**
     * Proves if the last found lease is actually valid or has already expired
     */
    public static function validateLease($lease, $type = null, $onlineTr069 = false)
    {
        if ($lease['text'] && $lease['text'][0]) {
            // calculate endtime
            preg_match('/ends [0-6] (.*?);/', $lease['text'][0], $endtime);
            $et = explode(',', str_replace([':', '/', ' '], ',', $endtime[1]));
            $endtime = \Carbon\Carbon::create($et[0], $et[1], $et[2], $et[3], $et[4], $et[5], 'UTC');

            // lease calculation
            // take care changing the state - it's used under cpe analysis
            $lease['state'] = 'green';
            $lease['forecast'] = "$type has a valid lease.";
            if ($endtime < \Carbon\Carbon::now()) {
                $lease['state'] = 'red';
                $lease['forecast'] = 'Lease is out of date';
            }
        } else {
            $lease['state'] = $onlineTr069 ? 'orange' : 'red';
            $lease['forecast'] = trans('messages.modem_lease_error');
        }

        return $lease;
    }

    /**
     * Fetch realtime values via FreeRADIUS database
     *
     * @param modem: modem object
     * @return array[section][Fieldname][Values]
     *
     * @author Ole Ernst
     */
    public function radiusData()
    {
        $ret = [];

        if (! $this->isPPP()) {
            return $ret;
        }

        // Current
        $cur = $this->radacct()->latest('radacctid')->first();
        if ($cur && ! $cur->acctstoptime) {
            $ret['DT_Current Session']['Start'] = [$cur->acctstarttime];
            $ret['DT_Current Session']['Last Update'] = [$cur->acctupdatetime];
            $ret['DT_Current Session']['BRAS IP'] = [$cur->nasipaddress];
        }

        // Sessions
        $sessionItems = [
            ['acctstarttime', 'Start', null],
            ['acctstoptime', 'Stop', null],
            ['acctsessiontime', 'Duration', function ($item) {
                return \Carbon\CarbonInterval::seconds($item)->cascade()->format('%dd %Hh %Im %Ss');
            }],
            ['acctterminatecause', 'Stop Info', null],
            ['acctinputoctets', 'In', function ($item) {
                return humanFilesize($item);
            }],
            ['acctoutputoctets', 'Out', function ($item) {
                return humanFilesize($item);
            }],
            ['nasportid', 'Port', null],
            ['callingstationid', 'MAC', null],
            ['framedipaddress', 'IP', null],
        ];
        $sessions = $this->radacct()
            ->latest('radacctid')
            ->limit(10)
            ->get(array_map(function ($a) {
                return $a[0];
            }, $sessionItems));

        foreach ($sessionItems as $item) {
            $values = $sessions->pluck($item[0])->toArray();
            $ret['DT_Last Sessions'][$item[1]] = $item[2] ? array_map($item[2], $values) : $values;
        }

        // Replies
        $replyItems = [
            ['attribute', 'Attribute'],
            ['op', 'Operand'],
            ['value', 'Value'],
        ];
        $replies = $this->radusergroups()
            ->join('radgroupreply', 'radusergroup.groupname', 'radgroupreply.groupname')
            ->get(array_map(function ($a) {
                return $a[0];
            }, $replyItems));

        foreach ($replyItems as $item) {
            $ret['DT_Replies'][$item[1]] = $replies->pluck($item[0])->toArray();
        }
        // add sequence number for proper sorting
        $ret['DT_Replies'] = array_merge(['#' => array_keys(reset($ret['DT_Replies']))], $ret['DT_Replies']);

        // Authentications
        $authItems = [
            ['authdate', 'Date'],
            ['reply', 'Reply'],
        ];
        $auths = $this->radpostauth()
            ->latest('id')
            ->limit(10)
            ->get(array_map(function ($a) {
                return $a[0];
            }, $authItems));

        foreach ($authItems as $item) {
            $ret['DT_Authentications'][$item[1]] = $auths->pluck($item[0])->toArray();
        }

        return $ret;
    }

    /**
     * Fetch realtime values via GenieACS
     *
     * @param refresh: bool refresh values from device instead of using cached ones
     * @return mixed
     *
     * @author Ole Ernst
     */
    public function realtimeTR069($refresh)
    {
        $mon = $this->configfile->getMonitoringConfig();
        if (! $mon) {
            return $mon;
        }

        if ($refresh) {
            $request = ['name' => 'getParameterValues'];
            $entries = $this->configfile->getValidMonitoringEntries();
            $request['parameterNames'] = array_values(
                array_map(fn ($entry) => $entry[0], $entries['entries'])
            );

            $id = rawurlencode($this->getGenieAcsModel('_id'));
            self::callGenieAcsApi("devices/$id/tasks?connection_request", 'POST', json_encode($request));
        }

        foreach ($mon as $category => &$params) {
            $params = array_map(function ($param) {
                if (! $param = self::sanitizeParameter($param)) {
                    return [];
                }

                $value = $this->getGenieAcsModel($param[0]);

                // _lastInform, _deviceId._SerialNumber etc. are strings, not objects
                $value = is_string($value) ? $value : ($value->_value ?? '');

                if (isset($param[1]) && is_numeric($value)) {
                    $value = eval("return $value {$param[1][0]} {$param[1][1]};");
                }

                return preg_split('/\r\n|\r|\n/', $value);
            }, $params);
        }

        $file = storage_path('app/config/provbase/realtime/prepare.php');
        if (file_exists($file)) {
            require_once $file;
            if (function_exists('prepareRealtimeTR069')) {
                $mon = prepareRealtimeTR069($this, $mon);
            }
        }

        return $mon;
    }

    /**
     * Sanitize parameter from json monitoring string
     *
     * @return mixed
     *
     * @author Ole Ernst
     */
    protected static function sanitizeParameter($param)
    {
        if (is_string($param)) {
            return [$param];
        }

        // not as expected -> ignore entry
        if (! isset($param[0]) || ! is_string($param[0])) {
            return;
        }

        // array not as expected -> don't perform evaluation
        if (! isset($param[1][0]) || ! in_array($param[1][0], ['+', '-', '*', '/']) || ! isset($param[1][1]) || ! is_numeric($param[1][1])) {
            return [$param[0]];
        }

        // perform evaluation
        return $param;
    }

    /**
     * Determine modem status of internet access and telephony for analysis dashboard
     *
     * @param array     Lines of Configfile
     * @return array Color & status text
     */
    public function servicesStatus($config)
    {
        if ($this->configfile->device == 'tr069') {
            return;
        }

        if (! $config || ! isset($config['text']) || isset($config['warn'])) {
            return ['bsclass' => 'danger',
                'text' => $config['warn'] ?? trans('messages.modemAnalysis.cfError'),
                'instructions' => "docsis -e /tftpboot/cm/{$this->hostname}.conf /tftpboot/keyfile /tftpboot/cm/{$this->hostname}.cfg",
            ];
        }

        $networkAccess = preg_grep('/NetworkAccess \d/', $config['text']);
        preg_match('/NetworkAccess (\d)/', end($networkAccess), $match);
        $networkAccess = $match[1];

        // Internet and voip blocked
        if (! $networkAccess) {
            return ['bsclass' => 'warning', 'text' => trans('messages.modemAnalysis.noNetworkAccess')];
        }

        $maxCpe = preg_grep('/MaxCPE \d/', $config['text']);
        preg_match('/MaxCPE (\d+)/', end($maxCpe), $match);
        $maxCpe = $match[1];

        $cpeMacs = preg_grep('/CpeMacAddress (.*?);/', $config['text']);

        // Internet and voip allowed
        if ($maxCpe > count($cpeMacs)) {
            return ['bsclass' => 'success', 'text' => trans('messages.modemAnalysis.fullAccess')];
        }

        // Only voip allowed
        // Check if configfile contains a different CPE MTA than the MTAs have - this case is actually [2019-03-06] not valid
        $mtaMacs = $this->mtas->each(function ($mac) {
            $mac->mac = strtolower($mac->mac);
        })->pluck('mac')->all();

        foreach ($cpeMacs as $line) {
            preg_match('/CpeMacAddress (.*?);/', $line, $match);

            $cpeMac = strtolower($match[1]);

            if (! in_array($cpeMac, $mtaMacs)) {
                return ['bsclass' => 'info', 'text' => trans('messages.modemAnalysis.cpeMacMissmatch')];
            }
        }

        return ['bsclass' => 'info', 'text' => trans('messages.modemAnalysis.onlyVoip')];
    }

    /**
     * Collect the necessary data for TicketReceiver and Notifications.
     *
     * @return array
     */
    public function getTicketSummary()
    {
        if ($this->street && $this->city) {
            $navi = [
                'link' => "https://www.google.com/maps/search/{$this->street} {$this->house_number}, {$this->zip} {$this->city}",
                'icon' => 'fa-globe',
                'title' => trans('view.Button_Search'),
            ];
        }

        if ($this->lng != 0 || $this->lat != 0) {
            $navi = [
                'link' => "https://www.google.com/maps/dir/my+location/{$this->lat},{$this->lng}",
                'icon' => 'fa-location-arrow',
                'title' => trans('messages.route'),
            ];
        }

        return [
            trans('messages.Personal Contact') => [
                'text' => "{$this->company} {$this->department} {$this->salutation} {$this->academic_degree} {$this->firstname} {$this->lastname}",
                'action' =>[
                    'link' => 'tel:'.preg_replace(["/\s+/", "/\//"], '', $this->contract()->first('phone')->phone),
                    'icon' => 'fa-phone',
                ],
            ],
            trans('messages.Address') => [
                'text' => "{$this->street} {$this->house_number}||{$this->zip} {$this->city} {$this->district}",
                'action' => $navi ?? null,
            ],
            trans('messages.Signal Parameters') => [
                'text' => "US Pwr: {$this->us_pwr} | US SNR: {$this->us_snr} ||DS Pwr: {$this->ds_pwr} | DS SNR: {$this->ds_snr}",
                'action' => [
                    'link' => route('ProvMon.index', [$this->id]),
                    'icon' => 'fa-area-chart',
                    'title' => trans('view.analysis'),
                ],
            ],
        ];
    }

    /**
     * To reduce AJAX Payload, only this subset is loaded.
     *
     * @return array
     */
    public function reducedFields()
    {
        return [
            'id', 'company', 'department', 'salutation', 'academic_degree', 'firstname', 'lastname',
            'street', 'house_number', 'zip', 'city', 'district', 'us_pwr', 'us_snr', 'ds_pwr', '
            ds_snr', 'lng', 'lat',
        ];
    }
}
