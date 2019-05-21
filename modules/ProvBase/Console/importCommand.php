<?php

namespace Modules\provbase\Console;

use Log;
use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvVoip\Entities\Mta;
use Modules\NmsMail\Entities\Email;
use Modules\ProvBase\Entities\Modem;
use Modules\BillingBase\Entities\Item;
use Modules\ProvBase\Entities\Contract;
use Modules\BillingBase\Entities\Product;
use Modules\ProvBase\Entities\Configfile;
use Modules\ProvVoip\Entities\Phonenumber;
use Modules\BillingBase\Entities\SepaMandate;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class importCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import km3';

    /**
     * Contract ID for NetElement Modems
     *
     * @var int
     */
    protected static $ne_contract_id = 1;

    /**
     * Old Prefix of contract numbers
     *
     * @var string
     */
    protected static $contract_nr_prefix = '002-';

    /**
     * Set to true if customers that had volume tariffs shall get a credit
     * NOTE: Please specify product ID then
     *
     * @var bool
     * @var int
     */
    protected static $credit = false;
    protected static $credit_id = 0;

    /**
     * Errors that will be written to stdout when command finishes
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Mapping of old Internet Tarif Names to new Tarif IDs
     *
     * @var array
     */
    protected $old_sys_inet_tariffs;

    /**
     * Mapping of old Voip Tarif IDs to new Voip Tarif IDs
     *
     * @var array
     */
    protected $old_sys_voip_tariffs;

    /**
     * Mapping of old Qos-Group ID to new QoS ID
     *
     * only in case Billing is not used
     *
     * @var array
     */
    protected $groupsToQos;

    /**
     * Mapping of old ConfigFile Names to new ConfigFile IDs
     *
     * @var array
     */
    protected $configfiles;

    /**
     * Mapping of old Cluster ID to new Cluster ID
     *
     * @var array
     */
    protected $cluster = [];

    /**
     * Mapping of old additional Item IDs to new additional Item IDs
     *
     * @var array
     */
    protected $add_items;

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
     * NOTE:
     * All Logging with level higher than 'info' are written to laravel log too
     * Check TODO(<nrs>) before Import!
     * (* set cluster mapping table)
     */
    public function fire()
    {
        // NOTE: Search by TODO(2) for Contract Filter and TODO(3) to change restrictions for adding credits!
        if (! $this->confirm("IMPORTANT!!!\n\nHave following things been prepared for this import?:
			(1) Created Mapping Configfile?
			(2) Has Contract filter been correctly set up (in source code)?
			(3) Shall volume tariffs get Credits (in source code)?\n")) {
            return;
        }

        // Pre - Testing
        if (! Qos::count()) {
            return $this->error('no QOS entry exists to use');
        }

        if (! Configfile::count()) {
            return $this->error('no configfile entry exists to use');
        }

        if (! Product::count()) {
            return $this->error('no product entry exists to use');
        }

        $cluster_filter = $this->option('cluster') ? 'm.cluster_id = '.$this->option('cluster') : 'TRUE';
        $plz_filter = $this->option('plz') ? 'cm_adr.plz = \''.$this->option('plz')."'" : 'TRUE';

        // TODO(2): Adapt this Contract Filter for every Import
        $area_filter = function ($query) use ($cluster_filter) {
            $query
                ->whereRaw($cluster_filter)
                // ->whereNotBetween('v.vertragsnummer', [30000, 31000])
                // ->whereNotIn('v.vertragsnummer', [43206,43214,43215,43217,43218,43219,43220,43223,43233,43346,43419,43441,44029])
                // ->orWhereIn('m.cluster_id', [36546, 36821])
                // ->where(function ($query) { $query
                // 	->whereRaw ("cm_adr.strasse like '%Flo%m%hle%'")
                // 	->orWhereRaw ("cm_adr.strasse like 'Fl%talstr%'")
                // 	->orWhereRaw ("cm_adr.ort like '%/OT Flo%'");}
                // 	)
;
        };

        $this->_load_mappings();

        // Connect to old Database
        $km3 = \DB::connection('pgsql-km3');

        // Get all important Data from new DB
        $products_new = Product::all();

        /*
         * Add Modems currently needed for HFC Devices (Amplifier & Nodes (VGPs & TVMs))
         */
        self::add_netelements($km3, $area_filter);

        /*
         * CONTRACT Import
         *
         * Get all Contracts that have at least one modem with an adress inside the specified area
         * with: Get customer data & Tarifname from old systems DB
         */
        $contracts = $km3->table('tbl_vertrag as v')
                ->selectRaw('distinct on (v.vertragsnummer) v.vertragsnummer, v.*, k.*, kadr.*, t.name as tariffname,
					v.id as id, v.beschreibung as contr_descr, m.cluster_id,
					a.vorname as v_vorname, a.nachname as v_nachname, a.strasse as v_strasse, a.plz as v_plz,
					a.ort as v_ort, a.firma as v_firma, a.tel as v_tel, a.anrede as v_anrede, a.email as v_email')
                ->join('tbl_modem as m', 'm.vertrag', '=', 'v.id')
                ->join('tbl_kunde as k', 'v.kunde', '=', 'k.id')
                ->join('tbl_adressen as a', 'v.ansprechpartner', '=', 'a.id')
                ->join('tbl_adressen as kadr', 'k.rechnungsanschrift', '=', 'kadr.id')
                ->join('tbl_adressen as cm_adr', 'm.adresse', '=', 'cm_adr.id')
                ->leftJoin('tbl_tarif as t', 'v.tarif', '=', 't.id')
                ->leftJoin('tbl_posten as p', 't.posten_volumen_extern', '=', 'p.id')
                ->where('v.deleted', '=', false)
                ->where('m.deleted', '=', false)
                ->whereRaw('(v.abgeklemmt is null or v.abgeklemmt >= CURRENT_DATE)') 		// dont import out-of-date contracts
                ->where($area_filter)
                ->orderBy('v.vertragsnummer')
                // ->toSql();
                ->get();

        // progress bar
        echo "\nADD Contracts\n";
        $bar = $this->output->createProgressBar(count($contracts));
        $bar->start();

        foreach ($contracts as $contract) {
            $bar->advance();
            $c = $this->add_contract($contract);

            /*
             * MODEM Import
             */
            $modems = $km3->table(\DB::raw('tbl_modem m, tbl_adressen a, tbl_configfiles c'))
                    ->selectRaw('m.*, a.*, m.id as id, c.name as cf_name')
                    ->where('m.vertrag', '=', $contract->id)
                    ->whereRaw('m.adresse = a.id')
                    ->whereRaw('m.configfile = c.id')
                    ->where('m.deleted', '=', 'false')->get();

            foreach ($modems as $modem) {
                $m = $this->add_modem($c, $modem, $km3);

                if ($c->relationLoaded('modems')) {
                    $c->modems->add($m);
                } else {
                    $c->setRelation('modems', $m);
                }

                /*
                 * MTA Import
                 */
                $mtas = $km3->table('tbl_computer as c')
                    ->join('tbl_packetcablemtas as mta', 'mta.computer', '=', 'c.id')
                    ->select(['c.*', 'mta.*', 'mta.id as id'])
                    ->where('c.modem', '=', $modem->id)
                    ->where('c.deleted', '=', 'false')
                    ->where('mta.deleted', '=', 'false')
                    ->get();

                foreach ($mtas as $mta) {
                    $mta_n = $this->add_mta($m, $mta, $km3);
                    $c->has_mta = true;

                    /*
                     * Phonenumber Import
                     */
                    $phonenumbers = $km3->table('tbl_mtaendpoints as e')
                        ->where('e.mta', '=', $mta->id)->where('e.deleted', '=', 'false')
                        ->distinct()->get();

                    foreach ($phonenumbers as $phonenumber) {
                        $p = $this->add_phonenumber($mta_n, $phonenumber, $km3);
                    }
                }
            }

            // Email Import
            if (\Module::collections()->has('Mail')) {
                self::add_email($c, $contract);
            }

            // Add Billing related Data
            if (\Module::collections()->has('BillingBase')) {
                $this->add_tariffs($c, $products_new, $contract);
                $this->add_tariff_credit($c, $contract);
                $this->add_sepamandate($c, $contract, $km3);
                $this->add_additional_items($c, $km3, $contract);
            }

            // disable network access where blockcpe is set
            if ($contract->blockcpe) {
                self::_blockcpe($c);
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

        $mappings = ['old_sys_inet_tariffs', 'old_sys_voip_tariffs', 'groupsToQos', 'configfiles', 'add_items', 'cluster'];

        foreach ($mappings as $key) {
            if (isset($arr[$key])) {
                $this->{$key} = $arr[$key];
            }
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
            $street = $utf8_encode ? utf8_encode($string) : $string;

            return [$street, null];
        }

        $x = strpos($string, $matches);
        $housenr = substr($string, $x);

        if (strlen($housenr) > 6) {
            $street = str_replace($matches, '', $string);
            $housenr = $matches;
        } else {
            $street = trim(substr($string, 0, $x));
        }

        $street = $utf8_encode ? utf8_encode($street) : $street;
        // $street = mb_convert_encoding(trim(substr($string, 0, $x)), 'iso-8859-1', 'ascii');
        // var_dump(mb_detect_encoding ($street), $street);

        return [$street, $housenr];
    }

    /**
     * Add Contract Data
     *
     * @param 	old_contract 		Object 		Contract from old DB
     * @param 	new_contracts 		Array 		All existing Contracts of new DB
     */
    private function add_contract($old_contract)
    {
        $c = Contract::where('number', $old_contract->vertragsnummer)->first();

        if ($c) {
            \Log::info("Contract $c->number already exists [$c->id]");

            return $c;
        }

        $c = new Contract;

        // Compare Customer and Contract Name, Surname, Address and print warning if they differ
        $desc = '';
        $c_datafields = ['vorname', 'nachname', 'strasse', 'plz', 'ort', 'firma', 'anrede', 'email'];

        foreach ($c_datafields as $field) {
            if ($old_contract->{$field} != $old_contract->{'v_'.$field}) {
                $desc .= ucwords($field).': '.$old_contract->{'v_'.$field}.PHP_EOL;
            }
        }

        $c->description = $old_contract->beschreibung.PHP_EOL.$old_contract->contr_descr.PHP_EOL;

        if ($desc) {
            $c->description .= 'Alte Vertragsdaten:'.PHP_EOL.$desc;
            Log::warning("Contract address differs from customer address for contract $old_contract->vertragsnummer");
        }

        // import all other fields
        $c->number = $old_contract->vertragsnummer;
        $c->number2 = self::$contract_nr_prefix.$old_contract->vertragsnummer;
        $c->number4 = self::$contract_nr_prefix.$old_contract->kundennr;
        $c->salutation = $old_contract->anrede;
        $c->company = $old_contract->firma;
        $c->firstname = $old_contract->vorname;
        $c->lastname = $old_contract->nachname;

        $ret = self::split_street_housenr($old_contract->strasse);
        $c->street = $ret[0];
        $c->house_number = $ret[1];

        $c->zip = $old_contract->plz;
        $c->city = $old_contract->ort;
        $c->phone = str_replace('/', '', $old_contract->tel);
        $c->fax = $old_contract->fax;
        $c->email = $old_contract->email;
        $c->birthday = $old_contract->geburtsdatum ?: null;

        $c->internet_access = $old_contract->network_access;
        $c->contract_start = $old_contract->angeschlossen;
        $c->contract_end = $old_contract->abgeklemmt ?: null;
        $c->create_invoice = $old_contract->rechnung;

        if (\Module::collections()->has('BillingBase')) {
            $c->costcenter_id = $this->option('cc');
        }
        $c->cluster = $this->map_cluster_id($old_contract->cluster_id);
        $c->net = $this->map_cluster_id($old_contract->cluster_id, 1);

        // Set qos_id if it won't be set via tariffs (items) because billing module is disabled
        if (! \Module::collections()->has('BillingBase')) {
            if (isset($this->groupsToQos[$old_contract->qosgroup])) {
                $c->qos_id = $this->groupsToQos[$old_contract->qosgroup];
            } else {
                $msg = "Mapping für QoS-Profil $old_contract->qosgroup fehlt. QoS konnte in Vertrag $c->number nicht gesetzt werden!";
                \Log::error($msg);
                $this->errors[] = $msg;
            }
        }

        // set fields with null input to ''.
        // This fixes SQL import problem with null fields
        $relations = $c->relationsToArray();
        $nullable = ['contract_end'];
        foreach ($c->toArray() as $key => $value) {
            if (array_key_exists($key, $relations) || in_array($key, $nullable)) {
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

        \Log::info("ADD CONTRACT: $c->id, $c->firstname $c->lastname, $c->street, $c->zip $c->city [$old_contract->vertragsnummer]");

        return $c;
    }

    /**
     * Return ID of Cluster/Net for new System from old systems cluster/net ID
     *
     * @param 	cluster_id 		Integer
     * @parma 	$net 			0/1 		Switch: 0 - return cluster id, 1 - return net id
     *
     * @return 	int
     */
    private function map_cluster_id($cluster_id, $net = 0)
    {
        if (isset($this->cluster[$cluster_id][$net])) {
            return $this->cluster[$cluster_id][$net];
        }

        return 0;
    }

    /**
     * Add Tarifs to corresponding Contract of new System
     *
     * TODO: Tarif next month can not be set as is - has still ID - Separate inet & voip tariff mappings and map all by id
     */
    private function add_tariffs($new_contract, $products_new, $old_contract)
    {
        $tariffs = [
            'tarif' 			=> $old_contract->tarif,
            'tarif_next_month'  => $old_contract->tarif_next_month,
            'tarif_next'        => $old_contract->tarif_next,
            'telefontarif'      => $old_contract->telefontarif,
            'telefontarif_next_month' => $old_contract->telefontarif_next_month,
            'telefontarif_next' => $old_contract->telefontarif_next,
            ];

        $items_new = $new_contract->items;

        foreach ($tariffs as $key => $tariff) {
            $prod_id = -1;

            if (! $tariff) {
                \Log::debug("\tNo $key Item exists in old System");
                continue;
            }

            if (\Str::startsWith($key, 'telefontarif')) {
                // Discard voip tariff if new contract doesnt have MTA
                if (! isset($new_contract->has_mta)) {
                    Log::notice('Discard voip tariff as contract has no MTA assigned', [$new_contract->number]);

                    continue;
                }

                if (array_key_exists($tariff, $this->old_sys_voip_tariffs)) {
                    $prod_id = $this->old_sys_voip_tariffs[$tariff];
                }
            } else {
                if (array_key_exists($tariff, $this->old_sys_inet_tariffs)) {
                    $prod_id = $this->old_sys_inet_tariffs[$tariff];
                }
            }

            if ($prod_id == -1) {
                $type = \Str::startsWith($key, 'telefontarif') ? 'voip' : 'internet';
                $msg = "Missing mapping for $type tariff $tariff (ID in km3 DB). Don't add voip item to contract $new_contract->number.";
                \Log::error($msg);
                $this->errors[] = $msg;

                continue;
            }

            $item_n = $items_new->where('product_id', $prod_id)->all();

            if ($item_n) {
                \Log::info("\tItem $key for Contract ".$new_contract->number.' already exists');
                continue;
            }

            $valid_from = $old_contract->angeschlossen;
            if (strpos($key, 'tarif_next_month') !== false) {
                $valid_from = date('Y-m-01', strtotime('first day of next month'));
            } elseif (strpos($key, 'tarif_next') !== false) {
                $valid_from = date('Y-m-d', strtotime($old_contract->{$key.'_date'}));
            }

            Item::create([
                'contract_id' 		=> $new_contract->id,
                'product_id' 		=> $prod_id,
                'valid_from' 		=> $valid_from,
                'valid_from_fixed' 	=> 1,
                'valid_to' 			=> $old_contract->abgeklemmt,
                'valid_to_fixed' 	=> 1,
                ]);

            \Log::info("ITEM ADD $key: ".$products_new->find($prod_id)->name.' ('.$prod_id.')');
        }
    }

    /**
     * Add extra credit item (5 Euro gross - 1 Year) if customer had an old volume tariff
     */
    private function add_tariff_credit($new_contract, $old_contract)
    {
        if (! self::$credit) {
            return;
        }

        // TODO(3) check restrictions of volume tariffs!
        if ((strpos($old_contract->tariffname, 'Volumen') === false) && (strpos($old_contract->tariffname, 'Speed') === false) && (strpos($old_contract->tariffname, 'Basic') === false)) {
            return;
        }

        \Log::info("Add extra Credit as Customer had volume tariff. [$new_contract->number]");

        Item::create([
            'contract_id' 		=> $new_contract->id,
            'product_id' 		=> self::$credit_id,
            'valid_from' 		=> date('Y-m-01', strtotime('first day of next month')),
            'valid_from_fixed' 	=> 1,
            'valid_to' 			=> date('Y-m-d', strtotime('last day of next year')),
            'valid_to_fixed' 	=> 1,
            'credit_amount' 	=> 4.2017,
            ]);
    }

    /**
     * Add SepaMandate to corresponding Contract
     */
    private function add_sepamandate($new_contract, $old_contract, $db_con)
    {
        $mandates_n = $new_contract->sepamandates;

        if (! $mandates_n->isEmpty()) {
            \Log::info("\tCustomer $new_contract->id already has SepaMandate assigned");

            return;
        }

        $mandates_old = $db_con->table('tbl_sepamandate as s')
                ->join('tbl_lastschriftkonten as l', 's.id', '=', 'l.sepamandat')
                ->select('s.*', 'l.*', 'l.id as id')
                ->where('s.kunde', '=', $old_contract->kunde)
                ->where('s.deleted', '=', 'false')
                ->where('l.deleted', '=', 'false')
                ->orderBy('l.id')
                ->get();

        if (! $mandates_old) {
            \Log::info("\tCustomer $new_contract->id has no SepaMandate in old DB");
        }

        foreach ($mandates_old as $mandate) {
            SepaMandate::create([
                'contract_id' 		=> $new_contract->id,
                'reference' 		=> $new_contract->number ?: '', 			// TODO: number circle ?
                'signature_date' 	=> $mandate->datum ?: '',
                'holder' 		=> $mandate->kontoinhaber ? utf8_encode($mandate->kontoinhaber) : '',
                'iban'			=> $mandate->iban ?: '',
                'bic' 			=> $mandate->bic ?: '',
                'institute' 	=> $mandate->institut ?: '',
                'valid_from' 	=> $mandate->gueltig_ab,
                'valid_to' 	=> $mandate->gueltig_bis,
                'disable' 			=> $old_contract->einzug ? false : true,
                'state' 			=> 'RCUR',
                ]);

            \Log::info('SEPAMANDATE ADD: '.utf8_encode($mandate->kontoinhaber).', '.$mandate->iban.', '.$mandate->institut.', '.$mandate->datum);
        }
    }

    /**
     * Add all relevant additional Items - see mapping table below which are relavant
     */
    private function add_additional_items($new_contract, $db_con, $old_contract)
    {
        // Additional Items
        $items = $db_con->table('tbl_zusatzposten as z')
                ->join('tbl_posten as p', 'z.posten', '=', 'p.id')
                ->select(['p.id', 'p.artikel', 'z.von', 'z.bis', 'z.menge', 'z.buchungstext', 'z.preis', 'z.abrechnen', 'z.abgerechnet'])
                ->where('z.vertrag', '=', $old_contract->id)
                ->where('z.closed', '=', 'false')
                ->where(function ($query) {
                    $query
                    ->where('z.bis', '>', date('Y-m-d'))
                    ->orWhere('z.bis', '=', null);
                })
                ->get();

        $items_new = $new_contract->items;

        foreach ($items as $item) {
            $prod_id = isset($this->add_items[$item->id]) ? $this->add_items[$item->id] : null;

            if (! $prod_id) {
                \Log::notice("\tCan not map Artikel \"$item->artikel\" - ID $item->id does not exist in internal mapping table");
                continue;
            }

            if ($item->id == 1 && ! $item->preis) {
                continue;
            }

            // Check if item already exists
            if ($items_new->contains('product_id', $prod_id)) {
                \Log::warning("Additional item with product id $prod_id already exists for Contract ".$new_contract->number.'! (Added again)');
            }

            \Log::info("\tAdd Item [$new_contract->number]: $item->artikel (from: $item->von, to: $item->bis, price: $item->preis) [Old ID: $item->id]");

            $valid_to = $item->bis;
            if (! $item->von) {
                $months = $item->abrechnen - $item->abgerechnet;
                $valid_to = date('Y-m-d', strtotime("last day of +$months month"));
            }

            Item::create([
                'contract_id' 		=> $new_contract->id,
                'product_id' 		=> $this->add_items[$item->id],
                'count' 			=> $item->menge,
                'valid_from' 		=> $item->von ?: date('Y-m-d'),
                'valid_from_fixed' 	=> 1,
                'valid_to' 			=> $valid_to,
                'valid_to_fixed' 	=> 1,
                'credit_amount' 	=> (-1) * $item->preis,
                'accounting_text' 	=> is_null($item->buchungstext) ? '' : utf8_encode($item->buchungstext),
            ]);
        }
    }

    /**
     * Add Emails to corresponding Contract
     */
    private function add_email($new_contract, $old_contract)
    {
        $emails = $km3->table('tbl_email')->selectRaw('*')->where('vertrag', '=', $old_contract->id)->get();
        $emails_new_cnt = \Module::collections()->has('Mail') ? $new_contract->emails()->count() : [];

        if (count($emails) == $emails_new_cnt) {
            return Log::info('Email Aliases already added!', [$new_contract->number]);
        }

        foreach ($emails as $email) {
            Email::create([
                'contract_id' 	=> $new_contract->id,
                'localpart' 	=> $email->alias,
                'password' 		=> $email->passwort,
                'blacklisting' 	=> $email->blacklisting,
                'greylisting' 	=> $email->greylisting,
                'forwardto' 	=> $email->forwardto ?: '',
                ]);
        }

        // Log
        \Log::info('MAIL: ADDED '.count($emails).' Addresses');
    }

    /**
     * Add Modem to the new Contract
     *
     * @param 	new_modems 		All modems already existing in new system for this contract
     */
    private function add_modem($new_contract, $old_modem, $db_con)
    {
        // dont update new modems with old data - return modem that new mtas & phonenumbers can be assigned
        $modems_n = $new_contract->modems;

        if (! $modems_n->isEmpty() && $modems_n->contains('mac', $old_modem->mac_adresse)) {
            $new_cm = $modems_n->where('mac', $old_modem->mac_adresse)->first();

            Log::info("Modem already exists in new System with ID $new_cm->id!", [$new_contract->id]);

            return $new_cm;
        }

        $modem = new Modem;

        // import fields
        $modem->mac = $old_modem->mac_adresse;
        $modem->number = $old_modem->id;
        $modem->name = utf8_encode($old_modem->name);

        $modem->serial_num = $old_modem->serial_num;
        $modem->inventar_num = $old_modem->inventar_num;
        $modem->description = $old_modem->beschreibung;
        $modem->internet_access = $old_modem->network_access;

        $modem->x = $old_modem->x / 10000000;
        $modem->y = $old_modem->y / 10000000;

        $modem->firstname = $new_contract->firstname;
        $modem->lastname = $new_contract->lastname;

        if ($old_modem->strasse) {
            $ret = self::split_street_housenr($old_modem->strasse, true);

            $modem->street = $ret[0];
            $modem->house_number = $ret[1];
        } else {
            $modem->street = $new_contract->street;
            $modem->house_number = $new_contract->house_number;
        }

        $modem->zip = $new_contract->zip;
        $modem->city = $new_contract->city;
        $modem->qos_id = $new_contract->qos_id;
        $modem->contract_id = $new_contract->id;

        if (isset($this->configfiles[$old_modem->configfile])) {
            $modem->configfile_id = $this->configfiles[$old_modem->configfile];
        } else {
            $msg = "Missing mapping for configfile $old_modem->configfile (ID in km3 DB). Modem '$modem->mac' of contract $new_contract->number will not have a configfile assigned.";
            \Log::error($msg);
            $this->errors[] = $msg;
        }

        // check if assigned cpe has public ip (starts with 7 or 8)
        // NOTE: if even 1 of the cpe's has a public IP we assign a public IP for all CPE's here
        $comps = $db_con->table('tbl_computer')->select('ip')->where('modem', '=', $old_modem->id)->get();

        // Determine if Device has a public IP
        $validator = new \Acme\Validators\ExtendedValidator;
        $privateIps = [['10.0.0.0', '255.0.0.0'], ['192.168.0.0', '255.255.0.0'], ['172.16.0.0', '255.224.0.0'], ['100.64.0.0', '255.192.0.0']];
        $modem->public = 1;

        foreach ($comps as $comp) {
            foreach ($privateIps as $range) {
                if ($validator->validateIpInRange(null, $comp->ip, $range)) {
                    $modem->public = 0;
                    break;
                }
            }

            if ($modem->public) {
                \Log::debug("Set public IP for $modem->hostname because of IP $comp->ip");
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

        // Output

        \Log::info("ADD MODEM: $modem->mac, QOS-$modem->qos_id, CF-$modem->configfile_id, $modem->street, $modem->zip, $modem->city, Public: ".($modem->public ? 'yes' : 'no'));

        $new_contract->modems->add($modem);

        return $modem;
    }

    /**
     * Add MTA to corresponding Modem of new System
     */
    private function add_mta($new_modem, $old_mta, $db_con)
    {
        // dont update new mtas with old data - return mta that new phonenumbers can be assigned
        $mtas_n = $new_modem->mtas;

        if (! $mtas_n->isEmpty() && $mtas_n->contains('mac', $old_mta->mac_adresse)) {
            $new_mta = $mtas_n->where('mac', $old_mta->mac_adresse)->first();

            Log::info("MTA already exists in new System with ID $new_mta->id!", [$new_modem->id]);

            return $new_mta;
        }

        $mta = new MTA;

        $mta->modem_id = $new_modem->id;
        $mta->mac = $old_mta->mac_adresse;
        $mta->configfile_id = $this->configfiles[$old_mta->configfile] ?? 0;
        $mta->type = 'sip';

        $mta->save();

        $new_modem->mtas->add($mta);

        \Log::info('ADD MTA: '.$mta->id.', '.$mta->mac.', CF-'.$mta->configfile_id);

        if (! $mta->configfile_id) {
            Log::warning("No Configfile set on MTA $mta->id (ID)");
        }

        return $mta;
    }

    /**
     * Add Phonenumber to corresponding MTA
     */
    private function add_phonenumber($new_mta, $old_phonenumber, $db_con)
    {
        $pns_n = $new_mta->phonenumbers;

        if (! $old_phonenumber->rufnummer) {
            \Log::error("Missing number of phonenumber with username $old_phonenumber->username and old ID $old_phonenumber->id", ["new MTA-ID: $new_mta->id"]);
        }

        // check if phonenumber was already added
        if (! $pns_n->isEmpty() && $pns_n->contains('number', $old_phonenumber->rufnummer)) {
            $new_pn = $pns_n->where('number', $old_phonenumber->rufnummer)->first();

            Log::info("Phonenumber ($old_phonenumber->vorwahl/$old_phonenumber->rufnummer) already exists in new System with ID $new_pn->id!", ["MTA-ID: $new_mta->id"]);

            return $new_pn;
        }

        $carrier = $db_con->table('tbl_clis')
            ->where('endpoint', '=', $old_phonenumber->id)
            ->where('carrier', '!=', null)->where('carrier', '!=', '')
            ->select('id', 'carrier')
            ->distinct()
            ->orderBy('id', 'desc')
            ->first();

        $carrier = $carrier ? $carrier->carrier : null;

        switch ($carrier) {
            case 'PURTel': $registrar = 'deu3.purtel.com'; break;
            case 'EnviaTel': $registrar = 'sip.enviatel.net'; break;
            default: $registrar = null;
                \Log::warning("Missing Registrar for Phonenumber $old_phonenumber->vorwahl/$old_phonenumber->rufnummer");
                break;
        }

        $phonenumber = new Phonenumber;

        $phonenumber->mta_id = $new_mta->id;
        $phonenumber->port = $old_phonenumber->port;
        $phonenumber->country_code = '0049';
        $phonenumber->prefix_number = $old_phonenumber->vorwahl;
        $phonenumber->number = $old_phonenumber->rufnummer;
        $phonenumber->username = $old_phonenumber->username;
        $phonenumber->password = $old_phonenumber->password;
        $phonenumber->sipdomain = $registrar;
        $phonenumber->active = true;  		// $old_phonenumber->aktiv; 		most phonenrs are marked as inactive because of automatic controlling

        $phonenumber->save();

        Log::info("ADD Phonenumber: $phonenumber->id (ID), ".$phonenumber->country_code.$phonenumber->prefix_number.$phonenumber->number.', '.($old_phonenumber->aktiv ? 'active' : 'inactive (but currently set fix to active)'));

        // add new PN to relation collection to check on next add if it was already added
        $new_mta->phonenumbers->add($phonenumber);

        return $phonenumber;
    }

    /**
     * Add Modems of Netelements to Erznet Contract as this is still necessary to get them online in new system
     */
    private function add_netelements($db_con, $area_filter)
    {
        $devices = $db_con->table('tbl_modem as m')
                    ->selectRaw('m.*, cm_adr.*, m.id as id, c.name as cf_name')
                    ->join('tbl_adressen as cm_adr', 'm.adresse', '=', 'cm_adr.id')
                    ->join('tbl_configfiles as c', 'm.configfile', '=', 'c.id')
                    ->where('m.deleted', '=', 'false')
                    ->where('m.device', '=', 9)
                    // ->where('m.mac_adresse', '=', '00:d0:55:07:1d:86')
                    ->where($area_filter)
                    ->get();

        if (! $devices) {
            return;
        }

        Log::info('ADD NETELEMENT Modems');

        $contract = Contract::find(self::$ne_contract_id);

        if (! $contract) {
            $msg = 'Wrong contract ID '.self::$ne_contract_id.' for netelement modems! Could not find this contract.';
            $this->error($msg);
            Log::error($msg.' Stop.');

            exit(1);
        }

        echo "ADD NETELEMENT Modems\n";
        $bar = $this->output->createProgressBar(count($devices));
        $bar->start();

        foreach ($devices as $device) {
            $bar->advance();
            self::add_modem($contract, $device, $db_con);
        }

        $bar->finish();
    }

    private static function _blockcpe($contract)
    {
        \Log::notice("Disable internet_access of all modems of contract number $contract->number");

        foreach ($contract->modems as $cm) {
            $cm->internet_access = 0;
            $cm->save();
        }
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
            ['plz', null, InputOption::VALUE_OPTIONAL, 'Import only Contracts with special zip code (from tbl_adressen), e.g. 09518', 0],
            ['cluster', null, InputOption::VALUE_OPTIONAL, 'Import only Contracts/Modems from cluster_id, e.g. 160', 0],
            ['cc', null, InputOption::VALUE_OPTIONAL, 'CostCenter ID for all the imported Contracts', 0],
            // array('debug', null, InputOption::VALUE_OPTIONAL, '1 enables debug', 0),
            // array('terminate', null, InputOption::VALUE_OPTIONAL, 'Date for all km3 Contracts to terminate', 0),
        ];
    }

    /**
     * Temporary functions to fix database bugs
     */
    public static function update_mandates_correct_encoding($db_con)
    {
        foreach (SepaMandate::all() as $m) {
            if ($m->contract->firstname.' '.$m->contract->lastname == $m->holder) {
                continue;
            }

            $mandate_old = $db_con->table(\DB::raw('tbl_sepamandate s, tbl_lastschriftkonten l'))
                    ->selectRaw('s.*, l.*, l.id as id')
                    ->whereRaw('s.id = l.sepamandat')
                    ->where('l.iban', '=', $m->iban)
                    ->where('s.deleted', '=', 'false')
                    ->where('l.deleted', '=', 'false')
                    ->orderBy('l.id')
                    ->get();

            if (! $mandate_old) {
                // echo "\tERROR: No corresponding SepaMandate in old sys [$m->id]";
                continue;
            }

            $mandate_old = $mandate_old[0];

            if ($m->holder == $mandate_old->kontoinhaber) {
                continue;
            }

            echo "\nSEPAMANDATE UPDATE [$m->id]: $m->holder to $mandate_old->kontoinhaber";

            $m->holder = $mandate_old->kontoinhaber ? utf8_encode($mandate_old->kontoinhaber) : '';

            $m->save();
        }

        exit(0);
    }

    /**
     * Import SIP Passwords from Envia CSV - needed after changing tel protocol from MGCP to SIP
     */
    public static function set_phonenr_passwords()
    {
        $fn = storage_path('app/tmp/wildenstein-mgcp-to-sip-passwords.csv');
        $csv = file($fn);
        $num = count($csv);

        foreach ($csv as $i => $line) {
            $line = str_getcsv($line, ';');

            echo "$i/$num\r";
            $username = $line[6];
            $psw = $line[7];

            $pn = Phonenumber::where('username', '=', $username)->first();

            if ($pn) {
                $pn->password = $psw;
                $pn->save();
            } else {
                echo "Error: Could not find phonenumber with username $username!\n";
            }
        }
    }
}
