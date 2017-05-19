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
	 	// in GroRü verwendet - oberste nicht gemapped:
		// PrivatBasic 							4 - 25 MBit
		// Internet Volumen Basic 100000 		
		// Internet Speed 10G
		// Internet Volumen 10G - 100000
		// PrivatFlat, Internet Flat 100000, Internet Flat 6000 (inoffiziel), Internet Flat 2000, Internet Flat 16000, Internet Flat 25000, keineDaten
			'BusinessBasic' 				=> 'business',
			'BusinessFlat' 					=> 'business',
			'Industrie/Gewerbe 150' 		=> 'business',
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
			'Internet Volumen Basic SAZ' 	=> 'vol',
			'Internet Flat 6000 (inoffiziel)' => 2,
			'Internet Volumen 10G POB' 		=> 'vol',
			'Internet Volumen 10G REI' 		=> 'vol',
			'Internet Volumen 10G SAZ' 		=> 'vol',
			'Internet Flat 16000' 			=> 4,
			'Internet Speed 10G' 			=> 4,
			'Internet Volumen Basic POB' 	=> 'vol',
			'Internet Volumen Basic REI' 	=> 'vol',
			'Internet Volumen 10G - 100000' => 4,
			'Internet Flat 2000 REI' 		=> 1,
			'Internet Flat 2000 SAZ' 		=> 1,
			'Internet Flat Spar SAZ' 		=> 'inactive',
			'Internet Flat Spar MAB,POB' 	=> 'inactive',
			'Internet Flat Spar POB' 		=> 'inactive',
			'Internet Flat Spar REI' 		=> 'inactive',
			'Internet Volumen Basic 100000' => 5,
			'PrivatBasic20G' 				=> 'vol',
			'Flat 25Mbits.' 				=> 4,
			'PrivatBasic30G' 				=> 'vol',
			'PrivatBasic30G REI' 			=> 'vol',
			'Internet Flat 100000' 			=> 5,
			'Internet Flat 2000' 			=> 1,
			'Internet Flat 25000' 			=> 4,
			'Internet Speed Basic MAB,POB'  => 'vol',
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
 				// used in GroRü
				// SNMPAllowMulticast  			3  | Base
				// Arris-TG862 					42 | Arris TG862  7.0593 (funktioniert)
				// Thomson-TWG850-4 			46 | TWG 850-4
				// Default CM Config 			3  | Base
				// Thomson-THG540 				47 | THG 540
				// Thomson-THG57X-SIP 			6  | THG571
				// TVM1000-2.31 				
				// Thomson-THG541 				45 | THG 541
				// SNMPSetup1 					3  | Base
				// Arris-TM822-v9 				3  | Base
				// Arris-TM822-SIP-V9 			3  | Base
				// Thomson-THG57X 				6  | THG571 
				// Thomson-TWG850 				46 | TWG 850-4  	??
				// Fritzbox AVM 6490 06.50 		25 | FritzBox 6490
				// FritzBox 6320 				3  | Base
				// Thomson-TWG870 				44 | TWG 870
				// Thomson-THG540-SIP 			47 | THG 540
				// Thomson-TCM47X 				3  | Base
				// TC7200.20-SIP 				3  | 3
				// Thomson-THG541-SIP 			45 | THG 541
				// Fritzbox AVM 6490 			25 | FritzBox 6490
				// Arris 820 					52 | Arris CM820
				// FritzBox-6360-6360.85.06.31 	3  | Base

				// FritzBox AVM MTA 			49 | Fritzbox MTA mit 10 Nrn
				// Arris-MTA-MGCP
				// Arris-MTA-SIP 				34 | ArrisMta
				// Thomson-eMTA-SIP-EnviaTel 	20 | ThomsonTechnicolorMta
				// Default MTA Config 			20 | ThomsonTechnicolorMta
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
				'TVM1000-2.08' 				=> 'todo',
				'TVM1000-2.04'				=> 'todo',
				'Thomson-THG57X' 			=> 6,
				'Thomson-TWG870' 			=> 44,
				'Kathrein' 					=> 'todo',
				'Kathrein-DCV8400' 			=> 'todo',
				'TVM1000-2.09' 				=> 'todo',
				'X_DQOS'					=> 'todo',
				'FritzBox AVM' 				=> 25,
				'TVM1000-2.10' 				=> 'todo',
				'FritzBox 6360' 			=> 3,
				'FritzBox AVM MTA' 			=> 49,
				'Thomson-THG540-SIP' 		=> 47,
				'Kathrein-DCM42' 			=> 'todo',
				'Thomson-THG57X-SIP' 		=> 6,
				'Thomson-TWG870-SIP' 		=> 'todo', 		//46
				'TVM1000-2.20' 				=> 'todo',
				'Thomson-TCM47X' 			=> 3,
				'Thomson' 					=> 3,
				'Technicolor' 				=> 3,
				'TC7200.20' 				=> 3,
				'Delta' 					=> 'todo',
				'Arris-TG862' 				=> 42,
				'Arris' 					=> 3,
				'Hitron CVE 30360' 			=> 'todo',
				'FritzBox 6320' 			=> 3,
				'TVM1000-2.31' 				=> 'todo',
				'TC7200.20 v01.03' 			=> 3,
				'Thomson-THG541-SIP' 		=> 45,
				'Thomson-TWG850-4-SIP' 		=> 46,
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

		if ($_ENV['APP_KEY'] != 'NTh0ocCOtO0x8NU7svT7lSrD9YGlLJAJ')
		{
			$this->notice('Import is not made for this Server!');
			return;
		}

		// Get all important Data from new DB
		$contracts_new 	= Contract::all();
		$modems_new 	= Modem::all();
		$items_new 		= Item::all();
		$mtas_new 		= MTA::all();
		$phonenumbers_new = Phonenumber::all();
		$emails_new 	= Email::all();
		$mandates_new 	= SepaMandate::all();
		$products_new 	= Product::all();

		$costcenter_id  = $this->option('cc') ? : 3; 			// MAB is default

		$termination_date = NULL;
		if ($this->option('terminate'))
		{
			// check if date is valid
			if (date('Y-m-d', strtotime($this->option('terminate'))) == $this->option('terminate'))
				$termination_date = $this->option('terminate');
			else
			{
				$this->option('debug') ? $this->error('Termination Date is not a valid string. Abort!') : Log::error('Import: Abort! Termination Date is not a valid string.');
				return;
			}
		}

		/*
		 * CONTRACT Import
		 */
		$km3 = \DB::connection('pgsql-km3');

		$cluster_filter = $this->option('cluster')  ? 'tbl_modem.cluster_id = '.$this->option('cluster') : 'TRUE';
		$plz_filter 	= $this->option('plz') 		? 'a.plz = \''.$this->option('plz')."'" 			 : 'TRUE';


		// Get all Contracts with Tarifs from old systems DB
		$contracts = $km3->table(\DB::raw('tbl_vertrag v, tbl_adressen a, tbl_kunde k, tbl_tarif t, tbl_posten p'))
				->selectRaw ('distinct v.id, v.*, a.*, k.*, t.name as tarif, v.id as id, v.beschreibung as contr_descr')
				->whereRaw('v.ansprechpartner = a.id')
				->whereRaw('v.kunde = k.id')
				->whereRaw('tbl_modem.vertrag = v.id')
				->whereRaw('v.tarif = t.id')
				// ->whereRaw('v.telefontarif is null or v.telefontarif = tel.id')
				->whereRaw('t.posten_volumen_extern = p.id')
				->where ('v.deleted', '=', 'false')
				->whereRaw('(v.abgeklemmt is null or v.abgeklemmt >= \''.date('Y-m-d').'\'::date)') 		// dont import out-of-date contracts
				->whereRaw ($cluster_filter)
				->whereRaw ($plz_filter)
				->orderBy('v.id')
				->get();

		// Get all SepaMandates
		$results = $km3->table('tbl_sepamandate')->where('deleted', '=', 'false')->get();
		foreach ($results as $mandate)
			$mandates[$mandate->kunde][] = $mandate;

		unset($results, $mandate);


		// progress bar
		$i   = 1;
		$num = sizeof($contracts);
		$bar = $this->output->createProgressBar($num);


		// foreach contract
		foreach ($contracts as $contract)
		{
			$c = $contracts_new->whereLoose('number2', $contract->vertragsnummer)->first();

			$info = 'UPDATE';
			if (sizeof($c) == 0)
			{
				$info = 'ADD';
				$c = new Contract;
			}

			// import fields
			$c->number2   = $contract->vertragsnummer;
			$c->number4   = $contract->kundennr;
			$c->salutation= $contract->anrede;
			$c->company   = $contract->firma;
			$c->firstname = $contract->vorname;
			$c->lastname  = $contract->nachname;
			$c->street    = $contract->strasse;
			$c->zip       = $contract->plz;
			$c->city      = $contract->ort;
			$c->phone     = str_replace("/", "", $contract->tel);
			$c->fax       = $contract->fax;
			$c->email     = $contract->email;
			$c->birthday  = $contract->geburtsdatum;

			$c->description    = $contract->beschreibung."\n".$contract->contr_descr;
			$c->network_access = $contract->network_access;
			$c->contract_start = $contract->angeschlossen;
			$c->contract_end   = $contract->abgeklemmt;
			$c->create_invoice = $contract->einzug;

			// TODO: costcenter
			$c->costcenter_id = $costcenter_id; // Dittersdorf=1, new one would be 3


			// set fields with null input to ''. 
			// This fixes SQL import problem with null fields
			$relations = $c->relationsToArray();
			foreach( $c->toArray() as $key => $value )
			{
				if (array_key_exists($key, $relations))
					continue;

				$field = $c->{$key};

				if ($field == null)
					$field = '';
				
				if (is_string($field))
					$field = utf8_encode ($field);


				$c->{$key} = $field;
			}
			$c->deleted_at = NULL;

			// Update or Create Entry
			$c->save();

			// Log
			if ($this->option('debug'))
				$this->info ("\n$i/$num \nCONTRACT $info: ".$c->id.', '.$c->firstname.', '.$c->lastname.', '.$c->street.', '.$c->zip.', '.$c->city.', '.$c->sepa_iban);


			// terminate km3 Contract
			if ($termination_date)
			{
				$km3->table('tbl_vertrag')->where('id', '=', $contract->id)->update(['abgeklemmt' => $termination_date]);
				if ($this->option('debug'))
					$this->info("Terminated Contract $contract->id by $termination_date");
			}

			/*
			 * Add Billing related Data - Models: Items (Internet, Voip, Zusatzposten), SepaMandate
			 */
			$tarifs = array(
				'tarif' 			=> $contract->tarif,
				'tarif_next_month'  => $contract->tarif_next_month,
				'voip' 				=> $contract->telefontarif,
				);

			foreach ($tarifs as $key => $tarif)
			{
				if (!$tarif)
				{
					if ($this->option('debug'))
						$this->info("\tNo $key Item exists for Contract ".$c->id);
					continue;
				}

				$prod_id = $this->_map_tarif_to_prod($tarif);
				$item_n  = $items_new->where('contract_id', $c->id)->where('product_id', $prod_id)->all();

				if ($item_n)
				{
					if ($this->option('debug'))
						$this->info("\tItem $key for Contract ".$c->id." already exists");
					continue;
				}

				if ($prod_id <= 0)
				{
					if ($this->option('debug'))
						$this->info("\tProduct $prod_id does not exist yet");
					continue;
				}

				Item::create([
					'contract_id' 		=> $c->id,
					'product_id' 		=> $prod_id,
					'valid_from' 		=> $key == 'tarif_next_month' ? date('Y-m-01', strtotime('first day of next month')) : date('Y-m-d'), //$contract->angeschlossen,
					'valid_from_fixed' 	=> 1,
					'valid_to' 			=> $key == 'tarif_next_month' ? NULL : $c->abgeklemmt,
					'valid_to_fixed' 	=> 1,
					]);

				if ($this->option('debug'))
					$this->info ("ITEM ADD $key: ".$products_new->find($prod_id)->name.' ('.$prod_id.')');
					// TODO: Set QoS-ID -- done by daily_conversion() ??
					// $c->next_voip_id = 0;
			}

			$mandates_n = $mandates_new->where('contract_id', $c->id)->all();

			// SepaMandate
			if (isset($mandates[$contract->kunde]) && !$mandates_n)
			{
				foreach ($mandates[$contract->kunde] as $mandate)
				{
					SepaMandate::create([
						'contract_id' 		=> $c->id,
						'reference' 		=> $c->number ? : '', 			// TODO: number circle ?
						'signature_date' 	=> $mandate->datum ? : '',
						'sepa_holder' 		=> $contract->kontoinhaber ? : '',
						'sepa_iban'			=> $contract->iban ? : '',
						'sepa_bic' 			=> $contract->bic ? : '',
						'sepa_institute' 	=> $contract->institut ? : '',
						'sepa_valid_from' 	=> $mandate->datum,
						'recurring' 		=> true,
						'state' 			=> 'RECUR',
						// 'sepa_valid_to' 	=> NULL,
						]);

					if ($this->option('debug'))
						$this->info ("SEPAMANDATE ADD: ".$contract->kontoinhaber.', '.$contract->iban.', '.$contract->institut.', '.$mandate->datum);
				}
			}
			elseif ($this->option('debug'))
				isset($mandates[$contract->kunde]) ? $this->info("\tCustomer $c->id already has SepaMandate assigned") : $this->info("\tCustomer $c->id has no SepaMandate in old DB");

			// Additional Items
			// $items = $km3->table(\DB::raw('tbl_zusatzposten z, tbl_posten p'))
			// 		->selectRaw ('*, z.id as id')
			// 		->whereRaw('z.vertrag = '.$contract->id)
			// 		->whereRaw('z.posten = p.id')
			// 		->where('z.closed', '=', 'false')
			// 		->whereRaw('z.bis > \''.date('Y-m-d').'\'::date or z.bis is null')
			// 		->get();


			// TODO: not important for GroRü
			// foreach ($items as $item)
			// {
			// }


			/*
			 * Email Import
			 */
			// $emails = $km3->table(\DB::raw('tbl_email'))
			// 		->selectRaw ('*')
			// 		->where('vertrag', '=', $contract->id)
			// 		->get();

			// if (count($emails) != count($emails_new->where('contract_id', $contract->id)->all()))
			// {
			// 	foreach ($emails as $email)
			// 	{
			// 		Email::create([
			// 			'contract_id' 	=> $c->id,
			// 			'localpart' 	=> $email->alias,
			// 			'password' 		=> $email->passwort,
			// 			'blacklisting' 	=> $email->blacklisting,
			// 			'greylisting' 	=> $email->greylisting,
			// 			'forwardto' 	=> $email->forwardto ? : '',
			// 			]);
			// 	}

			// 	// Log
			// 	if ($this->option('debug'))
			// 		$this->info ("MAIL: Added ".count($emails).' Addresses');
			// }



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

			foreach ($tmp as $key => $value)
				$modems_n[] = $value;

			$tmp = [];


			foreach ($modems as $k => $modem)
			{
				$m = isset($modems_n[$k]) ? $modems_n[$k] : NULL;

				$info ='UPDATE';
				if (sizeof($m) == 0)
				{
					$info = 'ADD';
					$m = new Modem;
				}
				
				// import fields
				$m->mac     = $modem->mac_adresse;
				$m->number  = $modem->id;

				$m->serial_num   = $modem->serial_num;
				$m->inventar_num = $modem->inventar_num;
				$m->description  = $modem->beschreibung;
				$m->network_access = $modem->network_access;

				$m->x = $modem->x;
				$m->y = $modem->y;

				$m->firstname = $c->firstname;
				$m->lastname  = $c->lastname;
				$m->street    = $c->street;
				$m->zip       = $c->zip;
				$m->city      = $c->city;
				$m->qos_id    = $c->qos_id;

				$m->contract_id   = $c->id;
				$m->configfile_id = isset($this->configfiles[$modem->cf_name]) && is_int($this->configfiles[$modem->cf_name]) ? $this->configfiles[$modem->cf_name] : 0;
				// $m->configfile_id = ($this->option('configfile') == 0 ? Configfile::first()->id : $this->option('configfile'));


				// set fields with null input to ''. 
				// This fixes SQL import problem with null fields
				$relations = $m->relationsToArray();
				foreach( $m->toArray() as $key => $value )
				{
					if (array_key_exists($key, $relations))
						continue;

					$field = $m->{$key};

					if ($field == null)
						$field = '';

					$m->{$key} = $field;
				}
				$m->deleted_at = NULL;

				// SAVE
				$m->save();

				// Log
				if ($m->configfile_id == 0)
					Log::warning('No Configfile could be assignet to Modem '.$m->id);

				if ($this->option('debug'))
					$this->info ("MODEM $info: ".$m->id.', '.$m->mac.', QOS-'.$m->qos_id.', CF-'.$m->configfile_id.', '.$m->street.', '.$m->zip.', '.$m->city);


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

					$info ='UPDATE';
					if (sizeof($mta_n) == 0)
					{
						$info = 'ADD';
						$mta_n = new MTA;
					}
					// else
					// 	$mta_n = $mta_n[0];

					$mta_n->modem_id 	= $m->id;
					$mta_n->mac 		= $mta->mac_adresse;
					$mta_n->configfile_id = isset($this->configfiles[$mta->configfile]) && is_int($this->configfiles[$mta->configfile]) ? $this->configfiles[$mta->configfile] : 0;
					$mta_n->type = 'sip';

					$mta_n->save();

					// Log
					if ($this->option('debug'))
						$this->info ("MTA $info: ".$mta_n->id.', '.$mta_n->mac.', CF-'.$mta_n->configfile_id);

					/*
					 * Phonenumber Import
					 */
					$phonenumbers = $km3->table(\DB::raw('tbl_mtaendpoints e'))
						->where('e.mta', '=', $mta->id)
						->where ('e.deleted', '=', 'false')
						->get();

					$tmp = $phonenumbers_new->where('mta_id', $mta_n->id)->all();

					foreach ($tmp as $key => $value)
						$pns_n[] = $value;

					$tmp = [];

					foreach ($phonenumbers as $k => $phonenumber)
					{
						$p = isset($pns_n[$k]) ? $pns_n[$k] : NULL;

						$info ='UPDATE';
						if (sizeof($p) == 0)
						{
							$info = 'ADD';
							$p = new Phonenumber;
						}
						// else
						// 	$p = $p[0];

						$p->mta_id 			= $mta_n->id;
						$p->port 			= $phonenumber->port;
						$p->country_code 	= '0049';
						$p->prefix_number 	= $phonenumber->vorwahl;
						$p->number 			= $phonenumber->rufnummer;
						$p->username 		= $phonenumber->username;
						$p->password 		= $phonenumber->password;
						$p->active 			= true;  		// $phonenumber->aktiv; 		most phonenrs are marked as inactive because of automatic controlling

						$p->save();

						// Log
						if ($this->option('debug'))
							$this->info ("Phonenumber $info: ".$p->id.', '.$mta_n->id.', '.$p->country_code.$p->prefix_number.$p->number.', '.($phonenumber->aktiv ? 'active' : 'inactive (but currently set fix to active)'));

					}
				}
			}

			// progress bar
			if (!$this->option('debug'))
				$bar->advance();
			
			$i++;
		}	

		echo "\n";
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
			array('debug', null, InputOption::VALUE_OPTIONAL, '1 enables debug', 0),
			array('cc', null, InputOption::VALUE_OPTIONAL, 'CostCenter ID for all the imported Contracts', 0),
			array('terminate', null, InputOption::VALUE_OPTIONAL, 'Date for all km3 Contracts to terminate', 0),
		);
	}

}

