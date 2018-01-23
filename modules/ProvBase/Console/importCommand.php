<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Log;

use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvBase\Entities\Configfile;

use Modules\BillingBase\Entities\Item;
use Modules\BillingBase\Entities\Product;
use Modules\BillingBase\Entities\SepaMandate;

use Modules\ProvVoip\Entities\Mta;
use Modules\ProvVoip\Entities\Phonenumber;

use Modules\Mail\Entities\Email;


class importCommand extends Command {

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
	 * Mapping of old Internet Tarif Names to new Tarif IDs
	 *
	 * @var array
	 */
	protected $old_sys_inet_tarifs;

	/**
	 * Mapping of old Voip Tarif IDs to new Voip Tarif IDs
	 *
	 * @var array
	 */
	protected $old_sys_voip_tarifs;

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
	 		(* set cluster mapping table)
	 */
	public function fire()
	{
		// NOTE: Search by TODO(2) for Contract Filter and TODO(3) to change restrictions for adding credits!
		if (!$this->confirm("IMPORTANT!!!\n\nHave following things been prepared for this import?:
			(1) Created Mapping Configfile?
			(2) Has Contract filter been correctly set up (in source code)?
			(3) Shall volume tarifs get Credits (in source code)?\n"))
			return;

		// Pre - Testing
		if (!Qos::count())
			return $this->error('no QOS entry exists to use');

		if (!Configfile::count())
			return $this->error('no configfile entry exists to use');

		if (!Product::count())
			return $this->error('no product entry exists to use');


		$cluster_filter = $this->option('cluster')  ? 'm.cluster_id = '.$this->option('cluster') : 'TRUE';
		$plz_filter 	= $this->option('plz') 		? 'cm_adr.plz = \''.$this->option('plz')."'" : 'TRUE';

		// TODO(2): Adapt this Contract Filter for every Import
		$area_filter = function ($query) use ($cluster_filter) {$query
				->whereRaw ($cluster_filter)
				// ->whereRaw("cm_adr.strasse not like '%Stra%'")
				// ->where(function ($query) { $query
				// 	->whereRaw ("cm_adr.strasse like '%Flo%m%hle%'")
				// 	->orWhereRaw ("cm_adr.strasse like 'Fl%talstr%'")
				// 	->orWhereRaw ("cm_adr.ort like '%/OT Flo%'");}
				// )
				;};

		$this->_load_mappings();

		// Connect to old Database
		$km3 = \DB::connection('pgsql-km3');

		// Get all important Data from new DB
		$products_new = Product::all();


		/**
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
				->selectRaw ('distinct on (v.vertragsnummer) v.vertragsnummer, v.*, a.*, k.*, t.name as tariffname,
					v.id as id, v.beschreibung as contr_descr, m.cluster_id')
				->join('tbl_modem as m', 'm.vertrag', '=', 'v.id')
				->join('tbl_adressen as a', 'v.ansprechpartner', '=', 'a.id')
				->join('tbl_adressen as cm_adr', 'm.adresse', '=', 'cm_adr.id')
				->join('tbl_kunde as k', 'v.kunde', '=', 'k.id')
				->join('tbl_tarif as t', 'v.tarif', '=', 't.id')
				->join('tbl_posten as p', 't.posten_volumen_extern', '=', 'p.id')
				->where ('v.deleted', '=', 'false')
				->where ('m.deleted', '=', 'false')
				->whereRaw('(v.abgeklemmt is null or v.abgeklemmt >= CURRENT_DATE)') 		// dont import out-of-date contracts
				->where($area_filter)

				->orderBy('v.vertragsnummer')
				->get();


		// progress bar
		$i   = 1;
		$num = count($contracts);

		foreach ($contracts as $contract)
		{
			$this->info("\n$i/$num");
			$c = $this->add_contract($contract);

			/*
			 * MODEM Import
			 */
			$modems = $km3->table(\DB::raw('tbl_modem m, tbl_adressen a, tbl_configfiles c'))
					->selectRaw ('m.*, a.*, m.id as id, c.name as cf_name')
					->where('m.vertrag', '=', $contract->id)
					->whereRaw('m.adresse = a.id')
					->whereRaw('m.configfile = c.id')
					->where ('m.deleted', '=', 'false')->get();

			foreach ($modems as $modem)
			{
				$m = $this->add_modem($c, $modem, $km3);


				/*
				 * MTA Import
				 */
				$mtas = $km3->table(\DB::raw('tbl_computer c, tbl_packetcablemtas mta, tbl_configfiles cf'))
					->selectRaw ('c.*, mta.*, cf.name as configfile, mta.id as id')
					->where('c.modem', '=', $modem->id)
					->whereRaw('mta.computer = c.id')
					->whereRaw('mta.configfile = cf.id')
					->where('c.deleted', '=', 'false')
					->where('mta.deleted', '=', 'false')
					->get();

				foreach ($mtas as $mta)
				{
					$mta_n = $this->add_mta($m, $mta);


					/*
				 	 * Phonenumber Import
					 */
					$phonenumbers = $km3->table('tbl_mtaendpoints as e')
						->join('tbl_clis as c', 'c.endpoint', '=', 'e.id')
						->where('e.mta', '=', $mta->id)
						->where ('e.deleted', '=', 'false')
						->select('e.*', 'c.carrier')
						->get();

					foreach ($phonenumbers as $phonenumber)
						$p = $this->add_phonenumber($mta_n, $phonenumber);
				}
			}

			// Email Import
			if (\PPModule::is_active('mail'))
				self::add_email($c, $contract);

			// Add Billing related Data
			$this->add_tarifs($c, $products_new, $contract);
			$this->add_tarif_credit($c, $contract);
			$this->add_sepamandate($c, $contract, $km3);
			$this->add_additional_items($c, $km3, $contract);

			$i++;
		}

		echo "\n";
	}


	/**
	 * Load all necessary mappings from config file
		(1) Tariff (Inet + Voip)
	 	(2) Configfile
		(3) Item-Mapping (Zusatzposten)
	 */
	private function _load_mappings()
	{
		$arr = require $this->argument('filename');

		$this->old_sys_inet_tarifs = $arr['old_sys_inet_tarifs'];
		$this->old_sys_voip_tarifs = $arr['old_sys_voip_tarifs'];
		$this->configfiles 		   = $arr['configfiles'];
		$this->add_items 		   = $arr['add_items'];

		if (isset($arr['cluster']))
			$this->cluster = $arr['cluster'];
	}


	/**
	 * Extract last number from street (and encode dependent of andre schuberts encoding mechanism)
	 */
	public static function split_street_housenr($string, $utf8_encode = false)
	{
		preg_match('/(\d+)(?!.*\d)/', $string, $matches);
		$matches = $matches ? $matches[0] : '';

		if (!$matches)
		{
			$street = $utf8_encode ? utf8_encode($string) : $string;
			return [$street, null];
		}

		$x 		 = strpos($string, $matches);
		$housenr = substr($string, $x);

		if (strlen($housenr) > 6) {
			$street  = str_replace($matches, '', $string);
			$housenr = $matches;
		}
		else
			$street = trim(substr($string, 0, $x));

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
			$this->error("Contract $c->vertragsnummer already exists [$c->id]");
			\Log::error("Contract $c->vertragsnummer already exists [$c->id]");
			return $c;
		}

		$c = new Contract;

		// import fields
		$c->number 			= $old_contract->vertragsnummer;
		$c->number2 		= '002-'.$old_contract->vertragsnummer;
		$c->number4 		= '002-'.$old_contract->kundennr;
		$c->salutation 		= $old_contract->anrede;
		$c->company 		= $old_contract->firma;
		$c->firstname 		= $old_contract->vorname;
		$c->lastname 		= $old_contract->nachname;

		$ret = self::split_street_housenr($old_contract->strasse);
		$c->street 			= $ret[0];
		$c->house_number 	= $ret[1];

		$c->zip 			= $old_contract->plz;
		$c->city 			= $old_contract->ort;
		$c->phone 			= str_replace("/", "", $old_contract->tel);
		$c->fax 			= $old_contract->fax;
		$c->email 			= $old_contract->email;

		// TODO: Fix that birthday and contract_end are '0000-00-00' in DB when not set
		$c->birthday 		= $old_contract->geburtsdatum ? : null;

		$c->description 	= $old_contract->beschreibung."\n".$old_contract->contr_descr;
		$c->network_access 	= $old_contract->network_access;
		$c->contract_start 	= $old_contract->angeschlossen;
		$c->contract_end   	= $old_contract->abgeklemmt ? : null;
		$c->create_invoice 	= $old_contract->rechnung;

		$c->costcenter_id 	= $this->option('cc') ? : 3; // Dittersdorf=1, new one would be 3
		$c->cluster 		= $this->map_cluster_id($old_contract->cluster_id);
		$c->net 			= $this->map_cluster_id($old_contract->cluster_id, 1);


		// set fields with null input to ''.
		// This fixes SQL import problem with null fields
		$relations = $c->relationsToArray();
		foreach( $c->toArray() as $key => $value )
		{
			if (array_key_exists($key, $relations))
				continue;

			$c->{$key} = $c->{$key} ? : '';

			if (is_string($c->{$key}))
				$c->{$key} = utf8_encode ($c->{$key});
		}
		$c->deleted_at = NULL;

		// Update or Create Entry
		$c->save();

		\Log::info ("ADD CONTRACT: $c->id, $c->firstname $c->lastname, $c->street, $c->zip $c->city [$old_contract->vertragsnummer]");
		$this->info ("\nADD CONTRACT: $c->id, $c->firstname $c->lastname, $c->street, $c->zip $c->city [$old_contract->vertragsnummer]");

		return $c;
	}


	/**
	 * Return the appropriate Product ID from new System dependent on the tarif of the old systems contract
	 *
	 * @param 	tarif 	String|Integer 		Old systems internet tarif name | Voip Tarif ID
	 * @return 			Integer 			Product ID | -1 on Error
	 *
	 * @author 	Nino Ryschawy
	 */
	private function _map_tarif_to_prod($tarif)
	{
		// Voip
		if (is_int($tarif))
		{
	 		if (!array_key_exists($tarif, $this->old_sys_voip_tarifs))
	 			return -1;

	 		return $this->old_sys_voip_tarifs[$tarif];
		}

		// Inet
		if (!array_key_exists($tarif, $this->old_sys_inet_tarifs))
			return -1;

 		return is_int($this->old_sys_inet_tarifs[$tarif]) ? $this->old_sys_inet_tarifs[$tarif] : -1;
	}


	/**
	 * Return ID of Cluster/Net for new System from old systems cluster/net ID
	 *
	 * @param 	cluster_id 		Integer
	 * @parma 	$net 			0/1 		Switch: 0 - return cluster id, 1 - return net id
	 *
	 * @return 	Integer
	 */
	private function map_cluster_id($cluster_id, $net = 0)
	{
		// old cluster ID => cluster ID in new System
		// TODO: Add new Cluster IDs when they exist in new system
		return $this->cluster[$cluster_id][$net];
	}


	/**
	 * Add Tarifs to corresponding Contract of new System
	 *
	 * TODO: Tarif next month can not be set as is - has still ID - Separate inet & voip tarif mappings and map all by id
	 */
	private function add_tarifs($new_contract, $products_new, $old_contract)
	{
		$tarifs = array(
			'tarif' 			=> $old_contract->tariffname,
			'tarif_next_month'  => $old_contract->tarif_next_month,
			'voip' 				=> $old_contract->telefontarif,
			);

		$items_new = $new_contract->items;

		foreach ($tarifs as $key => $tarif)
		{
			if (!$tarif) {
				\Log::info("\tNo $key Item exists in old System");
				continue;
			}

			$prod_id = $this->_map_tarif_to_prod($tarif);
			$item_n  = $items_new->where('product_id', $prod_id)->all();

			if ($item_n) {
				$this->error("\tItem $key for Contract ".$new_contract->id." already exists");
				\Log::error("\tItem $key for Contract ".$new_contract->id." already exists");
				continue;
			}

			if ($prod_id <= 0) {
				\Log::error("\tProduct $prod_id does not exist yet [$tarif]");
				continue;
			}

			Item::create([
				'contract_id' 		=> $new_contract->id,
				'product_id' 		=> $prod_id,
				'valid_from' 		=> $key == 'tarif_next_month' ? date('Y-m-01', strtotime('first day of next month')) : $old_contract->angeschlossen,
				'valid_from_fixed' 	=> 1,
				'valid_to' 			=> $key == 'tarif_next_month' ? NULL : $old_contract->abgeklemmt,
				'valid_to_fixed' 	=> 1,
				]);

			\Log::info ("ITEM ADD $key: ".$products_new->find($prod_id)->name.' ('.$prod_id.')');
			$this->info ("ITEM ADD $key: ".$products_new->find($prod_id)->name.' ('.$prod_id.')');
		}
	}


	/**
	 * Add extra credit item (5 Euro gross - 1 Year) if customer had an old volume tariff
	 */
	private function add_tarif_credit($new_contract, $old_contract)
	{
		// TODO(5) check restrictions of volume tarifs!
		if ((strpos($old_contract->tariffname, 'Volumen') === false) && (strpos($old_contract->tariffname, 'Speed') === false) && (strpos($old_contract->tariffname, 'Basic') === false))
			return;

		\Log::info("Add extra Credit as Customer had volume tariff. [$new_contract->number]");

		Item::create([
			'contract_id' 		=> $new_contract->id,
			'product_id' 		=> 10,
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

		if (!$mandates_n->isEmpty()) {
			\Log::notice("\tCustomer $new_contract->id already has SepaMandate assigned");
			return $this->error("\tCustomer $new_contract->number [$new_contract->id] already has SepaMandate assigned");
		}

		$mandates_old = $db_con->table('tbl_sepamandate as s')
				->join('tbl_lastschriftkonten as l', 's.id', '=', 'l.sepamandat')
			 	->select ('s.*', 'l.*', 'l.id as id')
			 	->where('s.kunde', '=', $old_contract->kunde)
			 	->where('s.deleted', '=', 'false')
			 	->where('l.deleted', '=', 'false')
			 	->orderBy('l.id')
			 	->get();

		if (!$mandates_old)
			$this->line("\tCustomer $new_contract->id has no SepaMandate in old DB");

		foreach ($mandates_old as $mandate)
		{
			SepaMandate::create([
				'contract_id' 		=> $new_contract->id,
				'reference' 		=> $new_contract->number ? : '', 			// TODO: number circle ?
				'signature_date' 	=> $mandate->datum ? : '',
				'sepa_holder' 		=> $mandate->kontoinhaber ? utf8_encode($mandate->kontoinhaber) : '',
				'sepa_iban'			=> $mandate->iban ? : '',
				'sepa_bic' 			=> $mandate->bic ? : '',
				'sepa_institute' 	=> $mandate->institut ? : '',
				'sepa_valid_from' 	=> $mandate->gueltig_ab,
				'sepa_valid_to' 	=> $mandate->gueltig_bis,
				'disable' 			=> $old_contract->einzug ? false : true,
				'state' 			=> 'RCUR',
				]);

			\Log::info ("SEPAMANDATE ADD: ".$mandate->kontoinhaber.', '.$mandate->iban.', '.$mandate->institut.', '.$mandate->datum);
			$this->info ("SEPAMANDATE ADD: ".$mandate->kontoinhaber.', '.$mandate->iban.', '.$mandate->institut.', '.$mandate->datum);
		}
	}

	/**
	 * Add all relevant additional Items - see mapping table below which are relavant
	 */
	private function add_additional_items($new_contract, $db_con, $old_contract)
	{
		// Additional Items
		$items = $db_con->table(\DB::raw('tbl_zusatzposten z, tbl_posten p'))
				->selectRaw ('p.id, p.artikel, z.von, z.bis, z.menge, z.buchungstext, z.preis')
				->where('z.vertrag', '=', $old_contract->id)
				->whereRaw('z.posten = p.id')
				->where('z.closed', '=', 'false')
				->where(function ($query) { $query
					->where('z.bis', '>', date('Y-m-d'))
					->orWhere ('z.bis', '=', null);})
				->get();

		$items_new = $new_contract->items;

		foreach ($items as $item)
		{
			$prod_id = isset($this->add_items[$item->id]) ? $this->add_items[$item->id] : null;

			if (!$prod_id) {
				$this->error("\tCan not map Artikel \"$item->artikel\" - ID $item->id does not exist in internal mapping table");
				\Log::error("\tCan not map Artikel \"$item->artikel\" - ID $item->id does not exist in internal mapping table");
				continue;
			}

			if ($item->id == 1 && !$item->preis)
				continue;

			// Check if item already exists
			if ($items_new->contains('product_id', $prod_id))
				\Log::warning("Additional item with product id $prod_id already exists for Contract ".$new_contract->number.'! (Added again)');

			// \Log::info("Add Item [$new_contract->number]: $item->artikel (from: $item->von, to: $item->bis, price: $item->preis) [Old ID: $item->id]");
			$this->info("\tAdd Item [$new_contract->number]: $item->artikel (from: $item->von, to: $item->bis, price: $item->preis) [Old ID: $item->id]");

			Item::create([
				'contract_id' 		=> $new_contract->id,
				'product_id' 		=> $this->add_items[$item->id],
				'count' 			=> $item->menge,
				'valid_from' 		=> $item->von ? : date('Y-m-d'),
				'valid_from_fixed' 	=> 1,
				'valid_to' 			=> $item->bis,
				'valid_to_fixed' 	=> 1,
				'credit_amount' 	=> (-1) * $item->preis,
				'accounting_text' 	=> $item->buchungstext,
			]);
		}
	}


	/**
	 * Add Emails to corresponding Contract
	 */
	private function add_email($new_contract, $old_contract)
	{
		$emails = $km3->table('tbl_email')->selectRaw ('*')->where('vertrag', '=', $old_contract->id)->get();
		$emails_new_cnt = \PPModule::is_active('mail') ? $new_contract->emails()->count() : [];

		if (count($emails) == $emails_new_cnt)
			return $this->error('Email Aliases already added!');

		foreach ($emails as $email)
		{
			Email::create([
				'contract_id' 	=> $new_contract->id,
				'localpart' 	=> $email->alias,
				'password' 		=> $email->passwort,
				'blacklisting' 	=> $email->blacklisting,
				'greylisting' 	=> $email->greylisting,
				'forwardto' 	=> $email->forwardto ? : '',
				]);
		}

		// Log
		$this->line ("MAIL: ADDED ".count($emails).' Addresses');
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

		if (!$modems_n->isEmpty() && $modems_n->contains('mac', $old_modem->mac_adresse))
		{
			$new_cm = $modems_n->where('mac', $old_modem->mac_adresse)->first();

			$this->info("Modem already exists in new System with ID $new_cm->id!");
			return $new_cm;
		}

		$modem = new Modem;

		// import fields
		$modem->mac     = $old_modem->mac_adresse;
		$modem->number  = $old_modem->id;
		$modem->name 	= utf8_encode($old_modem->name);

		$modem->serial_num   = $old_modem->serial_num;
		$modem->inventar_num = $old_modem->inventar_num;
		$modem->description  = $old_modem->beschreibung;
		$modem->network_access = $old_modem->network_access;

		$modem->x = $old_modem->x / 10000000;
		$modem->y = $old_modem->y / 10000000;

		$modem->firstname = $new_contract->firstname;
		$modem->lastname  = $new_contract->lastname;

		if ($old_modem->strasse)
		{
			$ret = self::split_street_housenr($old_modem->strasse, true);

			$modem->street 		 = $ret[0];
			$modem->house_number = $ret[1];
		}
		else
		{
			$modem->street 		 = $new_contract->street;
			$modem->house_number = $new_contract->house_number;
		}

		$modem->zip       = $new_contract->zip;
		$modem->city      = $new_contract->city;
		$modem->qos_id    = $new_contract->qos_id;

		$modem->contract_id   = $new_contract->id;
		$modem->configfile_id = isset($this->configfiles[$old_modem->cf_name]) && is_int($this->configfiles[$old_modem->cf_name]) ? $this->configfiles[$old_modem->cf_name] : 0;

		// check if assigned cpe has public ip (starts with 7 or 8)
		// NOTE: if even 1 of the cpe's has a public IP we assign a public IP for all CPE's here - Note: in future we maybe have maxCPE 1
		$comps = $db_con->table('tbl_computer')
			->select('ip')
			->where('modem', '=', $old_modem->id)
			->get();

		$modem->public = 0;
		foreach ($comps as $comp)
		{
			if ($comp->ip[0] != '1') {
				$modem->public = 1;
				break;
			}
		}

		// set fields with null input to ''.
		// This fixes SQL import problem with null fields
		$relations = $modem->relationsToArray();
		foreach( $modem->toArray() as $key => $value )
		{
			if (array_key_exists($key, $relations))
				continue;

			$modem->{$key} = $modem->{$key} ? : '';
		}
		$modem->deleted_at = NULL;

		// Output
		if ($modem->configfile_id == 0) {
			$this->error('No Configfile could be assigned to Modem '.$modem->id." Old ModemID: $old_modem->id");
			\Log::error('No Configfile could be assigned to Modem '.$modem->id." Old ModemID: $old_modem->id");
		}

		\Log::info ("ADD MODEM: $modem->mac, QOS-$modem->qos_id, CF-$modem->configfile_id, $modem->street, $modem->zip, $modem->city, Public: ".($modem->public ? 'yes' : 'no'));
		$this->info ("ADD MODEM: $modem->mac, QOS-$modem->qos_id, CF-$modem->configfile_id, $modem->street, $modem->zip, $modem->city, Public: ".($modem->public ? 'yes' : 'no'));

		$modem->save();

		return $modem;
	}


	/**
	 * Add MTA to corresponding Modem of new System
	 */
	private function add_mta($new_modem, $old_mta)
	{
		// dont update new mtas with old data - return mta that new phonenumbers can be assigned
		$mtas_n = $new_modem->mtas;

		if (!$mtas_n->isEmpty() && $mtas_n->contains('mac', $old_mta->mac_adresse))
		{
			$new_mta = $mtas_n->where('mac', $old_mta->mac_adresse)->first();

			$this->info("MTA already exists in new System with ID $new_mta->id!");
			return $new_mta;
		}

		$mta = new MTA;

		$mta->modem_id 	= $new_modem->id;
		$mta->mac 		= $old_mta->mac_adresse;
		$mta->configfile_id = isset($this->configfiles[$old_mta->configfile]) && is_int($this->configfiles[$old_mta->configfile]) ? $this->configfiles[$old_mta->configfile] : 0;
		$mta->type = 'sip';

		// Log
		\Log::info ("ADD MTA: ".$mta->id.', '.$mta->mac.', CF-'.$mta->configfile_id);
		$this->info ("ADD MTA: ".$mta->id.', '.$mta->mac.', CF-'.$mta->configfile_id);

		$mta->save();

		return $mta;
	}


	/**
	 * Add Phonenumber to corresponding MTA
	 */
	private function add_phonenumber($new_mta, $old_phonenumber)
	{
		$pns_n = $new_mta->phonenumbers;

		if (!$pns_n->isEmpty() && $pns_n->contains('username', $old_phonenumber->username))
		{
			$new_pn = $pns_n->where('username', $old_phonenumber->username)->first();

			$this->info("Phonenumber already exists in new System with ID $new_pn->id!");
			return $new_pn;
		}

		switch ($old_phonenumber->carrier)
		{
			case 'PURTel': $registrar = 'deu3.purtel.com'; break;
			case 'EnviaTel': $registrar = 'sip.enviatel.net'; break;
			default: $registrar = ''; \Log::warning("Missing Registrar for Phonenumber $old_phonenumber->vorwahl/$old_phonenumber->rufnummer"); break;
		}

		$phonenumber = new Phonenumber;

		$phonenumber->mta_id 		= $new_mta->id;
		$phonenumber->port 			= $old_phonenumber->port;
		$phonenumber->country_code 	= '0049';
		$phonenumber->prefix_number = $old_phonenumber->vorwahl;
		$phonenumber->number 		= $old_phonenumber->rufnummer;
		$phonenumber->username 		= $old_phonenumber->username;
		$phonenumber->password 		= $old_phonenumber->password;
		// TODO
		$phonenumber->sipdomain 	= $registrar;
		$phonenumber->active 		= true;  		// $old_phonenumber->aktiv; 		most phonenrs are marked as inactive because of automatic controlling

		// Log
		\Log::info ("ADD Phonenumber: ".$phonenumber->id.', '.$new_mta->id.', '.$phonenumber->country_code.$phonenumber->prefix_number.$phonenumber->number.', '.($old_phonenumber->aktiv ? 'active' : 'inactive (but currently set fix to active)'));
		$this->info ("ADD Phonenumber: ".$phonenumber->id.', '.$new_mta->id.', '.$phonenumber->country_code.$phonenumber->prefix_number.$phonenumber->number.', '.($old_phonenumber->aktiv ? 'active' : 'inactive (but currently set fix to active)'));

		$phonenumber->save();

		return $phonenumber;
	}


	/**
	 * Add Modems of Netelements to Erznet Contract as this is still necessary to get them online in new system
	 */
	private function add_netelements($db_con, $area_filter)
	{
		$devices = $db_con->table('tbl_modem as m')
					->selectRaw ('m.*, cm_adr.*, m.id as id, c.name as cf_name')
					->join('tbl_adressen as cm_adr', 'm.adresse', '=', 'cm_adr.id')
					->join('tbl_configfiles as c', 'm.configfile', '=', 'c.id')
					->where ('m.deleted', '=', 'false')
					->where('m.device', '=', 9)
					// ->where('m.mac_adresse', '=', '00:d0:55:07:1d:86')
					->where($area_filter)
					->get();

		if (!$devices)
			return;

		$this->info ("ADD NETELEMENT Modems");

		$contract = Contract::find(500000);

		foreach ($devices as $device)
			self::add_modem($contract, $device, $db_con);
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('filename', InputArgument::REQUIRED, 'Name of Mapping Configfile in Storage directory'),
		);
	}


	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('plz', null, InputOption::VALUE_OPTIONAL, 'Import only Contracts with special zip code (from tbl_adressen), e.g. 09518', 0),
			array('cluster', null, InputOption::VALUE_OPTIONAL, 'Import only Contracts/Modems from cluster_id, e.g. 160', 0),
			array('cc', null, InputOption::VALUE_OPTIONAL, 'CostCenter ID for all the imported Contracts', 0),
			// array('debug', null, InputOption::VALUE_OPTIONAL, '1 enables debug', 0),
			// array('terminate', null, InputOption::VALUE_OPTIONAL, 'Date for all km3 Contracts to terminate', 0),
		);
	}




	/**
	 * Temporary functions to fix database bugs
	 */
	public static function update_mandates_correct_encoding($db_con)
	{
		foreach (SepaMandate::all() as $m)
		{
			if ($m->contract->firstname.' '.$m->contract->lastname == $m->sepa_holder)
				continue;

			$mandate_old = $db_con->table(\DB::raw('tbl_sepamandate s, tbl_lastschriftkonten l'))
				 	->selectRaw ('s.*, l.*, l.id as id')
				 	->whereRaw('s.id = l.sepamandat')
				 	->where('l.iban', '=', $m->sepa_iban)
				 	->where('s.deleted', '=', 'false')
				 	->where('l.deleted', '=', 'false')
				 	->orderBy('l.id')
				 	->get();

			if (!$mandate_old) {
				// echo "\tERROR: No corresponding SepaMandate in old sys [$m->id]";
				continue;
			}

			$mandate_old = $mandate_old[0];

			if ($m->sepa_holder == $mandate_old->kontoinhaber)
				continue;

			echo "\nSEPAMANDATE UPDATE [$m->id]: $m->sepa_holder to $mandate_old->kontoinhaber";

			$m->sepa_holder = $mandate_old->kontoinhaber ? utf8_encode($mandate_old->kontoinhaber) : '';
			$m->save();

		}

		exit(0);
	}

}
