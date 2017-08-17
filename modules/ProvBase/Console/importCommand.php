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

	// TODO: Check every new import if new tarifs exist on old system
	protected $old_sys_inet_tarifs = [
			'BusinessBasic' 				=> 'business',
			'BusinessFlat' 					=> 'business',
			'Industrie/Gewerbe 150' 		=> 22,
			'Industrie/Gewerbe 145' 		=> 'business',
			'Internet Flat 6000 REI' 		=> 2,
			'Pob_PrivatBasic' 				=> 'vol',
			'Pob_PrivatBasic10G' 			=> 'vol',
			'Pob_PrivatBasic5G' 			=> 'vol',
			'Pob_PrivatFlat' 				=> 'vol',
			'Pob_PrivatFlat Spar' 			=> 'vol',
			'keineDaten' 					=> 0,
			'PrivatBasic' 					=> 4,
			'PrivatBasic 10G' 				=> 4,
			'PrivatFlat Spar' 				=> 1,
			'PrivatFlat' 					=> 1,
			'Keine Daten' 					=> 0,
			'PrivatBasic 5G' 				=> 'vol',
			'Internet Flat 16000 REI' 		=> 4,
			'Internet Flat 6000 SAZ' 		=> 2,
			'Internet Flat 16000 SAZ' 		=> 4,
			'Internet Flat 6000 POB' 		=> 2,
			'Internet Flat 16000 POB' 		=> 4,
			'Internet Flat 2000 POB' 		=> 1,
			'Internet Volumen Basic SAZ' 	=> 4,
			'Internet Flat 6000 (inoffiziel)' => 2,
			'Internet Volumen 10G POB' 		=> 'vol',
			'Internet Volumen 10G REI' 		=> 'vol',
			'Internet Volumen 10G SAZ' 		=> 'vol',
			'Internet Flat 16000' 			=> 4,
			'Internet Speed 10G' 			=> 4,
			'Internet Volumen Basic POB' 	=> 4,
			'Internet Volumen Basic REI' 	=> 4,
			'Internet Volumen 10G - 100000' => 4,
			'Internet Flat 2000 REI' 		=> 1,
			'Internet Flat 2000 SAZ' 		=> 1,
			'Internet Flat Spar SAZ' 		=> 'inactive',
			'Internet Flat Spar MAB,POB' 	=> 'inactive',
			'Internet Flat Spar POB' 		=> 'inactive',
			'Internet Flat Spar REI' 		=> 'inactive',
			'Internet Volumen Basic 100000' => 4,
			'PrivatBasic20G' 				=> 'vol',
			'Flat 25Mbits.' 				=> 4,
			'PrivatBasic30G' 				=> 'vol',
			'PrivatBasic30G REI' 			=> 'vol',
			'Internet Flat 100000' 			=> 5,
			'Internet Flat 2000' 			=> 1,
			'Internet Flat 25000' 			=> 4,
			'Internet Speed Basic MAB,POB'  => 4,
 		];

		protected $old_sys_voip_tarifs = array(
		12464 => 6,		// TelefonieBasic
		12465 => 6,		// TelefonieBasicData
		12466 => 7,		// TelefonieFlatData
		12965 => 7,		// TelefonieFlat
		22668 => 6,		// Rei_TelefonieBasic
		22669 => 6,		// Rei_TelefonieBasicData
		22671 => 7,		// Rei_TelefonieFlatData
		24057 => 6,		// Saz_TelefonieBasic
		24058 => 6,		// Saz_TelefonieBasicData
		24059 => 7,		// Saz_TelefonieFlat
		24060 => 7,		// Saz_TelefonieFlatData
		17663 => 6,		// Pob_TelefonieBasic
		17664 => 6,		// Pob_TelefonieBasicData
		17665 => 7,		// Pob_TelefonieFlat
		17666 => 7,		// Pob_TelefonieFlatData
		22670 => 7,		// Rei_TelefonieFlat
			);

	protected $configfiles = array(
			'SNMPSetup1' 				=> 3,
			'SNMPBlockMulticast' 		=> 3,
			'SNMPAllowMulticast' 		=> 3,
			'SNMPSetup-TEST-FW-1' 		=> 3,
			'SNMPSetup-TEST-FW-2' 		=> 3,
			'SNMPTVM' 					=> 3,
			'SNMPSIP' 					=> 3,
			'SNMPSetupUpdate' 			=> 3,
			'Thomson-THG540' 			=> 47,
			'Thomson-THG541' 			=> 45,
			'Thomson-TWG850' 			=> 46,
			'Thomson-TWG850-4' 			=> 46,
			'Default MTA Config' 		=> 20,
			'Thomson/Technicolor' 		=> 3,
			'Default CM Config' 		=> 3,
			'TVM1000' 					=> 'todo',
			'TVM1000-2.08' 				=> 63,
			'TVM1000-2.04'				=> 'todo',
			'Thomson-THG57X' 			=> 6,
			'Thomson-TWG870' 			=> 44,
			'Kathrein' 					=> 'todo',
			'Kathrein-DCV8400' 			=> 61,
			'TVM1000-2.09' 				=> 'todo',
			'X_DQOS'					=> 'todo',
			'FritzBox AVM' 				=> 25,
			'TVM1000-2.10' 				=> 'todo',
			'FritzBox 6360' 			=> 3,
			'FritzBox AVM MTA' 			=> 49,
			'Thomson-THG540-SIP' 		=> 47,
			'Kathrein-DCM42' 			=> 3,
			'Thomson-THG57X-SIP' 		=> 6,
			'Thomson-TWG870-SIP' 		=> 44,
			'TVM1000-2.20' 				=> 'todo',
			'Thomson-TCM47X' 			=> 3,
			'Thomson' 					=> 3,
			'Technicolor' 				=> 3,
			'TC7200.20' 				=> 3,
			'Delta' 					=> 3,
			'Arris-TG862' 				=> 42,
			'Arris' 					=> 3,
			'Hitron CVE 30360' 			=> 'todo',
			'FritzBox 6320' 			=> 3,
			'TVM1000-2.31' 				=> 'todo',
			'TC7200.20 v01.03' 			=> 3,
			'Thomson-THG541-SIP' 		=> 45,
			'Thomson-TWG850-SIP' 		=> 46,
			'Thomson-TWG850-4-SIP' 		=> 46,
			'Thomson-TWG870-SIP' 		=> 44,
			'Hitron BVG 3653 SIP' 		=> 'todo',
			'6320v2' 					=> 3,
			'Fritzbox AVM 6490' 		=> 25,
			'6320v2int' 				=> 3,
			'AVM 6490 V.06.51' 			=> 25,
			'AVM Basis Test 6340' 		=> 3,
			'Arris-TG862 SIP' 			=> 42,
			'Tarris' 					=> 42,
			'Tarris-TG862 SIP' 			=> 42,
			'Tarris_MTA' 				=> 34,
			'Arris-TG862 test neueste Firmware' => 53, // TG862 9.01103S5E1
			'Hitron eMTA' 				=> 'todo',
			'FritzBox-6360-6360.85.06.31' => 3,
			'Arris-TG862-v9' 			=> 53,
			'Arris-TG862-v9 sip' 		=> 53,
			'Thomson-eMTA-SIP-EnviaTel' => 20,
			'Fritzbox AVM 6490 06.50' 	=> 25,
			'TC7200.20-SIP' 			=> 3,
			'Fritz Box 6360.85.06.50' 	=> 3,
			'Arris 820' 				=> 52,
			'Arris-MTA-MGCP'			=> 'todo',
			'Arris-TM822-v9' 			=> 3,
			'FritzBoxAVM-6360 (Ver. 06.51) (SNMP)' => 3,
			'FritzBoxAVM-6490 (Ver. 06.51) (SNMP)' => 25,
			'AVM 6.50 - Test' 			=> 25,
			'Arris-TM822-SIP-V9' 		=> 3,
			'Arris-MTA-SIP' 			=> 34,
		);


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
	 * TODO / Note
	 *  - no qos import -> uses default qos, see options
	 *  - no voip contract id import
	 *  - no MTA / Phone import
	 *
	 * @return mixed
	 *
	 */
	public function fire()
	{
		// Pre - Testing
		if (!Qos::first())
		{
			$this->error('no QOS entry exists to use');
			return;
		}

		if (!Configfile::first())
		{
			$this->error('no configfile entry exists to use');
			return;
		}

		if (env('APP_KEY') != 'NTh0ocCOtO0x8NU7svT7lSrD9YGlLJAJ')
		{
			$this->error('Import is not made for this Server!');
			return;
		}

		// Get all important Data from new DB
		$contracts_new 	= Contract::all();
		$modems_new 	= Modem::all();
		$items_new 		= Item::all();
		$mtas_new 		= MTA::all();
		$phonenumbers_new = Phonenumber::all();
		$mandates_new 	= SepaMandate::all();
		$products_new 	= Product::all();

		if (\PPModule::is_active('mail'))
			$emails_new = Email::all();


		// $termination_date = NULL;
		// if ($this->option('terminate'))
		// {
		// 	// check if date is valid
		// 	if (date('Y-m-d', strtotime($this->option('terminate'))) == $this->option('terminate'))
		// 		$termination_date = $this->option('terminate');
		// 	else {
		// 		$this->error('Termination Date is not a valid string. Abort!');
		// 		return;
		// 	}
		// }


		// Connect to old Database
		$km3 = \DB::connection('pgsql-km3');


		/*
		 * CONTRACT Import
		 */
		$cluster_filter = $this->option('cluster')  ? 'm.cluster_id = '.$this->option('cluster') : 'TRUE';
		$plz_filter 	= $this->option('plz') 		? 'modem_adr.plz = \''.$this->option('plz')."'" : 'TRUE';

		// Get all Contracts with where modem adress is inside the specified area 
		// Get customer data & Tarifname from old systems DB
		$contracts = $km3->table(\DB::raw('tbl_vertrag v, tbl_modem m, tbl_adressen a, tbl_adressen modem_adr, tbl_kunde k, tbl_tarif t, tbl_posten p'))
				->selectRaw ('distinct on (v.vertragsnummer) v.vertragsnummer, v.*, a.*, k.*, t.name as tarif,
					v.id as id, v.beschreibung as contr_descr, m.cluster_id')
				->whereRaw('m.adresse = modem_adr.id')
				->whereRaw('v.ansprechpartner = a.id')
				->whereRaw('v.kunde = k.id')
				->whereRaw('m.vertrag = v.id')
				->whereRaw('v.tarif = t.id')
				->whereRaw('t.posten_volumen_extern = p.id')

				->where ('v.deleted', '=', 'false')
				->where ('m.deleted', '=', 'false')
				->whereRaw('(v.abgeklemmt is null or v.abgeklemmt >= \''.date('Y-m-d').'\'::date)') 		// dont import out-of-date contracts

				->whereRaw ($cluster_filter)
				->where(function ($query) use ($plz_filter) { $query
					->whereRaw ($plz_filter)
					->where ('modem_adr.ort', '!=', 'Mooshaide')
					->where ('modem_adr.ort', '!=', 'Lauterbach')
					->whereRaw ('modem_adr.strasse not like \'Bussardw%\'')
					->whereRaw ('modem_adr.strasse not like \'Alte Annab%\'')
					->whereRaw ('modem_adr.strasse not like \'Wolkensteiner%\'')
					->whereRaw ('modem_adr.strasse not like \'Mooshaid%\'')
					->whereRaw ('modem_adr.strasse not like \'Falkenw%\'')
					->whereRaw ('modem_adr.strasse not like \'Habichtw%\'')
					->orWhere ('v.vertragsnummer', '=', 43851);
					})
				->orderBy('v.vertragsnummer')
				->get();

		// progress bar
		$i   = 1;
		$num = sizeof($contracts);
		$bar = $this->output->createProgressBar($num);

		foreach ($contracts as $contract)
		{
			$c = $this->add_contract($contract, $contracts_new);

			if (!$c) {
				$i++;
				continue;
			}

			$this->info ("\n$i/$num \nCONTRACT ADDED: ".$c->id.', '.$c->firstname.', '.$c->lastname.', '.$c->street.', '.$c->zip.', '.$c->city);

			// terminate km3 Contract
			// if ($termination_date)
			// {
			// 	$km3->table('tbl_vertrag')->where('id', '=', $contract->id)->update(['abgeklemmt' => $termination_date]);
			// 	$this->info("Terminated Contract $contract->id by $termination_date");
			// }


			// Add Billing related Data
			self::add_tarifs($c, $items_new, $products_new, $contract);
			self::add_sepamandate($c, $mandates_new, $contract, $km3);
			self::add_additional_items();

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

			$tmp = $modems_new->where('contract_id', $c->id)->all();

			$modems_n = [];

			foreach ($tmp as $cm)
				$modems_n[] = $cm;

			$tmp = [];

			foreach ($modems as $k => $modem)
			{
				$m = isset($modems_n[$k]) ? $modems_n[$k] : NULL;
				$m = $this->add_modem($c, $m, $modem);

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

				$tmp = $mtas_new->where('modem_id', $m->id)->all();

				foreach ($tmp as $key => $value)
					$mtas_n[] = $value;

				$tmp = [];
				foreach ($mtas as $k => $mta)
				{
					$mta_n = isset($mtas_n[$k]) ? $mtas_n[$k] : NULL;
					$mta_n = $this->add_mta($m, $mta_n, $mta);

					/*	
				 	 * Phonenumber Import
					 */
					$phonenumbers = $km3->table(\DB::raw('tbl_mtaendpoints e'))
						->where('e.mta', '=', $mta->id)
						->where ('e.deleted', '=', 'false')
						->get();

					$tmp = $phonenumbers_new->where('mta_id', $mta_n->id)->all();

					$pns_n = [];

					foreach ($tmp as $pn)
						$pns_n[] = $pn;

					$tmp = [];

					foreach ($phonenumbers as $k => $phonenumber)
					{
						$p = isset($pns_n[$k]) ? $pns_n[$k] : NULL;
						$p = $this->add_phonenumber($mta_n, $p, $phonenumber);
					}
				}
			}

			// progress bar
			// if (!$this->option('debug'))
			// 	$bar->advance();
			
			$i++;
			$new_contracts[] = $c;
		}	

		echo "\n";

		// Update QoS-ID of Modems - we could alternatively add Items here and this should solve the problem,
		// but with the current daily-conversion it's very unsecure
		foreach ($new_contracts as $cont) {
			$cont->push_to_modems();
		}
	}


	/**
	 * Add Contract Data
	 *
	 * @param 	old_contract 		Object 		Contract from old DB
	 * @param 	new_contracts 		Array 		All existing Contracts of new DB
	 */
	private function add_contract($old_contract, $contracts_new)
	{
		$c = $contracts_new->whereLoose('number2', $old_contract->vertragsnummer)->first();

		$info = 'ADDED';
		if (sizeof($c) != 0)
		{
			// $info = 'UPDATE';
			$this->error("Contract $c->vertragsnummer already exists [$c->id]");
			return null;
		}
		
		$c = new Contract;

		// import fields
		$c->number2 		= $old_contract->vertragsnummer;
		$c->number4 		= $old_contract->kundennr;
		$c->salutation 		= $old_contract->anrede;
		$c->company 		= $old_contract->firma;
		$c->firstname 		= $old_contract->vorname;
		$c->lastname 		= $old_contract->nachname;

		// extract last number from street
		preg_match('/(\d+)(?!.*\d)/', $old_contract->strasse, $matches);
		$matches = $matches ? $matches[0] : '';
		$c->street 			= trim(str_replace($matches, '', $old_contract->strasse));
		$c->house_number 	= $matches;

		$c->zip 			= $old_contract->plz;
		$c->city 			= $old_contract->ort;
		$c->phone 			= str_replace("/", "", $old_contract->tel);
		$c->fax 			= $old_contract->fax;
		$c->email 			= $old_contract->email;
		$c->birthday 		= $old_contract->geburtsdatum;

		$c->description 	= $old_contract->beschreibung."\n".$old_contract->contr_descr;
		$c->network_access 	= $old_contract->network_access;
		$c->contract_start 	= $old_contract->angeschlossen;
		$c->contract_end   	= $old_contract->abgeklemmt;
		$c->create_invoice 	= $old_contract->einzug;

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
	 		{
	 			Log::error('importCommand: contract has unknown voip tarif');
	 			return -1;
	 		}

	 		return $this->old_sys_voip_tarifs[$tarif];

		}

		// Inet
		if (!array_key_exists($tarif, $this->old_sys_inet_tarifs))
		{
			Log::error('importCommand: contract has unknown tarif');
			return -1;
		}

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
		$cluster = array(
			33382 => [null, 3],			// Mbg-Stadt-Gebirge-Dampf
			33383 => [32, 3],			// Mbg-Edeka
			33384 => [null, 3],			// Mbg-NL-LB-HG-LF
			36385 => [null, 3],			// Pobershau
			36464 => [null, 3],			// Olb-Reitz-Rueb
			36546 => [null, 3],			// Satzung
			36821 => [null, 3],			// Khnhaide
			41298 => [null, 3],			// Wolk-Gehr-Strw
			);

		return $cluster[$cluster_id][$net];
	}


	/**
	 * Add Tarifs to corresponding Contract of new System
	 */
	private function add_tarifs($new_contract, $items_new, $products_new, $old_contract)
	{
		$tarifs = array(
			'tarif' 			=> $old_contract->tarif,
			'tarif_next_month'  => $old_contract->tarif_next_month,
			'voip' 				=> $old_contract->telefontarif,
			);

		foreach ($tarifs as $key => $tarif)
		{
			if (!$tarif)
			{
				$this->info("\tNo $key Item exists in old System");
				continue;
			}

			$prod_id = $this->_map_tarif_to_prod($tarif);
			$item_n  = $items_new->where('contract_id', $new_contract->id)->where('product_id', $prod_id)->all();

			if ($item_n)
			{
				$this->error("\tItem $key for Contract ".$new_contract->id." already exists");
				continue;
			}

			if ($prod_id <= 0)
			{
				$this->error("\tProduct $prod_id does not exist yet [$tarif]");
				continue;
			}

			Item::create([
				'contract_id' 		=> $new_contract->id,
				'product_id' 		=> $prod_id,
				'valid_from' 		=> $key == 'tarif_next_month' ? date('Y-m-01', strtotime('first day of next month')) : date('Y-m-d'), //$contract->angeschlossen,
				'valid_from_fixed' 	=> 1,
				'valid_to' 			=> $key == 'tarif_next_month' ? NULL : $new_contract->abgeklemmt,
				'valid_to_fixed' 	=> 1,
				]);

			$this->info ("ITEM ADD $key: ".$products_new->find($prod_id)->name.' ('.$prod_id.')');
			// TODO: Set QoS-ID -- done by daily_conversion() ??
			// $c->next_voip_id = 0;
		}
	}


	/**
	 * Add SepaMandate to corresponding Contract
	 */
	private function add_sepamandate($new_contract, $mandates_new, $old_contract, $db_con)
	{
		$mandates_n = $mandates_new->where('contract_id', $new_contract->id)->all();

		if ($mandates_n)
		{
			$this->error("\tCustomer $new_contract->id already has SepaMandate assigned");
			return;
		}

		$mandates_old = $db_con->table(\DB::raw('tbl_sepamandate s, tbl_lastschriftkonten l'))
			 	->selectRaw ('s.*, l.*, l.id as id')
			 	->whereRaw('s.id = l.sepamandat')
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
				'sepa_holder' 		=> $mandate->kontoinhaber ? : '',
				'sepa_iban'			=> $mandate->iban ? : '',
				'sepa_bic' 			=> $mandate->bic ? : '',
				'sepa_institute' 	=> $mandate->institut ? : '',
				'sepa_valid_from' 	=> $mandate->gueltig_ab,
				'sepa_valid_to' 	=> $mandate->gueltig_bis,
				'recurring' 		=> true,
				'state' 			=> 'RECUR',
				// 'sepa_valid_to' 	=> NULL,
				]);

			$this->info ("SEPAMANDATE ADD: ".$mandate->kontoinhaber.', '.$mandate->iban.', '.$mandate->institut.', '.$mandate->datum);
		}
	}

	/**
	 * TODO
	 */
	private function add_additional_items()
	{
		// Additional Items
		// $items = $km3->table(\DB::raw('tbl_zusatzposten z, tbl_posten p'))
		// 		->selectRaw ('*, z.id as id')
		// 		->whereRaw('z.vertrag = '.$contract->id)
		// 		->whereRaw('z.posten = p.id')
		// 		->where('z.closed', '=', 'false')
		// 		->whereRaw('z.bis > \''.date('Y-m-d').'\'::date or z.bis is null')
		// 		->get();

		// TODO: not important for GroRü
		// foreach ($items as $item) {
		// }
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
	 */
	private function add_modem($new_contract, $new_modem, $old_modem)
	{
		$info ='UPDATE';
		if (!$new_modem)
		{
			$info = 'ADDED';
			$new_modem = new Modem;
		}

		// import fields
		$new_modem->mac     = $old_modem->mac_adresse;
		$new_modem->number  = $old_modem->id;
		$new_modem->name 	= $old_modem->name;

		$new_modem->serial_num   = $old_modem->serial_num;
		$new_modem->inventar_num = $old_modem->inventar_num;
		$new_modem->description  = $old_modem->beschreibung;
		$new_modem->network_access = $old_modem->network_access;

		$new_modem->x = $old_modem->x;
		$new_modem->y = $old_modem->y;

		$new_modem->firstname = $new_contract->firstname;
		$new_modem->lastname  = $new_contract->lastname;
		$new_modem->street    = $new_contract->street;
		$new_modem->zip       = $new_contract->zip;
		$new_modem->city      = $new_contract->city;
		$new_modem->qos_id    = $new_contract->qos_id;

		$new_modem->contract_id   = $new_contract->id;
		$new_modem->configfile_id = isset($this->configfiles[$old_modem->cf_name]) && is_int($this->configfiles[$old_modem->cf_name]) ? $this->configfiles[$old_modem->cf_name] : 0;
		// $new_modem->configfile_id = ($this->option('configfile') == 0 ? Configfile::first()->id : $this->option('configfile'));


		// set fields with null input to ''. 
		// This fixes SQL import problem with null fields
		$relations = $new_modem->relationsToArray();
		foreach( $new_modem->toArray() as $key => $value )
		{
			if (array_key_exists($key, $relations))
				continue;

			$new_modem->{$key} = $new_modem->{$key} ? : '';
		}
		$new_modem->deleted_at = NULL;

		// SAVE
		$new_modem->save();

		// Output
		if ($new_modem->configfile_id == 0)
			$this->error('No Configfile could be assigned to Modem '.$new_modem->id." Old ModemID: $old_modem->id");

		$this->info ("MODEM $info: ".$new_modem->id.', '.$new_modem->mac.', QOS-'.$new_modem->qos_id.', CF-'.$new_modem->configfile_id.', '.$new_modem->street.', '.$new_modem->zip.', '.$new_modem->city);

		return $new_modem;
	}


	/**
	 * Add MTA to corresponding Modem of new System
	 */
	private function add_mta($new_modem, $new_mta, $old_mta)
	{
		$info ='UPDATE';
		if (sizeof($new_mta) == 0)
		{
			$info = 'ADD';
			$new_mta = new MTA;
		}
		// else
		// 	$new_mta = $new_mta[0];

		$new_mta->modem_id 	= $new_modem->id;
		$new_mta->mac 		= $old_mta->mac_adresse;
		$new_mta->configfile_id = isset($this->configfiles[$old_mta->configfile]) && is_int($this->configfiles[$old_mta->configfile]) ? $this->configfiles[$old_mta->configfile] : 0;
		$new_mta->type = 'sip';

		$new_mta->save();

		// Log
		$this->info ("MTA $info: ".$new_mta->id.', '.$new_mta->mac.', CF-'.$new_mta->configfile_id);

		return $new_mta;
	}

	/**
	 * Add Phonenumber to corresponding MTA
	 */
	private function add_phonenumber($new_mta, $new_phonenumber, $old_phonenumber)
	{
		$info ='UPDATE';
		if (sizeof($new_phonenumber) == 0)
		{
			$info = 'ADD';
			$new_phonenumber = new Phonenumber;
		}
		// else
		// 	$new_phonenumber = $new_phonenumber[0];

		$new_phonenumber->mta_id 		= $new_mta->id;
		$new_phonenumber->port 			= $old_phonenumber->port;
		$new_phonenumber->country_code 	= '0049';
		$new_phonenumber->prefix_number = $old_phonenumber->vorwahl;
		$new_phonenumber->number 		= $old_phonenumber->rufnummer;
		$new_phonenumber->username 		= $old_phonenumber->username;
		$new_phonenumber->password 		= $old_phonenumber->password;
		$new_phonenumber->active 		= true;  		// $old_phonenumber->aktiv; 		most phonenrs are marked as inactive because of automatic controlling

		$new_phonenumber->save();

		// Log
		$this->info ("Phonenumber $info: ".$new_phonenumber->id.', '.$new_mta->id.', '.$new_phonenumber->country_code.$new_phonenumber->prefix_number.$new_phonenumber->number.', '.($old_phonenumber->aktiv ? 'active' : 'inactive (but currently set fix to active)'));

		return $new_phonenumber;
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
			array('qos', null, InputOption::VALUE_OPTIONAL, 'QOS id for default QOS, e.g. 1', 0),
			array('configfile', null, InputOption::VALUE_OPTIONAL, 'Configfile id for default Configfile, e.g. 5', 0),
			array('plz', null, InputOption::VALUE_OPTIONAL, 'Import only Contracts with special zip code (from tbl_adressen), e.g. 09518', 0),
			array('cluster', null, InputOption::VALUE_OPTIONAL, 'Import only Contracts/Modems from cluster_id, e.g. 160', 0),
			// array('debug', null, InputOption::VALUE_OPTIONAL, '1 enables debug', 0),
			array('cc', null, InputOption::VALUE_OPTIONAL, 'CostCenter ID for all the imported Contracts', 0),
			// array('terminate', null, InputOption::VALUE_OPTIONAL, 'Date for all km3 Contracts to terminate', 0),
		);
	}

}

