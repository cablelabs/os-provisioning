<?php

namespace Modules\ProvBase\Console;

use Log;
use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvVoip\Entities\Mta;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Configfile;
use Modules\ProvVoip\Entities\Phonenumber;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class importNetUserCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:importNetUser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import all provisioning data from a NetUser database';

    /**
     * Error output after import has finished
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Mapping of old ConfigFile Names to new ConfigFile IDs
     *
     * @var array
     */
    protected $configfiles;

    /**
     * QoS's of NMSPrime DB loaded only once for better performance
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $qoss;

    /**
     * Phonenumber Prefix
     *
     * @var string
     */
    protected static $prefix = '036424';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * NOTE: Check TODOs before Import!
     */
    public function fire()
    {
        // NOTE: Search for TODO(2) for Contract Filter!
        // if (!$this->confirm("IMPORTANT!!!\n\nHave following things been prepared for this import?:
        // 	(1) Created Mapping Configfile?
        // 	(2) Has Contract filter been correctly set up (in source code)?\n"))
        // 	return;

        // Pre - Testing
        if (! Configfile::count()) {
            return $this->error('no configfile entry exists to use');
        }

        $cluster_filter = $this->option('cluster') ? 'd.Mandantnr = '.$this->option('cluster') : 'TRUE';

        // TODO(2): Adapt this Contract Filter for every Import
        $area_filter = function ($query) use ($cluster_filter) {
            $query
                ->whereRaw($cluster_filter)
                ->where(function ($query) { $query
                    ->whereRaw ("cm_adr.strasse like '%Flo%m%hle%'")
                    ->orWhereRaw ("cm_adr.ort like '%/OT Flo%'");}
                );
            };

        $this->qoss = Qos::get();
        $this->_load_mappings();

        // Connect to old Database
        $db = \DB::connection('mysql-netuser');

        /**
         * Add Modems currently needed for HFC Devices (Amplifier & Nodes (VGPs & TVMs))
         */
        // self::add_netelements($db, $area_filter);

        /*
         * CONTRACT Import
         *
         * Get all Contracts that have at least one modem with an adress inside the specified area
         */
        $contracts = $db->table('Nutzer as d')
                ->selectRaw('c.*, d.Mandantnr')
                ->join('billing.vkunden as c', 'd.Kundennr', '=', 'c.Kundennr')
                ->where($area_filter)
                ->whereIn('d.Kundennr', [9, 331, 2139, 2152, 2181, 2184])
                ->groupBy('d.Kundennr')->orderBy('d.Kundennr')
                ->get();

        // progress bar
        $num = count($contracts);
        $bar = $this->output->createProgressBar($num);
        echo "\nADD Contracts\n";
        $bar->start();

        foreach ($contracts as $contract) {
            $bar->advance();
            $c = $this->add_contract($contract);

            /*
             * MODEM Import
             */
            $modems = $db->table('Nutzer as m')
                    ->select('m.*', 'c.memo_cfg as cm_conf_default', 'm.memo_cfg as cm_conf_changed', 'c.Pfad as cf_name')
                    ->leftJoin('konfig as c', 'c.konfig_id', '=', 'm.konfig_id')
                    ->where('m.Kundennr', '=', $contract->Kundennr)
                    ->where('m.sec_typ', '=', 0)->get();

            foreach ($modems as $modem) {
                $m = $this->add_modem($c, $modem, $db);

                /*
                 * MTA Import
                 */
                $mtas = $db->table('Nutzer as m')
                    ->select('m.*', 'c.memo_cfg as mta_conf_default', 'm.memo_cfg as mta_conf_changed', 'c.Pfad as cf_name')
                    ->leftJoin('konfig as c', 'c.konfig_id', '=', 'm.konfig_id')
                    ->where('m.Kundennr', '=', $contract->Kundennr)
                    ->where('m.sec_typ', '=', 2)
                    ->where('m.modem_lfd', '=', $modem->Lfd)
                    ->get();

                foreach ($mtas as $mta) {
                    $mta_n = $this->add_mta($m, $mta);

                    /*
                     * Add Phonenumbers
                     */
                    $phonenumbers = self::add_phonenumbers_from_config($mta->mta_conf_changed, $mta_n);
                }
            }
        }

        echo "\n";
        foreach ($this->errors as $msg) {
            $this->error($msg);
        }
    }

    /**
     * Load all necessary mappings from config file
     * (1) Tariff (Inet + Voip)
     * (2) Configfile
     * (3) Item-Mapping (Zusatzposten)
     */
    private function _load_mappings()
    {
        $arr = require $this->argument('filename');

        $this->configfiles = $arr['configfiles'];

        if (isset($arr['cluster'])) {
            $this->cluster = $arr['cluster'];
        }
    }

    /**
     * Extract last number from street (and encode dependent of andre schuberts encoding mechanism)
     */
    public static function split_street_housenr($string, $utf8_encode = false)
    {
        preg_match('/(\d+)(?!.*\d)/', $string, $matches);
        $matches = $matches ? $matches[0] : '';

        if (! $matches) {
            // $street = $utf8_encode ? utf8_encode($string) : $string;
            return [$string, null];
        }

        $x = strpos($string, $matches);
        $housenr = substr($string, $x);

        if (strlen($housenr) > 6) {
            $street = str_replace($matches, '', $string);
            $housenr = $matches;
        } else {
            $street = trim(substr($string, 0, $x));
        }

        // $street = $utf8_encode ? utf8_encode($street) : $street;

        return [$street, $housenr];
    }

    /**
     * Add Contract Data
     *
     * @param   old_contract        Object      Contract from old DB
     * @param   new_contracts       Array       All existing Contracts of new DB
     */
    private function add_contract($old_contract)
    {
        $c = Contract::where('number', $old_contract->Kundennr)->first();

        if ($c) {
            \Log::info("Contract $c->vertragsnummer already exists [$c->id]");

            return $c;
        }

        $c = new Contract;

        $c->number = $old_contract->Kundennr;
        $c->number2 = $old_contract->Kundennr;
        $c->number4 = $old_contract->Kundennr;
        // $c->salutation   = $old_contract->anrede;
        // $c->company      = $old_contract->firma;
        $names = explode(',', $old_contract->Name_1);
        $c->firstname = isset($names[1]) ? trim($names[1]) : '';
        $c->lastname = isset($names[0]) ? trim($names[0]) : '';

        $ret = self::split_street_housenr($old_contract->Strasse);
        $c->street = $ret[0];
        $c->house_number = $ret[1];

        $c->zip = $old_contract->Plz;
        $c->city = $old_contract->Ort;
        $c->phone = $old_contract->Vorwahl.$old_contract->Tel;
        $c->fax = $old_contract->Fax;
        $c->email = $old_contract->Email;

        // $c->birthday         = $old_contract->geburtsdatum ? : null;

        $c->network_access = 1;
        // Datumfirst & Datumlast are always null
        $c->contract_start = date('Y-m-d', strtotime('first day of this month'));
        // $c->contract_end     = $old_contract->abgeklemmt ? : null;
        // $c->create_invoice   = $old_contract->rechnung;

        // $c->costcenter_id    = $this->option('cc') ? : 0; // Dittersdorf=1, new one would be 3
        $c->cluster = $old_contract->Mandantnr;
        $c->net = $old_contract->Mandantnr;

        // set fields with null input to ''.
        // This fixes SQL import problem with null fields
        $relations = $c->relationsToArray();
        foreach ($c->toArray() as $key => $value) {
            if (array_key_exists($key, $relations)) {
                continue;
            }

            $c->{$key} = $c->{$key} ?: '';

            if (is_string($c->{$key})) {
                $c->{$key} = utf8_encode($c->{$key});
            }
        }
        $c->deleted_at = null;

        // Update or Create Entry
        $c->save();

        \Log::info("ADD CONTRACT: $c->id, $c->firstname $c->lastname, $c->street, $c->zip $c->city [$old_contract->Kundennr]");

        return $c;
    }

    /**
     * Add Modem to the new Contract
     *
     * @param   new_modems      All modems already existing in new system for this contract
     */
    private function add_modem($new_contract, $old_modem, $db_con)
    {
        // dont update new modems with old data - return modem that new mtas & phonenumbers can be assigned
        $modems_n = $new_contract->modems;

        if (! $modems_n->isEmpty() && $modems_n->contains('mac', $old_modem->MACaddress)) {
            $new_cm = $modems_n->where('mac', $old_modem->MACaddress)->first();
            Log::info("Modem already exists in new System with ID $new_cm->id!", [$new_contract->id]);

            return $new_cm;
        }

        $modem = new Modem;

        // import fields
        $modem->mac = $old_modem->MACaddress;
        $modem->number = $old_modem->Lfd;
        $modem->name = utf8_encode($old_modem->Name);

        // $modem->x = $old_modem->x / 10000000;
        // $modem->y = $old_modem->y / 10000000;

        $names = explode(',', $old_modem->Nutzer);
        $modem->firstname = isset($names[1]) ? trim($names[1]) : '';
        $modem->lastname = isset($names[0]) ? trim($names[0]) : '';

        $modem->street = $new_contract->street;
        $modem->house_number = $new_contract->house_number;
        $modem->zip = $new_contract->zip;
        $modem->city = $new_contract->city;

        $modem->contract_id = $new_contract->id;

        if ($old_modem->konfig_id > 0) {
            $modem->configfile_id = isset($this->configfiles[$old_modem->konfig_id]) && is_int($this->configfiles[$old_modem->konfig_id]) ? $this->configfiles[$old_modem->konfig_id] : 0;
        } else {
            // Try to find it out via $old_modem->cm_conf_changed
            // else
            $modem->configfile_id = 0;
        }

        // determine qos_id
        $rates = self::get_modem_data_rates($old_modem->konfig_id > 0 ? $old_modem->cm_conf_default : $old_modem->cm_conf_changed);
        $qos = $this->qoss->where('ds_rate_max_help', (int) $rates['ds'])->where('us_rate_max_help', (int) $rates['us'])->first();

        if ($qos) {
            $modem->qos_id = $qos->id;
        } else {
            // if (Qos::where('ds_rate_max_help', '=', $rates['ds'])->where('us_rate_max_help', $rates['us'])->count())
            // if ($this->qoss->where('ds_rate_max_help', (int) $rates['ds'])->where('us_rate_max_help', (int) $rates['us'])->all())
            // if (($rates['ds'] == 2048000 && $rates['us'] == 256000) || ($rates['ds'] == 8192000 && $rates['us'] == 768000))
            // d($qos, $rates, $this->qoss, $this->qoss->where('ds_rate_max_help', (int) $rates['ds'])->where('us_rate_max_help', (int) $rates['us']));

            // Create QoS and assign new
            $qos = Qos::create([
                'name' => round($rates['ds'] / 1024000, 2).':'.round($rates['us'] / 1024000, 2),
                'ds_rate_max' => $rates['ds'] / 1024000,
                'us_rate_max' => $rates['us'] / 1024000,
                'ds_rate_max_help' => $rates['ds'],
                'us_rate_max_help' => $rates['us'],
                ]);

            \Log::info("ADD QoS with ID $qos->id, Name $qos->name, ds_rate_max_help ".$rates['ds'].' us_rate_max_help '.$rates['us']);

            $modem->qos_id = $qos->id;
            // add qos to collection
            $this->qoss = $this->qoss->add($qos);
        }

        // check if assigned cpe has public ip
        // NOTE: if even 1 of the cpe's has a public IP we assign a public IP for all CPE's here
        $comps = $db_con->table('Nutzer as cpe')
                    ->select('cpe.*')
                    ->where('cpe.Kundennr', '=', $old_modem->Kundennr)
                    ->where('cpe.modem_lfd', '=', $old_modem->Lfd)
                    ->where('cpe.sec_typ', '=', 1)->get();

        // Deactivate network access when gesperrt or when no cpe's attached
        $modem->network_access = 1;
        if ($old_modem->Gesperrt_int == 'Y' || ! $comps) {
            $modem->network_access = 0;
        }

        // Determine if Device has a public IP
        $validator = new \Acme\Validators\ExtendedValidator;
        $privateIps = [0 => ['10.0.0.0', '255.0.0.0'], 1 => ['192.168.0.0', '255.255.0.0'], 2 => ['172.16.0.0', '255.224.0.0']];
        $modem->public = 1;

        foreach ($comps as $comp) {
            foreach ($privateIps as $range) {
                if ($validator->validateIpInRange(null, $comp->Ip_adr, $range)) {
                    $modem->public = 0;
                    break;
                }
            }

            if ($modem->public) {
                \Log::debug("Set public IP of $modem->hostname because of IP $comp->Ip_adr");
                break;
            }
        }

        // set fields with null input to ''.
        // This fixes SQL import problem with null fields
        $relations = $modem->relationsToArray();
        foreach ($modem->toArray() as $key => $value) {
            if (array_key_exists($key, $relations)) {
                continue;
            }

            $modem->{$key} = $modem->{$key} ?: '';
        }
        $modem->deleted_at = null;

        // suppress output of MPR refresh and cacti diagram creation on saving
        ob_start();
        $modem->save();
        ob_end_clean();

        // Logging & Output
        if ($modem->configfile_id == 0) {
            $msg = 'No Configfile could be assigned to Modem '.($modem->id)." Old ModemID: $old_modem->Lfd, konfig_id: $old_modem->konfig_id";
            $this->errors[] = $msg;
            \Log::error($msg);
        }
        if (! $modem->qos_id) {
            $msg = 'No QoS defined for datarates '.$ret['ds_rate'].' (DS) '.$ret['us_rate']." (US) - Modem-ID: $modem->id";
            $this->errors[] = $msg;
            \Log::error($msg);
        }

        \Log::info("ADD MODEM: $modem->mac, QOS-$modem->qos_id, CF-$modem->configfile_id, $modem->street, $modem->zip, $modem->city, Public: ".($modem->public ? 'yes' : 'no'));

        $new_contract->modems->add($modem);

        return $modem;
    }

    /**
     * Extract Cable Modem data rates from configfile
     *
     * @param  string   Modem Configfile
     * @return array    Datarates [DS, US]
     */
    public static function get_modem_data_rates($config)
    {
        $datarates = ['ds' => 0, 'us' => 0];

        // define possible keys and regexes to search for datarate
        // Info: it is searched for the number behind the key inside the regex's matches
        $conditions = [
            'ds' => [
                'MaxRateSustained'  => '/DsServiceFlow(.*?)}/ms',
                'MaxRateDown'       => '/ClassOfService(.*?)}/ms',
                'max_down_rate ='   => '/cos {(.*?)}/ms',
                ],
            'us' => [
                'MaxRateSustained'  => '/UsServiceFlow(.*?)}/ms',
                'MaxRateUp'         => '/ClassOfService(.*?)}/ms',
                'max_up_rate ='     => '/cos {(.*?)}/ms',
                ],
            ];

        foreach ($conditions as $direction => $subconditions) {
            foreach ($subconditions as $key => $condition) {
                preg_match_all($condition, $config, $matches);

                if (! isset($matches[0]) || ! $matches[0]) {
                    continue;
                }

                foreach ($matches[0] as $match) {
                    // dont consider special reserved telephony datarates
                    if ($key == 'MaxRateSustained') {
                        if ($direction == 'ds' && strpos($match, 'DsServiceFlowRef 101') === false && strpos($match, 'DsServiceFlowRef 5') === false) {
                            continue;
                        }

                        if ($direction == 'us' && strpos($match, 'UsServiceFlowRef 1') === false) {
                            continue;
                        }
                    }

                    $match = str_replace(["\t", ';'], '', $match);
                    preg_match("/$key [0-9.]+/", $match, $hit);

                    if (! isset($hit[0]) || ! $hit[0]) {
                        throw new Exception('Missing case on parsing modem configfile for datarates');
                    }
                    $delimiter = $key == 'max_down_rate =' || $key == 'max_up_rate =' ? '=' : ' ';

                    $pieces = explode($delimiter, $hit[0]);
                    $datarates[$direction] = trim($pieces[1]);
                    break;
                }

                if ($datarates[$direction]) {
                    break;
                }
            }
        }

        return $datarates;
    }

    /**
     * Add MTA to corresponding Modem of new System
     */
    private function add_mta($new_modem, $old_mta)
    {
        // dont update new mtas with old data - return mta that new phonenumbers can be assigned
        $mtas_n = $new_modem->mtas;

        if (! $mtas_n->isEmpty() && $mtas_n->contains('mac', $old_mta->MACaddress)) {
            $new_mta = $mtas_n->where('mac', $old_mta->MACaddress)->first();

            Log::info("MTA already exists in new System with ID $new_mta->id!", [$new_modem->id]);

            return $new_mta;
        }

        $mta = new MTA;

        $mta->modem_id = $new_modem->id;
        $mta->mac = $old_mta->MACaddress;
        $mta->configfile_id = isset($this->configfiles[$old_mta->konfig_id]) && is_int($this->configfiles[$old_mta->konfig_id]) ? $this->configfiles[$old_mta->konfig_id] : 0;
        $mta->type = 'sip';

        $mta->save();

        if (! $mta->configfile_id) {
            $msg = 'No Configfile could be assigned to MTA '.$mta->id." Old MtaID: $old_mta->Lfd";
            $this->errors[] = $msg;
            \Log::error($msg);
        }

        \Log::info('ADD MTA: '.$mta->id.', '.$mta->mac.', CF-'.$mta->configfile_id);

        return $mta;
    }

    /**
     * Extract phonenumber infos from configfile and add new entry/entries to database
     *
     * @param string    MTA-Configfile
     * @param object    New MTA
     */
    public static function add_phonenumbers_from_config($config, $mta)
    {
        $pns_n = $mta->phonenumbers;

        $types = [
            // Thomson
            'SnmpMibObject iso.3.6.1.4.1.2863' => [
                'username' => 'iso.3.6.1.4.1.2863.78.3.4.1.1.3',
                'password' => 'iso.3.6.1.4.1.2863.78.3.4.1.1.4',
                'sipdomain' => 'iso.3.6.1.4.1.2863.78.3.4.1.1.6',
            ],
            // Arris
            'SnmpMibObject iso.3.6.1.4.1.4115' => [
                'username' => 'iso.3.6.1.4.1.4115.11.1.1.1.2',
                'password' => 'iso.3.6.1.4.1.4115.11.1.1.1.5',
                // sipdomain is probably always set globally!
                'sipdomain' => 'iso.3.6.1.4.1.4115.11.1.5',
            ],
            // FritzBox
            'SnmpMibObject iso.3.6.1.4.1.872' => [
                'username' => 'iso.3.6.1.4.1.872.1.4.3.1.4',
                'password' => 'iso.3.6.1.4.1.872.1.4.3.1.5',
                'sipdomain' => 'iso.3.6.1.4.1.872.1.4.2.1.12',
            ],
            ];

        foreach ($types as $filter => $fields) {
            if (strpos($config, $filter) === false) {
                continue;
            }

            $max_numbers = $filter == 'SnmpMibObject iso.3.6.1.4.1.872' ? 10 : 2;

            for ($i = 1; $i <= $max_numbers; $i++) {
                $pn = new Phonenumber;

                foreach ($fields as $col_name => $oid) {
                    $id = ($filter == 'SnmpMibObject iso.3.6.1.4.1.4115' && $col_name == 'sipdomain') ? 0 : $i;

                    preg_match("/SnmpMibObject $oid.$id String (.*?);/", $config, $hit);

                    if (! isset($hit[0]) || ! $hit[0]) {
                        \Log::debug("Missing $col_name for possible phonenumber $i. Discard number of MTA $mta->id.");
                        break;
                    }

                    $string = trim(str_replace([';', '"'], '', substr($hit[0], strpos($hit[0], 'String') + 6)));

                    if ($col_name == 'username') {
                        // check if phonenumber already exists
                        if (! $pns_n->isEmpty() && $pns_n->contains('username', $string)) {
                            $new_pn = $pns_n->where('username', $string)->first();
                            Log::info("Phonenumber already exists in new System with ID $new_pn->id!", [$mta->id]);
                            break;
                        }

                        $pn->number = str_replace(self::$prefix, '', $string);
                    }

                    $pn->{$col_name} = $string;
                }

                // Discard numbers with less than 2 of 4 informations
                if ((! $pn->number || ! $pn->username) && ! $pn->password && ! $pn->sipdomain) {
                    if ($pn->number || $pn->username) {
                        \Log::warning("Ignore phoneumber $pn->number (number), $pn->username (username) as password and sipdomain are empty");
                    }
                    continue;
                }

                $pn->mta_id = $mta->id;
                $pn->port = $i;
                $pn->country_code = '0049';
                $pn->prefix_number = self::$prefix;

                $pn->active = true;
                if ($filter != 'SnmpMibObject iso.3.6.1.4.1.872') {
                    // ifAdminStatus 2: down, 1: up (only for Thomson and Arris)
                    // 'iso.3.6.1.2.1.2.2.1.7.9 Integer 1'
                    // 'iso.3.6.1.2.1.2.2.1.7.10 Integer 2'

                    $k = $i + 8;
                    $match = [];
                    preg_match("/SnmpMibObject iso.3.6.1.2.1.2.2.1.7.$k Integer \d;/", $config, $match);

                    // if (! isset($match[0]) || ! $match[0]) {
                    // } else {
                    if ($match) {
                        // check integer value
                        preg_match("/\d/", substr($match[0], strpos($match[0], 'Integer') + 5), $match);
                        $pn->active = $match[0] == 1 ? true : false;
                    }
                }

                $pn->save();
                Log::info('ADD Phonenumber: '.$pn->id.', '.$mta->id.', '.$pn->username.', '.($pn->active ? 'active' : 'inactive (but currently set fix to active)'));

                foreach (['username', 'password', 'sipdomain'] as $property) {
                    if (! $pn->{$property}) {
                        \Log::warning("Missing $property in phonenumber $pn->id (ID), $pn->username (username)");
                    }
                }
            }
        }
    }

    /**
     * Add Modems of Netelements to Erznet Contract as this is still necessary to get them online in new system
     */
    private function add_netelements($db_con, $area_filter)
    {
        $devices = $db_con->table('Nutzer as d')
                    ->select('d.*', 'c.memo_cfg as cm_conf_default', 'd.memo_cfg as cm_conf_changed', 'c.Pfad as cf_name')
                    ->join('konfig as c', 'c.konfig_id', '=', 'd.konfig_id')
                    ->where('d.sec_typ', '=', 0)
                    ->where($area_filter)
                    ->get();

        if (! $devices) {
            return;
        }

        Log::info('ADD NETELEMENT Modems');
        echo "Add NETELEMENT Modems\n";

        $contract = Contract::findOrFail(500000);

        $bar = $this->output->createProgressBar(count($devices));
        $bar->start();

        foreach ($devices as $device) {
            $bar->advance();
            self::add_modem($contract, $device, $db_con);
        }

        $bar->finish();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['filename', InputArgument::REQUIRED, 'Name of Mapping Configfile in Storage directory'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['cluster', null, InputOption::VALUE_OPTIONAL, 'Import only Contracts/Modems from Mandantnr, e.g. 3', 0],
            // array('cc', null, InputOption::VALUE_OPTIONAL, 'CostCenter ID for all the imported Contracts', 0),
        ];
    }
}
