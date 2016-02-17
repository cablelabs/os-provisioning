<?php namespace Modules\Provvoip\Console;

use Log;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use \Modules\ProvVoip\Entities\CarrierCode;

/**
 * Class for updating database with carrier codes from csv file
 */
class CarrierCodeDatabaseUpdaterCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'provvoip:update_carrier_code_database';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update the database carrier code using the csv file $csv_file';


	// the path were the csv file
	protected $csv_file = '/etc/nms/provvoip/carrier_codes.csv';

	// where to store the hash file for csv comparation
	// this path is later used by \Storage::…
	protected $hash_file = 'modules/ProvVoip/carrier_codes__sha1sum';


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
	 * @return mixed
	 */
	public function fire()
	{

		// nothing to do
		if (!$this->_have_to_update()) {
			return;
		}

		// if csv in unreadable => do nothing
		if (!$this->_read_csv()) {
			return;
		}

		// all OK? update now!
		$this->_update_table();
	}


	/**
	 * Checks if the database has to be updated.
	 * This is a pessimistic check;
	 *
	 * @author Patrick Reichel
	 *
	 * @return True if database has to be updated, False else
	 */
	protected function _have_to_update() {

		// if there is no csv file, we can't update the db…
		if (!is_readable($this->csv_file)) {
			Log::warning($this->name.': CSV file with carrier codes does not exist ('.$this->csv_file.')');
			return false;
		}

		// if there is no hash file: update db (in worst case we don't change something)
		if (!\Storage::has($this->hash_file)) {
			return true;
		}

		// check if current csv's checksum differes from the last version
		$cur_hash  = sha1_file($this->csv_file);
		$last_hash = \Storage::get($this->hash_file);

		if ($cur_hash == $last_hash) {
			Log::debug($this->name.': CSV file has not changed; no database update necessary');
			return false;
		}

		return true;
	}


	/**
	 * Helper to read csv file to array
	 *
	 * @author Patrick Reichel
	 *
	 * @return True if csv file could be read, else false
	 */
	protected function _read_csv() {

		try {
			$this->csv = array_map('str_getcsv', file($this->csv_file));
		}
		catch (Exception $ex) {
			Log:error($this->name.': Something went wrong reading CSV file ('.$this->csv_file.') => '.$ex->getMessage());
			return false;
		}

		return true;
	}


	/**
	 * Updates the database table with CSV values.
	 *
	 * The only actions we perform is adding new and updating existing carrier codes.
	 * The deletion of not contained carrier codes would IMO be a bad idea:
	 *     - we lose data if something went wrong in building the csv
	 *     - some entries in e.g. older phonenumbers refer to non existing (=soft deleted) entries
	 *     - it general should be no problem to hold the old codes – they simply will not be used anymore in future actions
	 *
	 * @author Patrick Reichel
	 */
	protected function _update_table() {

		foreach ($this->csv as $key => $entry) {
			$code = $entry[0];
			$company = $entry[1];

			$cc = CarrierCode::firstOrNew(array('carrier_code' => $code));
			$cc->carrier_code = $code;
			$cc->company = $company;
			$cc->save();
		}

		$hash = sha1_file($this->csv_file);
		\Storage::put($this->hash_file, $hash);
		Log::info($this->name.': Database carriercodes updated');

	}

}
