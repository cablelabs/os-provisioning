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

namespace Modules\ProvVoip\Entities;

use Log;
use File;
use Session;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\ProvBase;
use Modules\ProvBase\Traits\HasConfigfile;

class Mta extends \BaseModel
{
    use HasConfigfile;

    public const TYPES = ['mta'];
    public const CONFIGFILE_PREFIX = 'mta';
    public const CONFIGFILE_DIRECTORY = '/tftpboot/mta/';
    public const CONF_FILE_PATH = '/etc/dhcp-nmsprime/mta.conf';

    // The associated SQL table for this Model
    public $table = 'mta';

    // Add your validation rules here
    public function rules()
    {
        return [
            'mac' => ['mac'],
            'modem_id' => ['required', 'exists:modem,id,deleted_at,NULL'],
            'configfile_id' => ['required', 'exists:configfile,id,deleted_at,NULL,public,yes,device,mta'],
            'type' => ['required'],
            // 'hostname' => ['required', "unique:mta,hostname,$id"],
        ];
    }

    /**
     * View Stuff
     */

    // Name of View
    public static function view_headline()
    {
        return 'MTA';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-fax"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        return ['table' => $this->table,
            'index_header' => [$this->table.'.hostname', $this->table.'.mac', $this->table.'.type', 'configfile.name'],
            'header' => $this->label(),
            'bsclass' => $this->get_bsclass(),
            'edit' => ['configfile.name' => 'assignedConfigfile'],
            'eager_loading' => ['configfile'],
        ];
    }

    public function get_bsclass()
    {
        if (! array_key_exists('configfile', $this->relations)) {
            return 'danger';
        }

        return 'info';
    }

    public function label()
    {
        return $this->hostname.($this->mac ? ' - '.$this->mac : '');
    }

    public function view_belongs_to()
    {
        return $this->modem;
    }

    public function view_has_many()
    {
        $this->setRelation('phonenumbers', $this->phonenumbers()->with('phonenumbermanagement')->get());
        $ret['Edit']['Phonenumber']['class'] = 'Phonenumber';
        $ret['Edit']['Phonenumber']['relation'] = $this->phonenumbers;

        return $ret;
    }

    public function modem()
    {
        return $this->belongsTo(\Modules\ProvBase\Entities\Modem::class, 'modem_id');
    }

    public function phonenumbers()
    {
        return $this->hasMany(Phonenumber::class);
    }

    /**
     * BOOT:
     * - init mta observer
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new \Modules\ProvVoip\Observers\MtaObserver);
        self::observe(new \App\Observers\SystemdObserver);
    }

    /**
     * Make Configfile for a single MTA
     *
     * @author Patrick Reichel
     */
    public function make_configfile()
    {
        Log::debug(__METHOD__.' started for '.$this->hostname);

        // dir; filenames
        $dir = static::CONFIGFILE_DIRECTORY;
        $conf_file = $dir.static::CONFIGFILE_PREFIX.'-'.$this->id.'.conf';
        $cfg_file = $dir.static::CONFIGFILE_PREFIX.'-'.$this->id.'.cfg';

        // load configfile for mta
        $cf = $this->configfile;

        if (! $cf) {
            Log::info('Error could not load configfile for mta '.$this->id);
            // change owner in case command was called from command line via php artisan nms:configfile that changes owner to root
            system("/bin/chown -R apache {$dir}");

            return false;
        }

        /*
         * Write and Build configfile
         * NOTE: We use docsis tool version 0.9.9 here where HASH building/adding is already implemented
         * For Versions lower than 0.9.8 we have to build it twice and use european OID
         * for pktcMtaDevProvConfigHash.0 from excentis packet cable mta mib
         */
        $text = "Main\n{\n\tMtaConfigDelimiter 1;".$cf->text_make($this, 'mta')."\n\tMtaConfigDelimiter 255;\n}";
        if (! File::put($conf_file, $text)) {
            Log::info('Error writing to file '.$conf_file);
            // change owner in case command was called from command line via php artisan nms:configfile that changes owner to root
            system("/bin/chown -R apache {$dir}");

            return false;
        }

        $dialplan = ($cf->firmware) ? "-dialplan \"/tftpboot/dialplan/{$cf->firmware}\"" : '';

        Log::info("cd /tmp; docsis -eu $dialplan -p $conf_file $cfg_file");
        // "&" to start docsis process in background improves performance but we can't reliably proof if file exists anymore
        exec("cd /tmp; docsis -eu $dialplan -p $conf_file $cfg_file >/dev/null 2>&1 &", $out);

        // TODO: Error handling missing (see Modem for more information)

        // this only is valid when we dont execute docsis in background
        // if (!file_exists($cfg_file))
        // {
        // 	Log::info('Error failed to build '.$cfg_file);
        // 	goto _failed;
        // }

        // change owner in case command was called from command line via php artisan nms:configfile that changes owner to root
        system("/bin/chown -R apache {$dir}");

        return true;
    }

    /**
     * Writes all mta entries to dhcp configfile
     */
    public static function make_dhcp_mta_all()
    {
        self::clear_dhcp_conf_file();

        $chunksize = 1000;
        $count = self::count();
        $rest = $count % $chunksize;
        $num = round($count / $chunksize) + ($rest ? 1 : 0);

        self::chunk($chunksize, function ($mtas) use ($num) {
            static $i = 1;
            $data = '';

            foreach ($mtas as $mta) {
                // FF-00-00-00-00 to FF-FF-FF-FF-FF reserved according to RFC7042
                if (stripos($mta->mac, 'ff:') === 0 || ! $mta->mac) {
                    continue;
                }

                $optionHostname = ($mta->type == 'packetcable') ? strtolower(str_replace(':', '', $mta->mac)) : $mta->id;
                $data .= 'host mta-'.$mta->id.' { hardware ethernet '.$mta->mac.'; filename "mta/mta-'.$mta->id.'.cfg"; ddns-hostname "mta-'.$mta->id.'"; option host-name "'.$optionHostname.'"; }'."\n";
            }

            $i++;
            echo "$i/$num\r";

            file_put_contents(self::CONF_FILE_PATH, $data, FILE_APPEND | LOCK_EX);
        });
    }

    /**
     * Create/Update/Delete single Entry in mta dhcpd configfile
     * See Modem@make_dhcp_cm for more explanations
     *
     * @author Nino Ryschawy
     */
    public function make_dhcp_mta($delete = false)
    {
        Log::debug(__METHOD__.' started');

        if (! file_exists(self::CONF_FILE_PATH)) {
            Log::critical('Missing DHCPD Configfile '.self::CONF_FILE_PATH);

            return;
        }

        // lock
        $fp = fopen(self::CONF_FILE_PATH, 'r+');

        if (! flock($fp, LOCK_EX)) {
            Log::error('Could not get exclusive lock for '.self::CONF_FILE_PATH);
        }

        $replace = "host $this->hostname";
        $conf = file(self::CONF_FILE_PATH);

        foreach ($conf as $key => $line) {
            if (strpos($line, "$replace {") !== false) {
                unset($conf[$key]);
                break;
            }
        }

        // Note: dont replace directly as this wouldnt add the entry for a new created mta
        // FF-00-00-00-00 to FF-FF-FF-FF-FF reserved according to RFC7042
        if (! $delete && stripos($this->mac, 'ff:') !== 0 && $this->mac) {
            $optionHostname = ($this->type == 'packetcable') ? strtolower(str_replace(':', '', $this->mac)) : $this->id;
            $conf[] = 'host mta-'.$this->id.' { hardware ethernet '.$this->mac.'; filename "mta/mta-'.$this->id.'.cfg"; ddns-hostname "mta-'.$this->id.'"; option host-name "'.$optionHostname.'"; }'."\n";
        }

        Modem::_write_dhcp_file(self::CONF_FILE_PATH, implode($conf));

        // unlock
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * Deletes the configfiles with all mta dhcp entries - used to refresh the config through artisan nms:dhcp command
     */
    public static function clear_dhcp_conf_file()
    {
        File::put(self::CONF_FILE_PATH, '');
    }

    /**
     * Restarts MTA through snmpset
     */
    public function restart()
    {
        // Log
        Log::info('restart MTA '.$this->hostname);

        // if hostname cant be resolved we dont want to have an php error
        try {
            $domain = ProvVoip::first()->mta_domain;

            if (! $domain) {
                $domain = ProvBase::first()->domain_name;
            }

            $fqdn = $this->hostname.'.'.$domain;

            // restart - PKTC-EXCENTIS-MTA-MIB::pktcMtaDevResetNow
            // NOTES: Version 2 is important!
            // 'private' is the always working default community
            snmp2_set($fqdn, 'private', '1.3.6.1.4.1.7432.1.1.1.1.0', 'i', '1', 300000, 1);
        } catch (\Exception $e) {
            try {
                // PKTC-IETF-MTA-MIB::pktcMtaDevResetNow
                snmp2_set($fqdn, 'private', '1.3.6.1.2.1.140.1.1.1.0', 'i', '1', 300000, 1);
            } catch (\Exception $e) {
                Log::error('Exception restarting MTA '.$this->id.' ('.$this->mac.'): '.$e->getMessage());

                // only ignore error with this error message (catch exception with this string)
                if (((strpos($e->getMessage(), 'php_network_getaddresses: getaddrinfo failed: Name or service not known') !== false) || (strpos($e->getMessage(), 'snmp2_set(): No response from') !== false))) {
                    Session::push('tmp_error_above_form', 'Could not restart MTA! (offline?)');
                } elseif (strpos($e->getMessage(), 'noSuchName') !== false) {
                    Session::push('tmp_error_above_form', 'Could not restart MTA – noSuchName');
                // this is not necessarily an error, e.g. the modem was deleted (i.e. Cisco) and user clicked on restart again
                } else {
                    Session::push('tmp_error_above_form', 'Unexpected exception: '.$e->getMessage());
                }
            }

            return -1;
        }
    }
}
