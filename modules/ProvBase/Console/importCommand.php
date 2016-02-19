<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Qos;
use Modules\ProvBase\Entities\Configfile;

// TODO:
//use Modules\ProvVoip\Entities\Mta;
//use Modules\ProvVoip\Entities\Phonenumber;


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


		$km3 = \DB::connection('pgsql-km3');

		/*
		 * CONTRACT Import
		 */
		$search = 'TRUE';
		if ($this->option('cluster'))
			$search = 'tbl_modem.cluster_id = '.$this->option('cluster');

		$contracts = $km3->table(\DB::raw('tbl_vertrag v, tbl_adressen a, tbl_kunde k'))
				->selectRaw ('*, v.id as id')
				->whereRaw('v.ansprechpartner = a.id')
				->whereRaw('v.kunde = k.id')
				->whereRaw('tbl_modem.vertrag = v.id')
				->where ('v.deleted', '=', 'false')
				->whereRaw ($search)->get();

		// progress bar
		$i   = 0;
		$num = sizeof($contracts);
		$bar = $this->output->createProgressBar($num);

		// foreach contract
		foreach ($contracts as $contract)
		{
			$c = Contract::where('number2', '=', $contract->vertragsnummer)->get();

			$info ='UPDATE';
			if (sizeof($c) == 0)
			{
				$info = 'ADD';
				$c = new Contract;
			}
			else
				$c = $c[0];

			// import fields
			$c->number2   = $contract->vertragsnummer;
			$c->salutation= $contract->anrede;
			$c->company   = $contract->firma;
			$c->firstname = $contract->vorname;
			$c->lastname  = $contract->nachname;
			$c->street    = $contract->strasse;
			$c->zip       = $contract->plz;
			$c->city      = $contract->ort;
			$c->phone     = $contract->tel;
			$c->fax       = $contract->fax;
			$c->email     = $contract->email;
			$c->birthday  = $contract->geburtsdatum;

			$c->description    = $contract->beschreibung;
			$c->network_access = $contract->network_access;
			$c->contract_start = $contract->angeschlossen;
			$c->contract_end   = $contract->abgeklemmt;
			
			$c->qos_id      = ($this->option('qos') == 0 ? Qos::first()->id : $this->option('qos'));
			$c->next_qos_id = 0;

			$c->voip_id      = 0; // TODO: translation table required for larger imports
			$c->next_voip_id = 0;

			$c->sepa_holder     = $contract->kontoinhaber;
			$c->sepa_iban       = $contract->iban;
			$c->sepa_bic        = $contract->bic;
			$c->sepa_institute  = $contract->institut;


			// set fields with null input to ''. 
			// This fixes SQL import problem with null fields
			foreach( $c->toArray() as $key => $value )
			{
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
			$log = "$i/$num CONTRACT $info: ".$contract->id.', '.$c->firstname.', '.$c->lastname.', '.$c->street.', '.$c->zip.', '.$c->city.', '.$c->sepa_iban;
			if ($this->option('debug'))
				$this->info ($log);


			/* 
			 * MODEM Import
			 */
			$modems = $km3->table(\DB::raw('tbl_modem as m, tbl_adressen a'))
					->selectRaw ('*, m.id as id')
					->whereRaw('m.vertrag = '.$contract->id)
					->whereRaw('m.adresse = a.id')
					->where ('m.deleted', '=', 'false')->get();

			// foreach modem
			foreach ($modems as $modem) 
			{
				$m = Modem::where('number', '=', $modem->id)->get();

				$info ='UPDATE';
				if (sizeof($m) == 0)
				{
					$info = 'ADD';
					$m = new Modem;
				}
				else
					$m = $m[0];
				

				// import fields
				$m->mac     = $modem->mac_adresse;
				$m->number  = $modem->id;

				$m->serial_num   = $modem->serial_num;
				$m->inventar_num = $modem->inventar_num;
				$m->description  = $modem->beschreibung;
				$m->network_access = $modem->network_access;

				$m->firstname = $c->firstname;
				$m->lastname  = $c->lastname;
				$m->street    = $c->street;
				$m->zip       = $c->zip;
				$m->city      = $c->city;
				$m->qos_id    = $c->qos_id;

				$m->contract_id   = $c->id;
				$m->configfile_id = ($this->option('configfile') == 0 ? Configfile::first()->id : $this->option('configfile'));


				// set fields with null input to ''. 
				// This fixes SQL import problem with null fields
				foreach( $m->toArray() as $key => $value )
				{
					$field = $m->{$key};

					if ($field == null)
						$field = '';

					$m->{$key} = $field;
				}
				$m->deleted_at = NULL;


				// SAVE
				$m->save();

				// Log
				$log = "\tMODEM $info: ".$m->id.', '.$m->mac.', QOS-'.$m->qos_id.', CF-'.$m->configfile_id.', '.$m->street.', '.$m->zip.', '.$m->city;
				if ($this->option('debug'))
					$this->info ($log);


				/*
				 * TODO: MTA Import
				 */
			}

			// progress bar
			if (!$this->option('debug'))
				$bar->advance();
			
			$i++;
		}	

		echo "\n";
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
			array('cluster', null, InputOption::VALUE_OPTIONAL, 'Import only Contracts/Modems from cluster_id, e.g. 160', 0),
			array('debug', null, InputOption::VALUE_OPTIONAL, '1 enables debug', 0),
		);
	}

}

