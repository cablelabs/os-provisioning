<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
// use Log;

use Modules\ProvBase\Entities\Contract;
use Modules\BillingBase\Entities\Item;
use Modules\BillingBase\Entities\SepaMandate;



class importTvCustomersCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nms:importTV';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'import Customers from CSV and add TV Tarif';


	/**
	 * Column Number and Description for easy Adaption
	 */
	const C_NR 			= 0;
	const C_NAME 		= 2;
	const C_STRASSE		= 4;
	const C_ZIP 		= 5;
	const C_CITY 		= 6;
	// const C_ACAD_DGR 	= 27; 		// Anrede
	const C_SALUT		= 27;		// Bemerkung
	// const C_NR_LEGACY 	= 7; 		// Alte MG-Nr
	const C_MAIL		= 26;
	const C_TEL			= 24;
	const C_DESC1		= 40;		// Watt
	const C_DESC2		= 41; 		// Zusatz
	const C_DESC3		= 3;		// Sonstiges
	const C_START		= 45; 		// Eintritt
	const C_END			= 46;		// Austritt

	// Sepa Data
	const S_REF 		= 62;
	const S_HOLDER 		= 54;
	const S_BIC 		= 35;
	const S_IBAN  		= 36;
	const S_INST 		= 10;
	const S_SIGNATURE 	= 63;
	const S_VALID 		= 29; 		// Zahlungsziel (invalid when = "14 Tage")

	// Item Data
	const TARIFF 		= 42; 		// Umlage
	const CREDIT 		= 44;		// Verstärkergeld


	/*
	 * Global Variables - need adaption for every import
	 * TODO: Change product IDs according to Database and yearly Charges according to CostCenter
	 */
	const TV_CHARGE1 	= 15; 		// Umlage 36 Euro
	const TV_CHARGE2 	= 40; 		// Umlage 66 Euro
	const PRODUCT_ID1 	= 29; 		// TV Billig
	const PRODUCT_ID2 	= 27;		// TV Teuer
	const CREDIT_ID 	= 28; 		// Credit for Amplifier


	public $important_todos = "";


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
	 * Execute the console command - Import new Contracts with TV Tariff from a CSV-File (Separator: ";")
	 *
	 * See order and description of Columns ahead defined by constants 
	 *
	 * @return mixed
	 */
	public function fire()
	{

		if ($_ENV['APP_KEY'] != 'NTh0ocCOtO0x8NU7svT7lSrD9YGlLJAJ')
			throw new \Exception('Import is not made for this Server!', 1);

		$fn = $this->argument('file');
		if (!is_file($fn))
			throw new \Exception("Can not read file [$fn]", 1);

		if (!$this->option('cc'))
			throw new \Exception("Customer Control Center has to be specified by Option --cc !", 1);

		$file_arr 	= file($fn);
		$t_year 	= date('Y-01-01');
		$num 		= count($file_arr) - 1;
		$bar 		= $this->output->createProgressBar($num);
		$i 			= 0;
		// skip headline
		// unset($file_arr[0]);
		\Log::info("Import $num Contracts for TV");

		foreach ($file_arr as $line)
		{
			$i++;
			// progress bar
			if (!$this->option('debug'))
				$bar->advance();

			$line 	= str_getcsv($line, "\t");
			$c 		= new Contract;

			// import fields
			$c->number 			= $line[self::C_NR];

			// Discard known Test Contracts
			if (in_array($c->number, [10002]))
				continue;

			$c->contract_start 	= $line[self::C_START] ? date('Y-m-d', strtotime($line[self::C_START])) : '2000-01-01';
			$c->contract_end   	= $line[self::C_END] ? date('Y-m-d', strtotime($line[self::C_END])) : null;

			$contract = Contract::where('number', '=', $c->number)->get()->all();
			if ($contract)
			{
				$contract = $contract[0];
				\Log::notice("Contract $contract->number already existing - only add TV Tarif");
				$this->_add_tarif($contract, $line[self::TARIFF], $c->contract_start);
				$this->_add_Credit($contract, $line[self::CREDIT]);
				continue;
			}

			// Discard contracts that ended last year
			if ($c->contract_end && ($c->contract_end < $t_year)) {
				\Log::info("Contract $c->number is out of date");
				continue;
			}

			$name 				= explode(',', $line[self::C_NAME]);
			$c->firstname 		= isset($name[1]) ? trim($name[1]) : trim($name[0]);
			$c->lastname 		= isset($name[1]) ? trim($name[0]) : '';
			$ret = importCommand::split_street_housenr($line[self::C_STRASSE]);
			$house_nr_strpos 	= strrpos($line[self::C_STRASSE], ' ');
			// $c->street    		= substr($line[self::C_STRASSE], 0, $house_nr_strpos);
			// $c->house_number	= substr($line[self::C_STRASSE], $house_nr_strpos);
			$c->street    		= $ret[0];
			$c->house_number	= $ret[1];
			$c->zip 			= str_pad($line[self::C_ZIP], 5, "0", STR_PAD_LEFT);
			$c->city 			= $line[self::C_CITY];
			// $c->academic_degree = $this->map_academic_degree($line[self::C_ACAD_DGR]);
			$c->salutation 		= $this->map_salutation($line[self::C_SALUT]);
			// $c->phone     		= str_replace(["/", '-', ' '], "", $line[self::C_TEL]);
			$c->phone     		= $line[self::C_TEL];
			$c->description    	= $line[self::C_DESC1]."\n".$line[self::C_DESC2]."\n".$line[self::C_DESC3];
			$c->costcenter_id 	= $this->option('cc'); 		// Dittersdorf=1
			$c->create_invoice 	= true;

			// $c->company  	= $contract->firma;
			// $c->fax      	= $contract->fax;
			$c->email    		= $line[self::C_MAIL];
			// $c->birthday 	= $contract->geburtsdatum;


			// Set null-fields to '' to fix SQL import problem with null fields
			$relations = $c->relationsToArray();
			foreach($c->toArray() as $key => $value)
			{
				if (array_key_exists($key, $relations))
					continue;

				if ($c->{$key} == null)
					$c->{$key} = '';
				
				if (is_string($c->{$key}))
					$c->{$key} = utf8_encode ($c->{$key});
			}

			$c->deleted_at = NULL;
			// Update or Create Entry
			$c->save();

			// Log
			\Log::debug("Add Contract $c->number: $c->firstname, $c->lastname");
			if ($this->option('debug'))
				$this->info ("\n$i/$num \nCONTRACT ADD: $c->id, $c->firstname, $c->lastname");


			// Add TV Tarif
			$this->_add_tarif($c, $line[self::TARIFF]);
			$this->_add_Credit($c, $line[self::CREDIT]);


			// Add Sepa Mandate
			$valid = trim($line[self::S_VALID]) == 'einzug';

			if (!$valid) {
				\Log::debug("Contract $c->number has no valid SepaMandate");
				continue;
			}

			SepaMandate::create([
				'contract_id' 		=> $c->id,
				'reference' 		=> $c->number, 
				'signature_date' 	=> date('Y-m-d', strtotime($line[self::S_SIGNATURE])),
				'sepa_holder' 		=> $line[self::S_HOLDER],
				'sepa_iban'			=> $line[self::S_IBAN],
				'sepa_bic' 			=> $line[self::S_BIC],
				'sepa_institute' 	=> $line[self::S_INST],
				'sepa_valid_from' 	=> date('Y-m-d', strtotime($line[self::S_SIGNATURE])),
				'recurring' 		=> true,
				'state' 			=> 'RECUR',
				// 'sepa_valid_to' 	=> NULL,
				]);

			\Log::debug("Add SepaMandate");
		}

		if ($this->important_todos)
			echo $this->important_todos."\n";
	}


	public function map_academic_degree($string)
	{
		if (strpos($string, 'Prof') !== false)
			return 'Prof. Dr.';

		if (strpos($string, 'Dr.') !== false)
			return 'Dr.';

		return '';
	}


	public function map_salutation($string)
	{
		if (strpos($string, 'Damen und Herren') !== false)
			return 'Firma';

		if (strpos($string, 'Herr') !== false)
			return 'Herr';

		return 'Frau';
	}


	private function _add_tarif($contract, $tariff, $start = null)
	{
		if (!$tariff) {
			\Log::debug("'Umlage' is zero or empty - don't add tariff");
			return;
		}

		$existing = false;
		if ($contract->items)
			$existing = $contract->items->contains(function($value, $item){
				return in_array($item->product_id, [self::PRODUCT_ID1, self::PRODUCT_ID2]);
			});

		if ($existing) {
			\Log::debug("Contract $contract->number already has TV Tariff assigned");
			return;
		}

		$amount = str_replace('EUR', '', $tariff);
		$amount = str_replace(',', '.', $tariff);
		$amount = (float) trim($amount);

		switch ($amount)
		{
			case  0: return;
			case self::TV_CHARGE2: $product_id = self::PRODUCT_ID2; break;
			case self::TV_CHARGE1: $product_id = self::PRODUCT_ID1; break;
			default:
				$msg = "Contract $contract->number is charged with $amount EUR. Please add Tariff manually!";
				$this->important_todos .= "\n$msg";
				\Log::warning($msg);
				return;
		}

		Item::create([
			'contract_id' 		=> $contract->id,
			'product_id' 		=> $product_id,
			'valid_from' 		=> $start ? : $contract->contract_start,
			'valid_from_fixed' 	=> 1,
			'valid_to' 			=> $contract->contract_end,
			'valid_to_fixed' 	=> 1,
			]);

		\Log::debug("Add TV Tariff $product_id for Contract $contract->number");
	}


	private function _add_Credit($contract, $credit)
	{
		if (!$credit)
			return;

		$existing = false;
		if ($contract->items)
			$existing = $contract->items->contains('product_id', self::CREDIT_ID);

		if ($existing) {
			\Log::debug("Contract $contract->number already has Credit ". self::CREDIT_ID." assigned");
			return;
		}

		$credit_amount = str_replace('EUR', '', $credit);
		$credit_amount = str_replace(',', '.', $credit);
		$credit_amount = trim($credit_amount);

		if (date('Y') == date('Y', strtotime($contract->contract_start)) || date('Y') == date('Y', strtotime($contract->contract_end)))
			$this->important_todos .= "\nPlease check Amplifier debit for Contract $contract->number as it's calculated partly for the year";

		Item::create([
			'contract_id' 		=> $contract->id,
			'product_id' 		=> self::CREDIT_ID,
			'valid_from' 		=> $contract->contract_start,
			'valid_from_fixed' 	=> 1,
			'valid_to' 			=> $contract->contract_end,
			'valid_to_fixed' 	=> 1,
			'credit_amount' 	=> $credit_amount,
			'costcenter_id' 	=> $this->option('cc'),
			]);

		\Log::debug("Add Credit $credit_amount Euro for Amplifier for Contract $contract->number");		
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('file', InputArgument::REQUIRED, 'Structured CSV FILE (-path) with Customer Data.'),
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
			array('cc', null, InputOption::VALUE_REQUIRED, 'CostCenter ID for all the imported Contracts', 0),
			array('debug', null, InputOption::VALUE_OPTIONAL, 'Print Debug Output to Commandline (1 - Yes, 0 - No (Default))', 0),
			// array('prod_id', null, InputOption::VALUE_REQUIRED, 'Product ID in Database of TV Tarif that will be assigned to all Contracts', 0),
		);
	}

}

