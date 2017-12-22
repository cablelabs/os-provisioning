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


	// TODO(2): mapping should be done by configfile and id <-> id
	// Marienberg
	// protected $old_sys_inet_tarifs = [
	// 	'BusinessBasic' 				=> 'business',
	// 	'BusinessFlat' 					=> 'business',
	// 	'Industrie/Gewerbe 150' 		=> 22,
	// 	'Industrie/Gewerbe 145' 		=> 'business',
	// 	'Internet Flat 6000 REI' 		=> 2,
	// 	'Pob_PrivatBasic' 				=> 4, 			// Volumentarif
	// 	'Pob_PrivatBasic10G' 			=> 4, 			// Volumentarif
	// 	'Pob_PrivatBasic5G' 			=> 4, 			// Volumentarif
	// 	'Pob_PrivatFlat' 				=> 'vol',
	// 	'Pob_PrivatFlat Spar' 			=> 'vol',
	// 	'keineDaten' 					=> 0,
	// 	'PrivatBasic' 					=> 4,
	// 	'PrivatBasic 10G' 				=> 4, 			// Volumentarif
	// 	'PrivatFlat Spar' 				=> 1,
	// 	'PrivatFlat' 					=> 1,
	// 	'Keine Daten' 					=> 0,
	// 	'PrivatBasic 5G' 				=> 4, 			// Volumentarif
	// 	'Internet Flat 16000 REI' 		=> 4,
	// 	'Internet Flat 6000 SAZ' 		=> 2,
	// 	'Internet Flat 16000 SAZ' 		=> 4,
	// 	'Internet Flat 6000 POB' 		=> 2,
	// 	'Internet Flat 16000 POB' 		=> 4,
	// 	'Internet Flat 2000 POB' 		=> 1,
	// 	'Internet Volumen Basic SAZ' 	=> 4,
	// 	'Internet Flat 6000 (inoffiziel)' => 2,
	// 	'Internet Volumen 10G POB' 		=> 4,
	// 	'Internet Volumen 10G REI' 		=> 4,
	// 	'Internet Volumen 10G SAZ' 		=> 4,
	// 	'Internet Flat 16000' 			=> 4,
	// 	'Internet Speed 10G' 			=> 4, 			// Volumentarif
	// 	'Internet Volumen Basic POB' 	=> 4,
	// 	'Internet Volumen Basic REI' 	=> 4,
	// 	'Internet Volumen 10G - 100000' => 4,
	// 	'Internet Flat 2000 REI' 		=> 1,
	// 	'Internet Flat 2000 SAZ' 		=> 1,
	// 	'Internet Flat Spar SAZ' 		=> 2, 			// inactive
	// 	'Internet Flat Spar MAB,POB' 	=> 2, 			// inactive
	// 	'Internet Flat Spar POB' 		=> 2, 			// inactive
	// 	'Internet Flat Spar REI' 		=> 2, 			// inactive
	// 	'Internet Volumen Basic 100000' => 4,
	// 	'PrivatBasic20G' 				=> 4, 			// Volumentarif
	// 	'Flat 25Mbits.' 				=> 4,
	// 	'PrivatBasic30G' 				=> 4, 			// Volumentari4f
	// 	'PrivatBasic30G REI' 			=> 4, 			// Volumentarif
	// 	'Internet Flat 100000' 			=> 5,
	// 	'Internet Flat 2000' 			=> 1,
	// 	'Internet Flat 25000' 			=> 4,
	// 	'Internet Speed Basic MAB,POB'  => 4,
 // 		];

	 // Wildenstein
	protected $old_sys_inet_tarifs = [
		'PrivatBasic' 				=> 4,
		'PrivatBasic 10G' 			=> 4,
		'PrivatFlat Spar' 			=> 1,
		'PrivatBasic 5G' 			=> 4,
		'Internet Speed 10G' 		=> 4,
		'Internet Speed Basic'  	=> 4,
		'Internet Volumen 10G'  	=> 4,
		'Internet Volumen Basic' 	=> 4,
		'keine Daten' 				=> 0,
		'PrivatBasic30G' 			=> 4,
		'Internet Flat 6000' 		=> 2,
		// 'Internet Flat 50000 LBD' 	=> ,
		'Internet Flat 6000+' 		=> 2,
		'Internet Flat 16000' 		=> 4,
		'Internet Flat Spar' 		=> 2,
		'Internet Flat 25000' 		=> 4,
		'Internet Flat 100000'  	=> 5,
		];

	// TODO(2)
	// Marienberg
	// protected $old_sys_voip_tarifs = array(
	// 	12464 => 6,		// TelefonieBasic
	// 	12465 => 6,		// TelefonieBasicData
	// 	12466 => 7,		// TelefonieFlatData
	// 	12965 => 7,		// TelefonieFlat
	// 	22668 => 6,		// Rei_TelefonieBasic
	// 	22669 => 6,		// Rei_TelefonieBasicData
	// 	22671 => 7,		// Rei_TelefonieFlatData
	// 	24057 => 6,		// Saz_TelefonieBasic
	// 	24058 => 6,		// Saz_TelefonieBasicData
	// 	24059 => 7,		// Saz_TelefonieFlat
	// 	24060 => 7,		// Saz_TelefonieFlatData
	// 	17663 => 6,		// Pob_TelefonieBasic
	// 	17664 => 6,		// Pob_TelefonieBasicData
	// 	17665 => 7,		// Pob_TelefonieFlat
	// 	17666 => 7,		// Pob_TelefonieFlatData
	// 	22670 => 7,		// Rei_TelefonieFlat
	// 		);

	// Wildenstein
	protected $old_sys_voip_tarifs = array(
		4162 => 38, 	// TelefonieBasic PURTel
		9620 => 39, 	// TelefonieFlatData PURTel
		9649 => 40, 	// TelefonieBasic PURTel (Evt)
		9502 => 6, 		// TelefonieBasic
		9503 => 6, 		// TelefonieBasicData
		9504 => 7, 		// TelefonieFlat
		9505 => 7, 		// TelefonieFlatData
		);


	// TODO(1)
	// Wildenstein
 	protected $configfiles = array(
		'Arris' 					=> 3,
		'Arris 820' 				=> 52,
		'Arris-SIP-MTA SIP v9' 		=> 34,
		'Arris-TG862' 				=> 42,
		'Arris-TG862-v9' 			=> 42,
		'Arris-TG862-v9 sip' 		=> 42,
		'Arris-TM822-SIP-V9' 		=> 55,
		'AVM 6360-85.06.31' 		=> 78,
		'AVM 6490' 					=> 25,
		'Default Configfile' 		=> 3,
		// 'Default-MTA-Config' 		=> ,
		'Fritzbox 6320' 			=> 3,
		'Fritzbox 6320v2' 			=> 3,
		'Fritzbox 6320v2 int' 		=> 3,
		'Fritzbox 6360.06.50' 		=> 66,
		'FritzBox AVM' 				=> 3,
		'FritzBoxAVM-6360 (Ver. 06.51) (SNMP)' => 78,
		'Fritzbox AVM 6490' 		=> 25,
		'Fritzbox AVM 6490 06.84' 	=> 25,
		'Fritzbox AVM 6490 6.50' 	=> 80,
		'FritzBoxAVM-6490 (Ver. 06.51) (SNMP)' => 64,
		'FritzBox AVM MTA' 			=> 81,
		'FritzBox AVM MTA EnviaTel' => 81,
		'FritzBox AVM MTA PURTel' 	=> 81,
		'MTA-SIP-Test' 				=> 18,
		'SNMPSetup-TVM' 			=> 3,
		'Tarris_MTA' 				=> 34,
		'TC7200.20-SIP' 			=> 72,
		'Technicolor' 				=> 50,
		'Technicolor/Thomson' 		=> 50,
		'THG540-T38-Test' 			=> 47,
		'Thomson' 					=> 50,
		'Thomson-eMTA-SIP-EnviaTel' => 20,
		'Thomson-THG540-SIP' 		=> 47,
		'Thomson-THG540-SIP-T.38' 	=> 47,
		'Thomson-THG541-SIP' 		=> 45,
		'Thomson-THG541-SIP-T.38' 	=> 45,
		'Thomson-THG57X-SIP' 		=> 6,
		'Thomson-TWG850-4-SIP' 		=> 46,
		'Thomson-TWG850-SIP' 		=> 46,
		'Thomson-TWG870-SIP' 		=> 44,
		'TVM 2.20' 					=> 71,
 		);

 	// Marienberg
	// protected $configfiles = array(
	// 		'SNMPSetup1' 				=> 3,
	// 		'SNMPBlockMulticast' 		=> 3,
	// 		'SNMPAllowMulticast' 		=> 3,
	// 		'SNMPSetup-TEST-FW-1' 		=> 3,
	// 		'SNMPSetup-TEST-FW-2' 		=> 3,
	// 		'SNMPTVM' 					=> 3,
	// 		'SNMPSIP' 					=> 3,
	// 		'SNMPSetupUpdate' 			=> 3,
	// 		'Thomson-THG540' 			=> 47,
	// 		'Thomson-THG541' 			=> 45,
	// 		'Thomson-TWG850' 			=> 46,
	// 		'Thomson-TWG850-4' 			=> 46,
	// 		'Default MTA Config' 		=> 20,
	// 		'Thomson/Technicolor' 		=> 3,
	// 		'Default CM Config' 		=> 3,
	// 		'TVM1000' 					=> 3,
	// 		'TVM1000-2.08' 				=> 63,
	// 		'TVM1000-2.04'				=> 68,
	// 		'Thomson-THG57X' 			=> 6,
	// 		'Thomson-TWG870' 			=> 44,
	// 		'Kathrein' 					=> 'todo',
	// 		'Kathrein-DCV8400' 			=> 61,
	// 		'TVM1000-2.09' 				=> 69,
	// 		'X_DQOS'					=> 'todo',
	// 		'FritzBox AVM' 				=> 25,
	// 		'TVM1000-2.10' 				=> 70,
	// 		'FritzBox 6360' 			=> 3,
	// 		'FritzBox AVM MTA' 			=> 49,
	// 		'Thomson-THG540-SIP' 		=> 47,
	// 		'Kathrein-DCM42' 			=> 3,
	// 		'Thomson-THG57X-SIP' 		=> 6,
	// 		'Thomson-TWG870-SIP' 		=> 44,
	// 		'TVM1000-2.20' 				=> 71,
	// 		'Thomson-TCM47X' 			=> 3,
	// 		'Thomson' 					=> 3,
	// 		'Technicolor' 				=> 3,
	// 		'TC7200.20' 				=> 3,
	// 		'Delta' 					=> 3,
	// 		'Arris-TG862' 				=> 42,
	// 		'Arris' 					=> 3,
	// 		'Hitron CVE 30360' 			=> 'todo',
	// 		'FritzBox 6320' 			=> 3,
	// 		'TVM1000-2.31' 				=> 70,
	// 		'TC7200.20 v01.03' 			=> 3,
	// 		'Thomson-THG541-SIP' 		=> 45,
	// 		'Thomson-TWG850-SIP' 		=> 46,
	// 		'Thomson-TWG850-4-SIP' 		=> 46,
	// 		'Thomson-TWG870-SIP' 		=> 44,
	// 		'Hitron BVG 3653 SIP' 		=> 'todo',
	// 		'6320v2' 					=> 3,
	// 		'Fritzbox AVM 6490' 		=> 25,
	// 		'6320v2int' 				=> 3,
	// 		'AVM 6490 V.06.51' 			=> 25,
	// 		'AVM Basis Test 6340' 		=> 3,
	// 		'Arris-TG862 SIP' 			=> 42,
	// 		'Tarris' 					=> 42,
	// 		'Tarris-TG862 SIP' 			=> 42,
	// 		'Tarris_MTA' 				=> 34,
	// 		'Arris-TG862 test neueste Firmware' => 53, // TG862 9.01103S5E1
	// 		'Hitron eMTA' 				=> 'todo',
	// 		'FritzBox-6360-6360.85.06.31' => 3,
	// 		'Arris-TG862-v9' 			=> 53,
	// 		'Arris-TG862-v9 sip' 		=> 53,
	// 		'Thomson-eMTA-SIP-EnviaTel' => 20,
	// 		'Fritzbox AVM 6490 06.50' 	=> 25,
	// 		'TC7200.20-SIP' 			=> 3,
	// 		'Fritz Box 6360.85.06.50' 	=> 3,
	// 		'Arris 820' 				=> 52,
	// 		'Arris-MTA-MGCP'			=> 'todo',
	// 		'Arris-TM822-v9' 			=> 3,
	// 		'FritzBoxAVM-6360 (Ver. 06.51) (SNMP)' => 3,
	// 		'FritzBoxAVM-6490 (Ver. 06.51) (SNMP)' => 25,
	// 		'AVM 6.50 - Test' 			=> 25,
	// 		'Arris-TM822-SIP-V9' 		=> 3,
	// 		'Arris-MTA-SIP' 			=> 34,
	// 		'Thomson-THG520-SIP' 		=> 3,
	// 		'AVM 6490 6.84 test' 		=> 25,
	// 	);

 	// Marienberg [cluster id, network id]
	// protected $cluster = array(
	// 		33382 => [null, 3],			// Mbg-Stadt-Gebirge-Dampf
	// 		33383 => [32, 3],			// Mbg-Edeka
	// 		33384 => [null, 3],			// Mbg-NL-LB-HG-LF
	// 		36385 => [null, 3],			// Pobershau
	// 		36464 => [null, 3],			// Olb-Reitz-Rueb
	// 		36546 => [null, 3],			// Satzung
	// 		36821 => [null, 3],			// Khnhaide
	// 		41298 => [null, 3],			// Wolk-Gehr-Strw
	// 	);

	// Wildenstein
	protected static $cluster = array(
			4 => [null, 643],			// Admin_Cluster
			9159 => [644, 643],			// Cluster-D3.0
			9974 => [null, 643],		// Gruenh-Borst
			10025 => [null, 643],		// Boern-Waldk-Krumh
			12905 => [null, 643],		// Cluster LBD
		);

	// TODO(3)
	// Additional Items Marienberg
	// protected static $add_items = [
	// 		1  => 10,			// Gutschrift monatlich
	// 		3  => 23, 			// postalische Rechnung
	// 		11 => 24, 			// Nebenanschluss
	// 		37 => 17, 			// feste öffentliche IP
	// 		42 => 25, 			// Freischalten des Kabel-TV-Internetanschlusses
	// 		65 => 26, 			// Rufnummernfreischaltung
	// 	];

	// Additional Items Wildenstein
	protected static $add_items = [
			9  => 10,			// Gutschrift monatlich
			23 => 23, 			// postalische Rechnung
			11 => 24, 			// Nebenanschluss
			50 => 17, 			// feste öffentliche IP
			6  => 25, 			// Freischalten des Kabel-TV-Internetanschlusses
			29 => 26, 			// Rufnummernfreischaltung
		];


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
		// NOTE: Search by TODO(1) for Configfile Map and so on!
		if (!$this->confirm("IMPORTANT!!!\n\nHave following things been done (in source code) for this import?:
			(1) Configfile-Mapping
			(2) Tariff-Mapping
			(3) Item-Mapping (Zusatzposten)
			(4) Has Contract filter been correctly set up?
			(5) Shall volume tarifs get Credits?\n"))
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

		// TODO(4): Adapt this Contract Filter for every Import
		$area_filter = function ($query) use ($cluster_filter) {$query
				->whereRaw ($cluster_filter)
				// ->whereRaw("cm_adr.strasse not like '%Stra%'")
				// ->where(function ($query) { $query
				// 	->whereRaw ("cm_adr.strasse like '%Flo%m%hle%'")
				// 	->orWhereRaw ("cm_adr.strasse like 'Fl%talstr%'")
				// 	->orWhereRaw ("cm_adr.ort like '%/OT Flo%'");}
				// )
				;};


		// Connect to old Database
		$km3 = \DB::connection('pgsql-km3');

		// Get all important Data from new DB
		$contracts_new 	= Contract::all();
		$modems_new 	= Modem::all();
		$items_new 		= Item::all();
		$mtas_new 		= MTA::all();
		$phonenumbers_new = Phonenumber::all();
		$mandates_new 	= SepaMandate::all();
		$products_new 	= Product::all();
		$emails_new 	= \PPModule::is_active('mail') ? Email::all() : [];


		/**
		 * Add Modems currently needed for HFC Devices (Amplifier & Nodes (VGPs & TVMs))
		 */
		self::add_netelements($km3, $area_filter, $modems_new);


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
			$c = $this->add_contract($contract, $contracts_new);

			// Email Import
			if (\PPModule::is_active('mail'))
				self::add_email($c, $emails_new, $contract);

			/*
			 * MODEM Import
			 */
			$modems = $km3->table(\DB::raw('tbl_modem m, tbl_adressen a, tbl_configfiles c'))
					->selectRaw ('m.*, a.*, m.id as id, c.name as cf_name')
					->where('m.vertrag', '=', $contract->id)
					->whereRaw('m.adresse = a.id')
					->whereRaw('m.configfile = c.id')
					->where ('m.deleted', '=', 'false')->get();

			$modems_n = [];
			foreach ($modems_new->where('contract_id', $c->id)->all() as $cm)
				$modems_n[$cm->mac] = $cm;

			foreach ($modems as $modem)
			{
				$m = $this->add_modem($c, $modems_n, $modem, $km3);


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

				$mtas_n = [];
				foreach ($mtas_new->where('modem_id', $m->id)->all() as $value)
					$mtas_n[$value->mac] = $value;

				foreach ($mtas as $mta)
				{
					$mta_n = $this->add_mta($m, $mtas_n, $mta);


					/*
				 	 * Phonenumber Import
					 */
					$phonenumbers = $km3->table('tbl_mtaendpoints as e')
						->join('tbl_clis as c', 'c.endpoint', '=', 'e.id')
						->where('e.mta', '=', $mta->id)
						->where ('e.deleted', '=', 'false')
						->select('e.*', 'c.carrier')
						->get();

					$pns_n = [];
					foreach ($phonenumbers_new->where('mta_id', $mta_n->id)->all() as $pn)
						$pns_n[$pn->username] = $pn;

					foreach ($phonenumbers as $phonenumber)
						$p = $this->add_phonenumber($mta_n, $pns_n, $phonenumber);
				}
			}

			// Add Billing related Data
			$this->add_tarifs($c, $items_new, $products_new, $contract);
			$this->add_tarif_credit($c, $contract);
			$this->add_sepamandate($c, $mandates_new, $contract, $km3);
			$this->add_additional_items($c, $km3, $contract);

			$i++;
		}

		echo "\n";
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
	private function add_contract($old_contract, $contracts_new)
	{
		$c = $contracts_new->whereLoose('number', $old_contract->vertragsnummer)->first();

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
		$c->cluster 		= self::map_cluster_id($old_contract->cluster_id);
		$c->net 			= self::map_cluster_id($old_contract->cluster_id, 1);


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
	private static function map_cluster_id($cluster_id, $net = 0)
	{
		// old cluster ID => cluster ID in new System
		// TODO: Add new Cluster IDs when they exist in new system
		return self::$cluster[$cluster_id][$net];
	}


	/**
	 * Add Tarifs to corresponding Contract of new System
	 *
	 * TODO: Tarif next month can not be set as is - has still ID - Separate inet & voip tarif mappings and map all by id
	 */
	private function add_tarifs($new_contract, $items_new, $products_new, $old_contract)
	{
		$tarifs = array(
			'tarif' 			=> $old_contract->tariffname,
			'tarif_next_month'  => $old_contract->tarif_next_month,
			'voip' 				=> $old_contract->telefontarif,
			);

		foreach ($tarifs as $key => $tarif)
		{
			if (!$tarif) {
				\Log::info("\tNo $key Item exists in old System");
				continue;
			}

			$prod_id = $this->_map_tarif_to_prod($tarif);
			$item_n  = $items_new->where('contract_id', $new_contract->id)->where('product_id', $prod_id)->all();

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
	private function add_sepamandate($new_contract, $mandates_new, $old_contract, $db_con)
	{
		$mandates_n = $mandates_new->where('contract_id', $new_contract->id)->all();

		if ($mandates_n) {
			\Log::error("\tCustomer $new_contract->id already has SepaMandate assigned");
			return $this->error("\tCustomer $new_contract->id already has SepaMandate assigned");
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

		// TODO: Check if items already exist !?

		foreach ($items as $item)
		{
			if (!isset(self::$add_items[$item->id])) {
				$this->error("\tCan not map Artikel \"$item->artikel\" - ID $item->id does not exist in internal mapping table");
				\Log::error("\tCan not map Artikel \"$item->artikel\" - ID $item->id does not exist in internal mapping table");
				continue;
			}

			if ($item->id == 1 && !$item->preis)
				continue;

			\Log::info("Add Item [$new_contract->number]: $item->artikel (from: $item->von, to: $item->bis, price: $item->preis) [Old ID: $item->id]");
			$this->info("\tAdd Item [$new_contract->number]: $item->artikel (from: $item->von, to: $item->bis, price: $item->preis) [Old ID: $item->id]");

			Item::create([
				'contract_id' 		=> $new_contract->id,
				'product_id' 		=> self::$add_items[$item->id],
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
	private function add_email($new_contract, $emails_new, $old_contract)
	{
		$emails = $km3->table(\DB::raw('tbl_email'))
				->selectRaw ('*')
				->where('vertrag', '=', $old_contract->id)
				->get();

		if (count($emails) == count($emails_new->where('contract_id', $old_contract->id)->all()))
		{
			$this->error('Email Aliases already added!');
			return;
		}

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
	private function add_modem($new_contract, $new_modems, $old_modem, $db_con)
	{
		// $m = isset($modems_n[$k]) ? $modems_n[$k] : NULL;
		// dont update new modems with old data - return modem that new mtas & phonenumbers can be assigned
		if (array_key_exists($old_modem->mac_adresse, $new_modems))
		{
			$new_cm = $new_modems[$old_modem->mac_adresse];
			\Log::info("Modem already exists in new System with ID $new_cm->id!");
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
	private function add_mta($new_modem, $new_mtas, $old_mta)
	{
		// dont update new mtas with old data - return mta that new phonenumbers can be assigned
		if (array_key_exists($old_mta->mac_adresse, $new_mtas))
		{
			$new_mta = $new_mtas[$old_mta->mac_adresse];
			\Log::info("MTA already exists in new System with ID $new_mta->id!");
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
	private function add_phonenumber($new_mta, $new_phonenumbers, $old_phonenumber)
	{
		if (array_key_exists($old_phonenumber->username, $new_phonenumbers))
		{
			$new_pn = $new_phonenumbers[$old_phonenumber->username];
			\Log::info("Phonenumber already exists in new System with ID $new_pn->id!");
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
	private function add_netelements($db_con, $area_filter, $new_modems)
	{
		$devices = $db_con->table(\DB::raw('tbl_modem m, tbl_adressen a, tbl_configfiles c'))
					->selectRaw ('m.*, a.*, m.id as id, c.name as cf_name')
					->whereRaw('m.adresse = a.id')
					->whereRaw('m.configfile = c.id')
					->where ('m.deleted', '=', 'false')
					->where('m.device', '=', 9)
					// ->where('m.mac_adresse', '=', '00:d0:55:07:1d:86')
					->where($area_filter)
					->get();

		if (!$devices)
			return;

		$contract = Contract::find(500000);
		$modems_n = [];
		foreach ($new_modems->where('contract_id', 500000)->all() as $cm)
			$modems_n[$cm->mac] = $cm;

		$this->info ("ADD NETELEMENT Modems");

		foreach ($devices as $device)
			self::add_modem($contract, $modems_n, $device, $db_con);
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			// array('example', InputArgument::REQUIRED, 'An example argument.'),
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
			// array('qos', null, InputOption::VALUE_OPTIONAL, 'QOS id for default QOS, e.g. 1', 0),
			array('configfile', null, InputOption::VALUE_OPTIONAL, 'Configfile id for default Configfile, e.g. 5', 0),
			array('plz', null, InputOption::VALUE_OPTIONAL, 'Import only Contracts with special zip code (from tbl_adressen), e.g. 09518', 0),
			array('cluster', null, InputOption::VALUE_OPTIONAL, 'Import only Contracts/Modems from cluster_id, e.g. 160', 0),
			// array('debug', null, InputOption::VALUE_OPTIONAL, '1 enables debug', 0),
			array('cc', null, InputOption::VALUE_OPTIONAL, 'CostCenter ID for all the imported Contracts', 0),
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
